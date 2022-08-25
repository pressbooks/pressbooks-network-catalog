<?php

namespace  PressbooksNetworkCatalog;

use Pressbooks\DataCollector\Book;
use PressbooksNetworkCatalog\Filters\License;

class Books
{
	private int $defaultBooksPerPage = 15;

	/**
	 * Get Books list to display in the Catalog page.
	 *
	 * @param array $params Valid keys: page (int), per_page (int), subjects (array), licenses (array), institutions (array), publishers (array), h5p_count (array symb => int)
	 *
	 * @return array
	 */
	public function get(array $params = []): array
	{
		if (! $this->validateParams($params)) {
			return [];
		}

		return $this->prepareResponse($this->query($params));
	}

	/**
	 * Validate filter parameters.
	 *
	 * @param array $params
	 *
	 * @return bool
	 */
	private function validateParams(array $params): bool
	{
		$numericParams = ['page', 'per_page'];
		foreach ($numericParams as $param) {
			if (isset($params[$param]) && ! is_numeric($params[$param])) {
				return false;
			}
		}

		$arrayParams = ['subjects', 'licenses', 'institutions', 'publishers', 'h5p_count'];
		foreach ($arrayParams as $param) {
			if (isset($params[$param]) && ! is_array($params[$param])) {
				return false;
			}
		}

		if (isset($params['h5p_count'])) {
			$symbol = array_key_first($params['h5p_count']);
			if (! in_array($symbol, ['>=', '<=']) || ! is_int($params['h5p_count'][$symbol])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Perform SQL query to get paginated, filtered catalog books.
	 *
	 * @param array $params
	 * @return array
	 */
	private function query(array $params = [])
	{
		$limit = $params['per_page'] ?? $this->defaultBooksPerPage;
		$offset = isset($params['page']) ? ($params['page'] - 1) * $limit : 0;

		global $wpdb;

		$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS
            b.blog_id AS id,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS cover,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS title,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS url,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS informationArray,
            MAX(IF(b.meta_key=%s,CAST(b.meta_value AS DATETIME),null)) AS updatedAt,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS language,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS subjects,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS licenses,
            MAX(IF(b.meta_key=%s,CAST(b.meta_value AS UNSIGNED),null)) AS h5pCount
        FROM {$wpdb->blogmeta} b
        WHERE blog_id IN (
            SELECT blog_id FROM {$wpdb->blogmeta}
                WHERE meta_key = %s AND meta_value = '1'
            )
        GROUP BY blog_id";

		$sqlQuery .= $this->getQueryConditionsByParams($params);
		$sqlQuery .= ' LIMIT %d OFFSET %d';

		return $wpdb->get_results(
			$wpdb->prepare(
				$sqlQuery,
				[
					Book::COVER,
					Book::TITLE,
					Book::BOOK_URL,
					Book::BOOK_INFORMATION_ARRAY,
					Book::LAST_EDITED,
					Book::LANGUAGE,
					Book::SUBJECT,
					Book::LICENSE,
					Book::H5P_ACTIVITIES,
					Book::IN_CATALOG,
					$limit,
					$offset,
				]
			)
		);
	}

	/**
	 * Get SQL query conditions by filter parameters.
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	private function getQueryConditionsByParams(array $params): string
	{
		if (empty($params)) {
			return '';
		}
		$sqlQueryConditions = '';
		$arrayFormatParams = [
			'subjects',
			'licenses',
		];
		foreach ($arrayFormatParams as $filter) {
			if (isset($params[$filter]) && ! empty($params[$filter])) {
				global $wpdb;
				$in = '';
				foreach ($params[$filter] as $filterValue) {
					$in .= $wpdb->prepare('%s', $filterValue).',';
				}
				$in = str_replace(',', '', $in);

				$sqlQueryConditions .= " $filter IN ($in)";
			}
		}

		if (isset($params['h5p_count']) && ! empty($params['h5p_count'])) {
			global $wpdb;

			$symbol = array_key_first($params['h5p_count']);
			$sqlQueryConditions .= $wpdb->prepare(" h5pCount $symbol %d", $params['h5p_count'][$symbol]);
		}

		return empty($sqlQueryConditions) ? '' : '  HAVING '.$sqlQueryConditions;
	}

	/**
	 * Prepare books list response.
	 *
	 * @param array $booksList raw books list
	 *
	 * @return array
	 */
	private function prepareResponse(array $booksList): array
	{
		$possibleLicenses = License::getPossibleValues();

		return array_map(function ($book) use ($possibleLicenses) {
			$bookInformation = unserialize($book->informationArray);
			$book->authors = $bookInformation['pb_authors'] ?? '';
			$book->editors = $bookInformation['pb_editors'] ?? '';
			$book->description = $bookInformation['pb_about_unlimited'] ?? '';
			$book->institutions = isset($bookInformation['pb_institutions']) ?
				implode(',', $bookInformation['pb_institutions']) : '';
			$book->publisher = $bookInformation['pb_publisher'] ?? '';
			$book->license = $possibleLicenses[$book->licenses] ?? '';

			return $book;
		}, $booksList);
	}
}
