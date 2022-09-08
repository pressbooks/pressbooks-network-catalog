<?php

namespace PressbooksNetworkCatalog;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
		$this->allowedParams = Collection::make([
			'pg' => [
				'type' => 'string',
				'default' => 1,
				'filter' => FILTER_SANITIZE_STRING,
			],
			'per_page' => [
				'type' => 'string',
				'default' => $this->defaultPerPage,
				'filter' => FILTER_SANITIZE_STRING,
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
				'filter' => FILTER_SANITIZE_STRING,
			],
			'h5p' => [
				'type' => 'string',
				'filter' => FILTER_SANITIZE_STRING,
				'field' => 'h5p',
			],
			'from' => [
				'type' => 'string',
				'regex' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',
				'filter' => FILTER_VALIDATE_REGEXP,
				'sqlOperator' => '>=',
				'field' => 'last_updated',
			],
			'to' => [
				'type' => 'string',
				'regex' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',
				'filter' => FILTER_VALIDATE_REGEXP,
				'sqlOperator' => '<=',
				'field' => 'last_updated',
			],
			'sort_by' => [
				'type' => 'string',
				'filter' => FILTER_SANITIZE_STRING,
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
	 *
	 * @return bool
	 */
	public function validateRequest(): bool
	{
		$valid = true;
		$this->allowedParams->each(function ($config, $filter) use (&$valid) {
			// It could be improved with filter_var_array, see https://www.php.net/manual/es/function.filter-var-array.php
			if ($this->request->get($filter)) {
				if (empty($this->request->get($filter))) {
					$this->request->request->remove($filter);
				} elseif (
					gettype($this->request->get($filter)) !== $config['type'] ||
					(
						isset($config['regex']) &&
						filter_var($this->request->get($filter), $config['filter'], ['options' => ['regexp' => $config['regex']]]) === false
					) ||
					(
						! isset($config['regex']) && isset($config['filter']) &&
						filter_var($this->request->get($filter), $config['filter']) === false
					)
				) {
					$valid = false;

					return false;
				}
			}
		});

		return $valid;
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
			if ($this->request->get($filter) && ! empty($this->request->get($filter)) && isset($paramConfig['field'])) {
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
			return 'LOWER('.$field['alias'].') LIKE '.$wpdb->prepare('%s', '%'.strtolower($this->request->search).'%');
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
		   in_array($this->request->sort_by, array_keys($this->allowedParams->get('sort_by')['allowedValues']))
		) {
			$orderBy = $this->allowedParams->get('sort_by')['allowedValues'][$this->request->sort_by];

			return ' ORDER BY '.$orderBy['field'].' '.$orderBy['order'];
		}

		return '';
	}

	public function getPage(): int
	{
		return $this->request->pg ?? $this->allowedParams->get('pg')['default'];
	}
}
