<?php

namespace Tests;

use Illuminate\Support\Carbon;
use Pressbooks\Book;
use Pressbooks\DataCollector\Book as DataCollector;
use function Pressbooks\Metadata\get_in_catalog_option;
use PressbooksNetworkCatalog\CatalogManager;
use PressbooksNetworkCatalog\PressbooksNetworkCatalog;
use utilsTrait;

class CatalogManagerTest extends TestCase
{
	use utilsTrait;

	protected CatalogManager $catalogManager;

	protected DataCollector $collector;

	public function setUp(): void
	{
		parent::setUp();

		$this->invalidateSingletonInstance(PressbooksNetworkCatalog::class);

		$this->catalogManager = new CatalogManager;

		$this->collector = new DataCollector;

		PressbooksNetworkCatalog::init();
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_retrieves_books_that_are_in_catalog(): void
	{
		$firstBookId = $this->createBookInCatalog();

		$secondBookId = $this->createBookInCatalog();

		$thirdBookId = $this->_book();

		$response = $this->catalogManager->handle();

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
	public function it_paginates_book_results(): void
	{
		foreach (range(1, 11) as $_) {
			$this->createBookInCatalog();
		}

		$response = $this->catalogManager->handle();

		$this->assertCount(10, $response['books']);

		$this->assertEquals([
			'currentPage' => 1,
			'elements' => [1, 2],
			'perPage' => 10,
			'total' => 11,
			'totalPages' => 2,
		], $response['pagination']);

		$_GET['pg'] = '2'; // this should be allowed to be either integer or string;

		$response = $this->catalogManager->handle();

		$this->assertCount(1, $response['books']);

		$this->assertEquals([
			'currentPage' => 2,
			'elements' => [1, 2],
			'perPage' => 10,
			'total' => 11,
			'totalPages' => 2,
		], $response['pagination']);
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_allows_changing_the_amount_of_books_per_page(): void
	{
		foreach (range(1, 11) as $_) {
			$this->createBookInCatalog();
		}

		$_GET['per_page'] = '20'; // this should be allowed to be either integer or string

		$response = $this->catalogManager->handle();

		$this->assertCount(11, $response['books']);

		$this->assertEquals([
			'currentPage' => 1,
			'elements' => [1],
			'perPage' => 20,
			'total' => 11,
			'totalPages' => 1,
		], $response['pagination']);
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_sorts_books_by_last_updated_by_default(): void
	{
		$firstId = $this->createBookInCatalog();

		$secondId = $this->createBookInCatalog();

		update_blog_details($secondId, [
			'last_updated' => Carbon::now()->addMinutes(10)->toDateTimeString(),
		]);

		Book::deleteBookObjectCache();

		$this->collector->copyBookMetaIntoSiteTable($secondId);

		$response = $this->catalogManager->handle();

		$this->assertEquals($secondId, $response['books'][0]->id);
		$this->assertEquals($firstId, $response['books'][1]->id);
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_allows_changing_the_sort_order_of_books(): void
	{
		$firstId = $this->createBookInCatalog();

		$secondId = $this->createBookInCatalog();

		update_blog_details($secondId, [
			'last_updated' => Carbon::now()->addMinutes(10)->toDateTimeString(),
		]);

		Book::deleteBookObjectCache();

		$this->collector->copyBookMetaIntoSiteTable($secondId);

		$_GET['sort_by'] = 'title';

		$response = $this->catalogManager->handle();

		$this->assertEquals($firstId, $response['books'][0]->id);
		$this->assertEquals($secondId, $response['books'][1]->id);
	}

	/**
	 * Creates a new book and add it to the catalog
	 *
	 * @return int
	 */
	protected function createBookInCatalog(): int
	{
		$this->_book();

		return tap(get_current_blog_id(), function ($id) {
			update_option(get_in_catalog_option(), 1);

			Book::deleteBookObjectCache();

			$this->collector->copyBookMetaIntoSiteTable($id);
		});
	}
}
