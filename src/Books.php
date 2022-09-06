<?php

namespace  PressbooksNetworkCatalog;

use Illuminate\Support\Collection;
use Pressbooks\DataCollector\Book;
use PressbooksNetworkCatalog\Filters\License;

class Books
{
	private BooksRequestManager $booksRequestManager;

	/**
	 * Fields to be returned from the query.
	 *
	 * @var array
	 */
	private Collection $fields;

	public function __construct()
	{
		global $wpdb;

		$this->fields = Collection::make([
			[
				'column' => 'blog_id',
				'alias' => 'id',
				'selectMethod' => 'b.blog_id',
			],
			[
				'column' => Book::COVER,
				'alias' => 'cover',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
			],
			[
				'column' => Book::BOOK_URL,
				'alias' => 'url',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
			],
			[
				'column' => Book::LONG_DESCRIPTION,
				'alias' => 'description',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
				'searchable' => true,
			],
			[
				'column' => Book::SHORT_DESCRIPTION,
				'alias' => 'shortDescription',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
				'searchable' => true,
			],
			[
				'column' =>Book::TITLE,
				'alias' => 'title',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
				'searchable' => true,
			],
			[
				'column' => Book::SUBJECTS_STRINGS,
				'alias' => 'subjects',
				'selectMethod' => "(SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id)",
				'type' => 'array',
				'conditionQueryType' => 'subquery',
				'filterable' => true,
				'filterColumn' => 'subjects',
				'searchable' => true,
			],
			[
				'column' => Book::AUTHORS,
				'alias' => 'authors',
				'selectMethod' => "(SELECT GROUP_CONCAT(meta_value SEPARATOR ',') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id)",
				'searchable' => true,
			],
			[
				'column' => Book::EDITORS,
				'alias' => 'editors',
				'selectMethod' => "(SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id)",
				'searchable' => true,
			],
			[
				'column' => Book::LICENSE,
				'alias' => 'license',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
				'type' => 'array',
				'conditionQueryType' => 'standard',
				'filterable' => true,
				'filterColumn' => 'licenses',
			],
			[
				'column' => Book::INSTITUTIONS,
				'alias' => 'institutions',
				'selectMethod' => "(SELECT GROUP_CONCAT(meta_value SEPARATOR ',') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id)",
				'type' => 'array',
				'conditionQueryType' => 'subquery',
				'filterable' => true,
				'filterColumn' => 'institutions',
			],
			[
				'column' => Book::PUBLISHER,
				'alias' => 'publisher',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
				'type' => 'array',
				'conditionQueryType' => 'standard',
				'filterable' => true,
				'filterColumn' => 'publishers',
			],
			[
				'column' => Book::H5P_ACTIVITIES,
				'alias' => 'h5pCount',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,CAST(b.meta_value AS UNSIGNED),null))',
				'type' => 'string',
				'conditionQueryType' => 'numeric',
				'filterable' => true,
				'filterColumn' => 'h5p',
			],
			[
				'column' => Book::LAST_EDITED,
				'alias' => 'updatedAt',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,CAST(b.meta_value AS DATETIME),null))',
				'conditionQueryType' => 'date',
				'type' => 'string',
				'regex' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',
				'filterable' => true,
				'filterColumn' => 'last_updated',
			],
			[
				'column' => Book::LANGUAGE,
				'alias' => 'language',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
			],
		]);
	}

	/**
	 * Get Books list to display in the Catalog page.
	 * Valid GET Request parameters with examples are:
	 *  [
	 *      pg (int),
	 *      per_page (int),
	 *      subjects => [ 'ABC', 'LNR' ],
	 *      licenses [ 'public-domain', 'all-rights-reserved' ],
	 *      institutions [ 'Australian National University', 'University of Newcastle' ],
	 *      publishers => [ 'Press Books', 'Univ Pub' ],
	 *      h5p => 'on',
	 *      last_updated => '2019-01-01', // YYYY-MM-DD format
	 *  ]
	 *
	 * @return array
	 */
	public function get(): array
	{
		$this->booksRequestManager = new BooksRequestManager($this->fields);

		return $this->booksRequestManager->validateRequest() ? $this->prepareResponse($this->query()) : [];
	}

	/**
	 * Perform SQL query to get paginated, filtered catalog books according to Request parameters.
	 *
	 * @return array
	 */
	private function query(): array
	{
		global $wpdb;

		$sqlQuery = 'SELECT SQL_CALC_FOUND_ROWS ';
		$sqlQuery .= $this->fields->map(
			function ($field) use ($wpdb) {
				return strpos($field['selectMethod'], '%s') !== false ?
					$wpdb->prepare($field['selectMethod'], $field['column']).' AS '.$field['alias'] :
					$field['selectMethod'].' AS '.$field['alias'];
			}
		)->implode(', ');

		$sqlQuery .= " FROM {$wpdb->blogmeta} b
            WHERE blog_id IN (
                SELECT blog_id FROM {$wpdb->blogmeta}
                    WHERE meta_key = %s AND meta_value = '1'
                )
            GROUP BY blog_id";

		$sqlQuery .= $this->booksRequestManager->getSqlConditionsForCatalogQuery().
			$this->booksRequestManager->getSqlPaginationForCatalogQuery();

		return $wpdb->get_results($wpdb->prepare($sqlQuery, Book::IN_CATALOG));
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

			return $book;
		}, $booksList);
	}
}
