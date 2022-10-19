<?php

namespace PressbooksNetworkCatalog;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PressbooksNetworkCatalog\Validators\ValidatorFactory;

class BooksRequestManager
{
	/**
	 * Request object handler
	 *
	 * @var Request
	 */
	private Request $request;

	/**
	 * Book fields.
	 *
	 * @var Collection
	 */
	private Collection $bookFields;

	/**
	 * Default of books per page requested.
	 *
	 * @var int
	 */
	private int $defaultPerPage = 10;

	/**
	 * Get parameters allowed to be used in the request.
	 *
	 * @var Collection
	 */
	private Collection $allowedParams;

	/**
	 * @param Collection $bookFields
	 */
	public function __construct(Collection $bookFields)
	{
		$this->bookFields = $bookFields;
		$this->allowedParams = collect([
			'pg' => [
				'type' => 'number',
				'default' => 1,
			],
			'per_page' => [
				'type' => 'number',
				'default' => $this->defaultPerPage,
			],
			'subjects' => [
				'type' => 'array',
				'field' => 'subjects',
			],
			'licenses' => [
				'type' => 'array',
				'field' => 'licenses',
			],
			'institutions' => [
				'type' => 'array',
				'field' => 'institutions',
			],
			'publishers' => [
				'type' => 'array',
				'field' => 'publishers',
			],
			'search' => [
				'type' => 'string',
			],
			'h5p' => [
				'type' => 'flag',
				'field' => 'h5p',
			],
			'from' => [
				'type' => 'date',
				'sqlOperator' => '>=',
				'field' => 'last_updated',
			],
			'to' => [
				'type' => 'date',
				'sqlOperator' => '<=',
				'field' => 'last_updated',
				'greaterThanOrEqualTo' => 'from',
			],
			'sort_by' => [
				'type' => 'array',
				'default' => 'last_updated',
				'allowedValues' => [
					'last_updated' => [
						'field' => 'updatedAt',
						'order' => 'DESC',
					],
					'title' => [
						'field' => 'title',
						'order' => 'ASC',
					],
				],
			],
		]);
		$this->request = Request::capture();
	}

	/**
	 * Validate parameters requests.
	 * This function prevents to pass invalid parameters to the query if any of the parameters is not valid it won't perform the query.
	 * Meaning if any of the validators returns false the query won't be performed.
	 *
	 * @param $params
	 * @return bool
	 */
	public function validateRequest($params): bool
	{
		return $this->allowedParams->map(function ($rules, $key) use ($params) {
			if (empty($this->request->get($key))) {
				$this->request->request->remove($key);

				return true; // Skip parameter validation if param is not present or empty.
			}

			$validator = ValidatorFactory::make($rules['type']);
			$rules = $this->mergeParams($rules, $params);

			return $validator->rules($rules)->validate($this->request->get($key));
		})->doesntContain(false);
	}

	/**
	 * Merge subjects, licenses, institutions and publishers as allowedValues to the rules.
	 * @param $rules
	 * @param $params
	 * @return array|mixed
	 */
	public function mergeParams($rules, $params)
	{
		if (isset($rules['field']) && array_key_exists($rules['field'], $params)) {
			foreach ($params[$rules['field']] as $key => $value) {
				$rules['allowedValues'][$key] = $value;
			}

			return $rules;
		}

		return $rules;
	}

	/**
	 * Get SQL Query Limit and Offset for books catalog query.
	 *
	 * @return string
	 */
	public function getSqlPaginationForCatalogQuery(): string
	{
		return ' LIMIT '.$this->getPerPage().' OFFSET '.$this->getPageOffset();
	}

	public function getPerPage(): int
	{
		return $this->request->per_page ?? $this->defaultPerPage;
	}

	public function getPageOffset(): int
	{
		return $this->request->pg ?
			((int) $this->request->pg - 1) * $this->getPerPage() : 0;
	}

	/**
	 * Get SQL Books Catalog Query conditions according to the request parameters.
	 *
	 * @return string
	 */
	public function getSqlConditionsForCatalogQuery(): string
	{
		if (empty($this->request)) {
			return '';
		}

		$filtearableColumns = $this->bookFields->where('filterable', true);

		$sqlQueryConditions = [];

		global $wpdb;

		$this->allowedParams->each(function ($paramConfig, $filter) use (&$sqlQueryConditions, $wpdb, $filtearableColumns) {
			if (isset($paramConfig['field']) && $this->request->has($filter) && ! empty($this->request->get($filter))) {
				$config = $filtearableColumns->where('filterColumn', $paramConfig['field'])->first();
				if ($config['conditionQueryType']) {
					switch ($config['conditionQueryType']) {
						case 'standard':
							$in_placeholder = array_fill(0, count($this->request->get($filter)), '%s');
							$sqlQueryConditions[] = $config['alias'].
								$wpdb->prepare(' IN ('.implode(', ', $in_placeholder).')', $this->request->get($filter));
							break;
						case 'subquery':
							$in = '';
							foreach ($this->request->get($filter) as $filterValue) {
								$in .= $wpdb->prepare('%s', $filterValue).',';
							}
							$in = rtrim($in, ',');
							$column = $config['column'];
							$sqlQueryConditions[] = " blog_id IN (SELECT blog_id FROM {$wpdb->blogmeta}
                            WHERE meta_key = '$column' AND meta_value IN ($in) GROUP BY blog_id)";
							break;
						case 'date':
							if (isset($paramConfig['sqlOperator'])) {
								$column = $config['alias'];
								$sqlOperator = $paramConfig['sqlOperator'];
								$sqlQueryConditions[] = "DATE($column) $sqlOperator DATE(".
									$wpdb->prepare('%s', $this->request->get($filter)).')';
							}
							break;
						case 'numeric':
							$sqlQueryConditions[] = $config['alias'].' > 0';
							break;
					}
				}
			}
		});
		if (isset($this->request->search) && ! empty($this->request->search)) {
			$sqlQueryConditions[] = $this->getSqlSearchConditionsForCatalogQuery();
		}

		return empty($sqlQueryConditions) ? '' : '  HAVING '.implode(' AND ', $sqlQueryConditions);
	}

	/**
	 * Get SQL Search Conditions for books catalog query.
	 *
	 * @return string
	 */
	private function getSqlSearchConditionsForCatalogQuery(): string
	{
		global $wpdb;
		$searchableColumns = $this->bookFields->where('searchable', true);

		return '('.$searchableColumns->map(function ($field) use ($wpdb) {
			$term = $wpdb->esc_like(strtolower($this->request->search));

			return "LOWER({$field['alias']}) LIKE '%{$term}%'";
		})->implode(' OR ').')';
	}

	/**
	 * Get SQL Query Order By for books catalog query.
	 *
	 * @return string
	 */
	public function getSqlOrderByForCatalogQuery(): string
	{
		if (
			isset($this->request->sort_by) &&
			array_key_exists($this->request->sort_by, $this->allowedParams->get('sort_by')['allowedValues'])
		) {
			$orderBy = $this->allowedParams->get('sort_by')['allowedValues'][$this->request->sort_by];

			return ' ORDER BY '.$orderBy['field'].' '.$orderBy['order'];
		}

		$orderBy = $this->allowedParams->get('sort_by')['allowedValues']['last_updated'];

		return ' ORDER BY '.$orderBy['field'].' '.$orderBy['order'];
	}

	public function getPage(): int
	{
		return $this->request->pg ?? $this->allowedParams->get('pg')['default'];
	}
}
