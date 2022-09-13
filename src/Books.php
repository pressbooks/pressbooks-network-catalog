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
		if (! $this->booksRequestManager->validateRequest()) {
			return [];
		}
		$this->queryBooks();

		return $this->getPreparedBooks();
	}

	public function getPagination(): array
	{
		if (! $this->booksRequestManager->validateRequest()) {
			return [
                'currentPage' => 1,
                'elements' => [],
                'perPage' => 0,
                'total' => 0,
                'totalPages' => 0,
			];
		}

		$this->queryBooksCount();

        $totalPageCount = $this->getTotalPages();
        $perPage = $this->booksRequestManager->getPerPage();
        $currentPage = $this->booksRequestManager->getPage();
        $elements = $this->getElements($currentPage, $totalPageCount);

		return [
            'currentPage' => $currentPage,
            'elements' => $elements ?? [],
            'perPage' => $perPage,
            'total' => $this->totalBooks,
            'totalPages' => $totalPageCount,
		];
	}

	private function getTotalPages(): int
	{
		return ceil($this->totalBooks / $this->booksRequestManager->getPerPage());
	}

    private function getElements(int $currentPage, int $totalPageCount): array
    {
        $pagesToDisplay = 5;
        $siblingCount = 1;

        if ($pagesToDisplay >= $totalPageCount) {
            return range(1, $totalPageCount);
        }

        $leftSiblingIndex = max($currentPage - $siblingCount, 1);
        $rightSiblingIndex = min($currentPage + $siblingCount, $totalPageCount);

        $shouldShowLeftDots = $leftSiblingIndex > 2;
        $shouldShowRightDots = $rightSiblingIndex < $totalPageCount - 2;

        $firstPageIndex = 1;
        $lastPageIndex = $totalPageCount;

        if ( ! $shouldShowLeftDots && $shouldShowRightDots) {
            $leftItemCount = 2 + 2 * $siblingCount;
            $leftRange = range(1, $leftItemCount);

            return [...$leftRange, '...', $totalPageCount];
        }

        if ($shouldShowLeftDots && ! $shouldShowRightDots) {
            $rightItemCount = 2 + 2 * $siblingCount;
            $rightRange = range($totalPageCount - $rightItemCount + 1, $totalPageCount);

            return [$firstPageIndex, '...', ...$rightRange];
        }

        $middleRange = range($leftSiblingIndex, $rightSiblingIndex);

        return [$firstPageIndex, '...', ...$middleRange, '...', $lastPageIndex];
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
