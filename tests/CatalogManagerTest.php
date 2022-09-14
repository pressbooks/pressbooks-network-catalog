<?php

namespace Tests;

use Pressbooks\Book;
use Pressbooks\DataCollector\Book as DataCollector;
use function Pressbooks\Metadata\get_in_catalog_option;
use PressbooksNetworkCatalog\CatalogManager;
use PressbooksNetworkCatalog\PressbooksNetworkCatalog;
use utilsTrait;

class CatalogManagerTest extends TestCase
{
	use utilsTrait;

	public function setUp(): void
	{
		parent::setUp();

		$this->invalidateSingletonInstance(PressbooksNetworkCatalog::class);
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_retrieves_a_list_of_books_that_are_in_catalog(): void
	{
		PressbooksNetworkCatalog::init();

		$firstBookId = $this->createBookInCatalog();

		$secondBookId = $this->createBookInCatalog();

		$thirdBookId = $this->_book();

		$response = (new CatalogManager)->handle();

		$books = collect($response['books']);

		$this->assertCount(2, $books);

		$this->assertTrue($books->contains('id', $firstBookId));
		$this->assertTrue($books->contains('id', $secondBookId));
		$this->assertFalse($books->contains('id', $thirdBookId));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_paginates_results(): void
	{
		PressbooksNetworkCatalog::init();

		foreach (range(1, 11) as $_) {
			$this->createBookInCatalog();
		}

		$response = (new CatalogManager)->handle();

		$books = collect($response['books']);
		$pagination = $response['pagination'];

		$this->assertCount(10, $books);

		$this->assertEquals([
			'currentPage' => 1,
			'elements' => [1, 2],
			'perPage' => 10,
			'total' => 11,
			'totalPages' => 2,
		], $pagination);
	}

	/**
	 * Creates a book
	 * @return void
	 */
	protected function createBookInCatalog(): int
	{
		$this->_book();

		return tap(get_current_blog_id(), function ($id) {
			update_option(get_in_catalog_option(), 1);

			Book::deleteBookObjectCache();

			(new DataCollector)->copyBookMetaIntoSiteTable($id);
		});
	}
}
