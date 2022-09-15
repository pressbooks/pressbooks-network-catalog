<?php

namespace Tests;

use Illuminate\Support\Carbon;
use Pressbooks\Book;
use Pressbooks\DataCollector\Book as DataCollector;
use Pressbooks\Metadata;
use function Pressbooks\Metadata\get_in_catalog_option;
use PressbooksNetworkCatalog\CatalogManager;
use PressbooksNetworkCatalog\PressbooksNetworkCatalog;
use utilsTrait;

class CatalogManagerTest extends TestCase
{
	use utilsTrait;

	protected CatalogManager $catalogManager;

	protected DataCollector $collector;

	protected Metadata $metadata;

	public function setUp(): void
	{
		parent::setUp();

		$this->invalidateSingletonInstance(PressbooksNetworkCatalog::class);

		$this->catalogManager = new CatalogManager;

		$this->collector = new DataCollector;

		$this->metadata = new Metadata;

		PressbooksNetworkCatalog::init();
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_retrieves_books_that_are_in_catalog(): void
	{
		$firstBookId = $this->createCatalogBook();

		$secondBookId = $this->createCatalogBook();

		$thirdBookId = $this->_book();

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(2, $books);

		$this->assertTrue($books->containsAll([$firstBookId, $secondBookId]));
		$this->assertFalse($books->contains($thirdBookId));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_paginates_book_results(): void
	{
		foreach (range(1, 11) as $_) {
			$this->createCatalogBook();
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
			$this->createCatalogBook();
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
		$firstId = $this->createCatalogBook();

		$secondId = $this->createCatalogBook();

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
		$firstId = $this->createCatalogBook();

		$secondId = $this->createCatalogBook();

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
	 * @test
	 * @group request
	 */
	public function it_filters_books_by_license(): void
	{
		$allRightsBook1 = $this->createCatalogBook();

		$allRightsBook2 = $this->createCatalogBook();

		$ccByBook = $this->createCatalogBook($createOpenBook = true);

		$_GET['licenses'] = [
			'all-rights-reserved',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(2, $books);

		$this->assertTrue(
			$books->containsAll([$allRightsBook1, $allRightsBook2])
		);
		$this->assertFalse($books->contains($ccByBook));

		$_GET['licenses'] = [
			'cc-by',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertFalse(
			$books->containsAny([$allRightsBook1, $allRightsBook2])
		);
		$this->assertTrue($books->contains($ccByBook));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_filters_books_by_multiple_licenses(): void
	{
		$allRightsBook1 = $this->createCatalogBook();

		$allRightsBook2 = $this->createCatalogBook();

		$ccByBook = $this->createCatalogBook($createOpenBook = true);

		$_GET['licenses'] = [
			'all-rights-reserved',
			'cc-by',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(3, $books);

		$this->assertTrue(
			$books->containsAll([
				$allRightsBook1,
				$allRightsBook2,
				$ccByBook,
			])
		);
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_filters_books_by_subject(): void
	{
		$firstBook = $this->createCatalogBook();

		$this->addSubjectsToBook($firstBook, [
			'primary' => 'ABA',
			'additional' => ['AVP', 'AVR', 'AVRQ'],
		]);

		$secondBook = $this->createCatalogBook();

		$this->addSubjectsToBook($secondBook, [
			'primary' => 'AB',
			'additional' => ['ABC', 'AF', 'AFCC'],
		]);

		$_GET['subjects'] = [
			'ABA',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertTrue($books->contains($firstBook));
		$this->assertFalse($books->contains($secondBook));

		$_GET['subjects'] = [
			'ABC',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertFalse($books->contains($firstBook));
		$this->assertTrue($books->contains($secondBook));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_filters_books_by_multiple_subjects(): void
	{
		$firstBook = $this->createCatalogBook();

		$this->addSubjectsToBook($firstBook, [
			'primary' => 'ABA',
			'additional' => ['AVP', 'AVR', 'AVRQ'],
		]);

		$secondBook = $this->createCatalogBook();

		$this->addSubjectsToBook($secondBook, [
			'primary' => 'AB',
			'additional' => ['ABC', 'AF', 'AFCC'],
		]);

		$_GET['subjects'] = [
			'ABA',
			'ABC',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(2, $books);

		$this->assertTrue($books->containsAll([$firstBook, $secondBook]));
	}

	/**
	 * Creates a new book and add it to the catalog
	 *
	 * @param bool $createOpenBook
	 * @return int
	 */
	protected function createCatalogBook(bool $createOpenBook = false): int
	{
		$createOpenBook ? $this->_openTextbook() : $this->_book();

		return tap(get_current_blog_id(), function ($id) {
			update_option(get_in_catalog_option(), 1);

			Book::deleteBookObjectCache();

			$this->collector->copyBookMetaIntoSiteTable($id);
		});
	}

	/**
	 * @param int $id
	 * @param string $primary
	 * @param array $additional
	 * @return void
	 */
	protected function addSubjectsToBook(int $id, array $subjects): void
	{
		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_primary_subject', $subjects['primary'] ?? 'ABA');

		if ($subjects['additional'] ?? false) {
			add_post_meta($meta_id, 'pb_additional_subjects', implode(', ', $subjects['additional']));
		}

		Book::deleteBookObjectCache();
		$this->collector->copyBookMetaIntoSiteTable($id);
	}
}
