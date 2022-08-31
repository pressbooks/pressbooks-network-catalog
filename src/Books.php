<?php

namespace  PressbooksNetworkCatalog;

use Pressbooks\DataCollector\Book;
use PressbooksNetworkCatalog\Filters\License;

class Books
{
	private BooksRequestManager $booksRequestManager;

	/**
	 * Get Books list to display in the Catalog page.
	 * Valid GET Request parameters with examples are:
	 *  [
	 *      page (int),
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
		$this->booksRequestManager = new BooksRequestManager();

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

		$sqlQuery .= $this->booksRequestManager->getSqlConditionsForCatalogQuery().
			$this->booksRequestManager->getSqlPaginationForCatalogQuery();

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
				]
			)
		);
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
					// We want to use the book local language for the subject name in the book card.
					return \Pressbooks\Metadata\get_subject_from_thema($subject_code);
				}, explode(', ', $book->subjects)));
			}

			return $book;
		}, $booksList);
	}
}
