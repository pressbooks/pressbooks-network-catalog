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
	private int $defaultPerPage = 15;

	/**
	 * @param Collection $bookFields
	 */
	public function __construct(Collection $bookFields)
	{
		$this->bookFields = $bookFields;
		$this->request = Request::capture();
	}

	/**
	 * Validate parameters requests.
	 *
	 * @return bool
	 */
	public function validateRequest(): bool
	{
		$filterableColumns = $this->bookFields->where('filterable', true);
		$valid = true;
		$filterableColumns->each(function ($config) use (&$valid) {
			$filter = $config['filterColumn'];
			if (isset($this->request[$filter])) {
				if (empty($this->request[$filter])) {
					$this->request->request->remove($filter);
				} elseif (
					gettype($this->request[$filter]) !== $config['type'] ||
					(isset($config['regex']) && ! preg_match($config['regex'], $this->request[$filter]))
				) {
					$valid = false;

					return false;
				}
			}
		});

		foreach (['page', 'per_page'] as $param) {
			if (empty($this->request[$param])) {
				$this->request->request->remove($param);
				continue;
			}
			if (isset($this->request[$param]) && ! is_numeric($this->request[$param])) {
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * Get SQL Query Limit and Offset for books catalog query.
	 *
	 * @return string
	 */
	public function getSqlPaginationForCatalogQuery(): string
	{
		return ' LIMIT '.$this->getPageLimit().' OFFSET '.$this->getPageOffset();
	}

	public function getPageLimit(): int
	{
		return $this->request['per_page'] ?? $this->defaultPerPage;
	}

	public function getPageOffset(): int
	{
		$p = $this->request['page'];

		return isset($this->request['page']) ?
			($this->request['page'] - 1) * $this->getPageLimit() : 0;
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

		$filtertableColumns = $this->bookFields->where('filterable', true);

		$sqlQueryConditions = [];

		global $wpdb;

		$filtertableColumns->each(function ($config) use (&$sqlQueryConditions, $wpdb) {
			$filter = $config['filterColumn'];
			if (isset($this->request[$filter]) && ! empty($this->request[$filter])) {
				if (isset($config['conditionQueryType'])) {
					if ($config['conditionQueryType'] === 'standard') {
						$in_placeholder = array_fill(0, count($this->request[$filter]), '%s');
						$sqlQueryConditions[] = $config['alias'].
							$wpdb->prepare(' IN ('.implode(', ', $in_placeholder).')', $this->request[$filter]);
					}
					if ($config['conditionQueryType'] === 'subquery') {
						$in = '';
						foreach ($this->request[$filter] as $filterValue) {
							$in .= $wpdb->prepare('%s', $filterValue).',';
						}
						$in = rtrim($in, ',');
						$column = $config['column'];
						$sqlQueryConditions[] = " blog_id IN (SELECT blog_id FROM {$wpdb->blogmeta}
                            WHERE meta_key = '$column' AND meta_value IN ($in) GROUP BY blog_id)";
					}
					if ($config['conditionQueryType'] === 'date') {
						$column = $config['alias'];
						$sqlQueryConditions[] = "UNIX_TIMESTAMP($column) > UNIX_TIMESTAMP(".
												$wpdb->prepare('%s', $this->request[$filter]).')';
					}
					if ($config['conditionQueryType'] === 'numeric') {
						$sqlQueryConditions[] = $config['alias'].' > 0';
					}
				}
			}
		});
		if (isset($this->request['search']) && ! empty($this->request['search']) && is_string($this->request['search'])) {
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
			return $field['alias'].' LIKE '.$wpdb->prepare('%s', '%'.$this->request['search'].'%');
		})->implode(' OR ').')';
	}
}
