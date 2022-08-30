<?php

namespace  PressbooksNetworkCatalog;

use Pressbooks\DataCollector\Book;
use PressbooksNetworkCatalog\Filters\License;

class Books
{
	private int $defaultBooksPerPage = 15;

	/**
	 * Get Books list to display in the Catalog page.
	 * Valid parameters with examples are:
	 * [
	 *  page (int),
	 *  per_page (int),
	 *  subjects => [ 'ABC', 'LNR' ],
	 *  licenses [ 'public-domain', 'all-rights-reserved' ],
	 *  institutions [ 'Australian National University', 'University of Newcastle' ],
	 *  publishers => [ 'Press Books', 'Univ Pub' ],
	 *  h5p => true / false, // boolean
	 *  last_updated => '2019-01-01', // YYYY-MM-DD format
	 * ]
	 *
	 * @param array $params Parameters to filter the list of books.
	 *
	 * @return array
	 */
	public function get(array $params = []): array
	{
		return $this->validateParams($params) ? $this->prepareResponse($this->query($params)) : [];
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
		// We want to display different messages depending on the type of validation error.
		$numericParams = ['page', 'per_page'];
		foreach ($numericParams as $param) {
			if (isset($params[$param]) && ! is_numeric($params[$param])) {
				return false;
			}
		}

		$arrayParams = ['subjects', 'licenses', 'institutions', 'publishers'];
		foreach ($arrayParams as $param) {
			if (isset($params[$param]) && ! is_array($params[$param])) {
				return false;
			}
		}

		if (isset($params['h5p']) && ! is_bool($params['h5p'])) {
			return false;
		}

		if (isset($params['last_updated'])) {
			if (
				! is_string($params['last_updated']) ||
				! preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $params['last_updated']) ||
				time() < strtotime($params['last_updated'])
			) {
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
            (SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id) AS institutions,
            (SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id) AS authors,
            (SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id) AS editors,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS publisher,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS description,
            MAX(IF(b.meta_key=%s,CAST(b.meta_value AS DATETIME),null)) AS updatedAt,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS language,
            (SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id) AS subjects,
            MAX(IF(b.meta_key=%s,b.meta_value,null)) AS license,
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
					Book::INSTITUTIONS,
					Book::AUTHORS,
					Book::EDITORS,
					Book::PUBLISHER,
					Book::LONG_DESCRIPTION,
					Book::LAST_EDITED,
					Book::LANGUAGE,
					Book::SUBJECTS_CODES,
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
		$sqlQueryConditions = [];

		global $wpdb;

		$queryFilters = [
			'licenses' => 'license',
			'publishers' => 'publisher',
		];
		foreach ($queryFilters as $filter => $column) {
			if (isset($params[$filter]) && ! empty($params[$filter])) {
				$in = '';
				foreach ($params[$filter] as $filterValue) {
					$in .= $wpdb->prepare('%s', $filterValue).',';
				}
				$in = rtrim($in, ',');

				$sqlQueryConditions[] = " $column IN ($in)";
			}
		}

		$subQueryFilters = [
			'subjects' => Book::SUBJECTS_CODES,
			'institutions' => Book::INSTITUTIONS,
		];
		foreach ($subQueryFilters as $filter => $column) {
			if (isset($params[$filter]) && ! empty($params[$filter])) {
				$in = '';
				foreach ($params[$filter] as $filterValue) {
					$in .= $wpdb->prepare('%s', $filterValue).',';
				}
				$in = rtrim($in, ',');
				$sqlQueryConditions[] = " blog_id IN (SELECT blog_id FROM {$wpdb->blogmeta}
                WHERE meta_key = '$column' AND meta_value IN ($in) GROUP BY blog_id)";
			}
		}

		if (isset($params['h5p'])) {
			$sqlQueryConditions[] = $params['h5p'] ? ' h5pCount > 0' : 'h5pCount = 0';
		}

		if (isset($params['search'])) {
			$sqlQueryConditions[] = ' title LIKE '.$wpdb->prepare('%s', '%'.$params['search'].'%');
		}

		if (isset($params['last_updated'])) {
			$sqlQueryConditions[] = ' UNIX_TIMESTAMP(updatedAt) > UNIX_TIMESTAMP('.
				$wpdb->prepare('%s', $params['last_updated']).')';
		}

		return empty($sqlQueryConditions) ? '' : '  HAVING '.implode('AND', $sqlQueryConditions);
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
			$book->license = $possibleLicenses[$book->license] ?? '';
			if ($book->subjects) {
				$book->subjects = implode(', ', array_map(function ($subject_code) {
					// We want to use the main site language here
					return \Pressbooks\Metadata\get_subject_from_thema($subject_code);
				}, explode(', ', $book->subjects)));
			}

			return $book;
		}, $booksList);
	}
}
