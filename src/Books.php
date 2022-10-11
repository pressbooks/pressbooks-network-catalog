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

	/**
	 * Total number of paginated books in the DB.
	 *
	 * @var int
	 */
	private int $totalBooks = 0;

	/**
	 * List books requested.
	 *
	 * @var array
	 */
	private array $books = [];

	/**
	 * Main filters to be used in the query.
	 *
	 * @var array
	 */
	private array $filters = [];

	public function __construct($filters = [])
	{
		global $wpdb;

		$this->filters = $filters;

		$this->fields = collect([
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
				'conditionQueryType' => 'subquery',
				'searchable' => true,
			],
			[
				'column' => Book::SUBJECTS_CODES,
				'alias' => 'subjectsCodes',
				'selectMethod' => "(SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id)",
				'conditionQueryType' => 'subquery',
				'filterable' => true,
				'filterColumn' => 'subjects',
			],
			[
				'column' => Book::AUTHORS,
				'alias' => 'authors',
				'selectMethod' => "(SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id)",
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
				'conditionQueryType' => 'standard',
				'filterable' => true,
				'filterColumn' => 'licenses',
			],
			[
				'column' => Book::INSTITUTIONS,
				'alias' => 'institutions',
				'selectMethod' => "(SELECT GROUP_CONCAT(meta_value SEPARATOR ', ') FROM {$wpdb->blogmeta} WHERE meta_key=%s AND blog_id = id)",
				'conditionQueryType' => 'subquery',
				'filterable' => true,
				'filterColumn' => 'institutions',
			],
			[
				'column' => Book::PUBLISHER,
				'alias' => 'publisher',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
				'conditionQueryType' => 'standard',
				'filterable' => true,
				'filterColumn' => 'publishers',
			],
			[
				'column' => Book::H5P_ACTIVITIES,
				'alias' => 'h5pCount',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,CAST(b.meta_value AS UNSIGNED),null))',
				'conditionQueryType' => 'numeric',
				'filterable' => true,
				'filterColumn' => 'h5p',
			],
			[
				'column' => Book::LAST_EDITED,
				'alias' => 'updatedAt',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,CAST(b.meta_value AS DATETIME),null))',
				'conditionQueryType' => 'date',
				'filterable' => true,
				'filterColumn' => 'last_updated',
			],
			[
				'column' => Book::LANGUAGE,
				'alias' => 'language',
				'selectMethod' => 'MAX(IF(b.meta_key=%s,b.meta_value,null))',
			],
		]);

		$this->booksRequestManager = new BooksRequestManager($this->fields);
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
	 *      from => '2019-01-01', // YYYY-MM-DD format
	 *      to => '2020-05-20', // YYYY-MM-DD format
	 *  ]
	 *
	 * @return array
	 */
	public function get(): array
	{
		if (! $this->booksRequestManager->validateRequest($this->filters)) {
			return [];
		}
		$this->queryBooks();

		return $this->getPreparedBooks();
	}

	public function catalogHasBooks(): bool
	{
		global $wpdb;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(DISTINCT blog_id) FROM $wpdb->blogmeta WHERE blog_id IN (SELECT blog_id FROM $wpdb->blogmeta WHERE meta_key = %s AND meta_value = 1)",
				Book::IN_CATALOG
			)
		);

		return (int) $count > 0;
	}

	public function getPagination(): array
	{
		if (! $this->booksRequestManager->validateRequest($this->filters)) {
			return [
				'currentPage' => 1,
				'previousPage' => 1,
				'nextPage' => 1,
				'total' => 0,
				'totalPages' => 1,
			];
		}

		$this->queryBooksCount();

		$pageCount = $this->getTotalPages();
		$currentPage = $this->booksRequestManager->getPage();

		return [
			'currentPage' => $currentPage,
			'previousPage' => max($currentPage - 1, 1),
			'nextPage' => min($currentPage + 1, $pageCount),
			'total' => $this->totalBooks,
			'totalPages' => $pageCount,
		];
	}

	private function getTotalPages(): int
	{
		return ceil($this->totalBooks / $this->booksRequestManager->getPerPage());
	}

	/**
	 * Query books count according to the request parameters.
	 *
	 * @return void
	 */
	private function queryBooksCount(): void
	{
		global $wpdb;

		$sqlQueryFields = $this->getSqlQueryFields();
		$sqlQuery = $this->getSqlFromQuery();
		$sqlQueryConditions = $this->booksRequestManager->getSqlConditionsForCatalogQuery();

		$this->totalBooks = (int) $wpdb->get_var("SELECT COUNT(DISTINCT id) FROM ({$sqlQueryFields} {$sqlQuery} {$sqlQueryConditions}) AS t");
	}

	private function getSqlFromQuery(): string
	{
		global $wpdb;

		return $wpdb->prepare(" FROM {$wpdb->blogmeta} b
            WHERE blog_id IN (
                SELECT blog_id FROM {$wpdb->blogmeta}
                    WHERE meta_key = %s AND meta_value = '1'
                )
            GROUP BY blog_id", Book::IN_CATALOG);
	}

	/**
	 * Perform SQL query to get paginated, filtered catalog books according to Request parameters.
	 *
	 * @return void
	 */
	private function queryBooks(): void
	{
		global $wpdb;

		$sqlBooksQuery = $this->getSqlQueryFields().$this->getSqlFromQuery().
			$this->booksRequestManager->getSqlConditionsForCatalogQuery().
			$this->booksRequestManager->getSqlOrderByForCatalogQuery().
			$this->booksRequestManager->getSqlPaginationForCatalogQuery();

		$this->books = $wpdb->get_results($sqlBooksQuery);
	}

	/**
	 * Get SQL query fields.
	 *
	 * @return string
	 */
	private function getSqlQueryFields(): string
	{
		global $wpdb;

		return 'SELECT '.$this->fields->map(
			function ($field) use ($wpdb) {
				return strpos($field['selectMethod'], '%s') !== false ?
					$wpdb->prepare($field['selectMethod'], $field['column']).' AS '.$field['alias'] :
					$field['selectMethod'].' AS '.$field['alias'];
			}
		)->implode(', ');
	}

	/**
	 * Prepare books list response.
	 *
	 * @return array
	 */
	private function getPreparedBooks(): array
	{
		$possibleLicenses = License::getPossibleValues();

		return array_map(function ($book) use ($possibleLicenses) {
			$book->license = $possibleLicenses[$book->license] ?? '';

			return $book;
		}, $this->books);
	}
}
