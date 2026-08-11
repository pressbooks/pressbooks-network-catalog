<?php

namespace PressbooksNetworkCatalog;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
    public function __construct(Collection $bookFields, Request $request = null)
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
            'search_term' => [
                'type' => 'string',
            ],
            'h5p' => [
                'type' => 'flag',
                'field' => 'h5p',
            ],
            'date_field' => [
                'type' => 'array',
                'default' => 'last_updated',
                'allowedValues' => [
                    'last_updated' => ['field' => 'last_updated'],
                    'publication_date' => ['field' => 'publication_date'],
                ],
            ],
            'published_from' => [
                'type' => 'date',
                'sqlOperator' => '>=',
                'field' => 'publication_date',
            ],
            'published_to' => [
                'type' => 'date',
                'sqlOperator' => '<=',
                'field' => 'publication_date',
                'greaterThanOrEqualTo' => 'published_from',
            ],
            'updated_from' => [
                'type' => 'date',
                'sqlOperator' => '>=',
                'field' => 'last_updated',
            ],
            'updated_to' => [
                'type' => 'date',
                'sqlOperator' => '<=',
                'field' => 'last_updated',
                'greaterThanOrEqualTo' => 'updated_from',
            ],
            'sort_by' => [
                'type' => 'array',
                'default' => 'last_updated',
                'allowedValues' => [
                    'last_updated' => [
                        // ORDER BY uses the SELECT alias from Books (camelCase)
                        'field' => 'updatedAt',
                        'order' => 'DESC',
                    ],
                    'title' => [
                        'field' => 'title',
                        'order' => 'ASC',
                    ],
                    'publication_date' => [
                        // ORDER BY uses the SELECT alias from Books (camelCase)
                        'field' => 'publicationDate',
                        'order' => 'DESC',
                    ],
                ],
            ],
        ]);
        $this->request = $request ?? Request::capture();
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
            if (! $this->request->has($key) || empty($this->request->get($key))) {
                return true; // skip if not present
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
    public function mergeParams($rules, $params): mixed
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
        return (int) ($this->request->get('per_page', $this->defaultPerPage));
    }

    public function getPage(): int
    {
        return (int) ($this->request->get('pg', 1));
    }

    public function getPageOffset(): int
    {
        return ($this->getPage() - 1) * $this->getPerPage();
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

        $filterableColumns = $this->bookFields->where('filterable', true);

        $sqlQueryConditions = [];

        global $wpdb;

        $this->allowedParams->each(function ($paramConfig, $filter) use (&$sqlQueryConditions, $wpdb, $filterableColumns) {
            if (isset($paramConfig['field']) && $this->request->has($filter) && ! empty($this->request->get($filter))) {
                $config = $filterableColumns->where('filterColumn', $paramConfig['field'])->first();
                if (! $config) {
                    return;
                }
                if ($config['conditionQueryType']) {
                    switch ($config['conditionQueryType']) {
                        case 'standard':
                            $in_placeholder = array_fill(0, count($this->request->get($filter)), '%s');
                            $sqlQueryConditions[] = $config['alias'].
                                $wpdb->prepare(' IN ('.implode(', ', $in_placeholder).')', $this->request->get($filter));
                            break;
                        case 'subquery':
                            $values = $this->request->get($filter);
                            $placeholders = implode(', ', array_fill(0, count($values), '%s'));
                            $column = $config['column'];
                            // Prepare the meta_value IN (...) safely
                            $inCondition = $wpdb->prepare("meta_value IN ($placeholders)", ...$values);
                            $sqlQueryConditions[] = " blog_id IN (SELECT blog_id FROM {$wpdb->blogmeta}
	                            WHERE meta_key = '$column' AND {$inCondition} GROUP BY blog_id)";
                            break;
                        case 'date':
                            if (isset($paramConfig['sqlOperator'])) {
                                $dateField = $paramConfig['field'] ?? 'updated_at';
                                $selectedConfig = $filterableColumns->where('filterColumn', $dateField)->first();
                                $column = $selectedConfig['alias'] ?? $config['alias'];
                                $sqlOperator = $paramConfig['sqlOperator'];

                                $dateValue = $this->request->get($filter);
                                $date = Carbon::parse($dateValue);

                                if ($sqlOperator === '>=') {
                                    $date = $date->startOfDay();
                                } elseif ($sqlOperator === '<=') {
                                    $date = $date->endOfDay();
                                }

                                // Compare DATE() of the column to a YYYY-MM-DD string for both publication_date (which uses FROM_UNIXTIME) and and updated_at.
                                $dateString = $date->toDateString();
                                $sqlQueryConditions[] = "DATE($column) $sqlOperator ".$wpdb->prepare('%s', $dateString);
                            }
                            break;
                        case 'numeric':
                            $sqlQueryConditions[] = $config['alias'].' > 0';
                            break;
                    }
                }
            }
        });
        if (isset($this->request->search_term) && ! empty($this->request->search_term)) {
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
            $term = $wpdb->esc_like(strtolower($this->request->search_term));

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
}
