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

		$this->resetSingletonInstance(PressbooksNetworkCatalog::class);

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

		$this->syncBookMetadata($secondId);

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

		$this->syncBookMetadata($secondId);

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
	 * @test
	 * @group request
	 */
	public function it_filters_books_by_institution(): void
	{
		$firstBook = $this->createCatalogBook();

		$this->addInstitutionsToBook($firstBook, [
			'CA-ON-001',
			'CA-ON-002',
		]);

		$secondBook = $this->createCatalogBook();

		$this->addInstitutionsToBook($secondBook, [
			'CA-ON-003',
		]);

		$_GET['institutions'] = [
			'Algoma University',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertTrue($books->contains($firstBook));
		$this->assertFalse($books->contains($secondBook));

		$_GET['institutions'] = [
			'Assumption University',
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
	public function it_filters_books_by_multiple_institutions(): void
	{
		$firstBook = $this->createCatalogBook();

		$this->addInstitutionsToBook($firstBook, [
			'CA-ON-001',
			'CA-ON-002',
		]);

		$secondBook = $this->createCatalogBook();

		$this->addInstitutionsToBook($secondBook, [
			'CA-ON-003',
		]);

		$_GET['institutions'] = [
			'Algoma University',
			'Assumption University',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(2, $books);

		$this->assertTrue($books->containsAll([$firstBook, $secondBook]));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_filters_books_by_publisher(): void
	{
		$firstBook = $this->createCatalogBook();

		$this->addPublisherToBook($firstBook, 'Pressbooks');

		$secondBook = $this->createCatalogBook();

		$this->addPublisherToBook($secondBook, 'Random Publisher');

		$_GET['publishers'] = [
			'Pressbooks',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertTrue($books->contains($firstBook));
		$this->assertFalse($books->contains($secondBook));

		$_GET['publishers'] = [
			'Random Publisher',
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
	public function it_filters_books_by_multiple_publishers(): void
	{
		$firstBook = $this->createCatalogBook();

		$this->addPublisherToBook($firstBook, 'Pressbooks');

		$secondBook = $this->createCatalogBook();

		$this->addPublisherToBook($secondBook, 'Random Publisher');

		$_GET['publishers'] = [
			'Pressbooks',
			'Random Publisher',
		];

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(2, $books);

		$this->assertTrue($books->containsAll([$firstBook, $secondBook]));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_filters_books_that_were_updated_after_a_given_date(): void
	{
		$firstBook = $this->createCatalogBook();
		$firstBookLastUpdated = Carbon::now()->subMonth();

		$this->updateLastUpdated($firstBook, $firstBookLastUpdated);

		$secondBook = $this->createCatalogBook();

		$this->updateLastUpdated($secondBook, Carbon::now()->subMonths(2));

		$_GET['from'] = $firstBookLastUpdated->toDateString();

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertTrue($books->contains($firstBook));
		$this->assertFalse($books->contains($secondBook));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_filters_books_that_were_updated_before_a_given_date(): void
	{
		$firstBook = $this->createCatalogBook();
		$firstBookLastUpdated = Carbon::now()->subMonths(2);

		$this->updateLastUpdated($firstBook, $firstBookLastUpdated);

		$secondBook = $this->createCatalogBook();

		$this->updateLastUpdated($secondBook, Carbon::now()->subMonth());

		$_GET['to'] = $firstBookLastUpdated->toDateString();

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertTrue($books->contains($firstBook));
		$this->assertFalse($books->contains($secondBook));
	}

	/**
	 * @test
	 * @group request
	 */
	public function it_filters_books_that_were_updated_on_a_given_period(): void
	{
		$firstBook = $this->createCatalogBook();
		$firstBookLastUpdated = Carbon::now()->subDays(15);

		$this->updateLastUpdated($firstBook, $firstBookLastUpdated);

		$secondBook = $this->createCatalogBook();

		$this->updateLastUpdated($secondBook, Carbon::now()->subMonths(2));

		$thirdBook = $this->createCatalogBook();

		$this->updateLastUpdated($thirdBook, Carbon::now()->addMonths(2));

		$_GET['from'] = Carbon::now()->subMonth()->toDateString();
		$_GET['to'] = Carbon::now()->addMonth()->toDateString();

		$response = $this->catalogManager->handle();

		$books = collect($response['books'])->map->id;

		$this->assertCount(1, $books);

		$this->assertTrue($books->contains($firstBook));
		$this->assertFalse($books->containsAny([$secondBook, $thirdBook]));
	}

	/**
	 * Create a new book and add it to the catalog
	 *
	 * @param bool $createOpenBook
	 * @return int
	 */
	protected function createCatalogBook(bool $createOpenBook = false): int
	{
		$createOpenBook ? $this->_openTextbook() : $this->_book();

		return tap(get_current_blog_id(), function ($id) {
			update_option(get_in_catalog_option(), 1);

			$this->syncBookMetadata($id);
		});
	}

	/**
	 * Add the list of subjects to the given book
	 *
	 * @param int $id
	 * @param array $subjects
	 * @return void
	 */
	protected function addSubjectsToBook(int $id, array $subjects): void
	{
		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_primary_subject', $subjects['primary'] ?? 'ABA');

		if ($subjects['additional'] ?? false) {
			add_post_meta($meta_id, 'pb_additional_subjects', implode(', ', $subjects['additional']));
		}

		$this->syncBookMetadata($id);
	}

	/**
	 * Add the list institutions to the given book
	 *
	 * @param int $id
	 * @param array $institutions
	 * @return void
	 */
	protected function addInstitutionsToBook(int $id, array $institutions): void
	{
		$meta_id = $this->metadata->getMetaPostId();

		foreach ($institutions as $institution) {
			add_post_meta($meta_id, 'pb_institutions', $institution);
		}

		$this->syncBookMetadata($id);
	}

	/**
	 * Add a publisher to the given book
	 *
	 * @param int $id
	 * @param string $publisher
	 * @return void
	 */
	protected function addPublisherToBook(int $id, string $publisher): void
	{
		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', $publisher
		);

		$this->syncBookMetadata($id);
	}

	/**
	 * Update the last updated date on the given book
	 *
	 * @param int $id
	 * @param Carbon $date
	 * @return void
	 */
	protected function updateLastUpdated(int $id, Carbon $date): void
	{
		update_blog_details($id, [
			'last_updated' => $date->toDateTimeString(),
		]);

		$this->syncBookMetadata($id);
	}

	/**
	 * Sync the book metadata with the wp_blogmeta table.
	 *
	 * @param int $id
	 * @return void
	 */
	protected function syncBookMetadata(int $id): void
	{
		Book::deleteBookObjectCache();

		$this->collector->copyBookMetaIntoSiteTable($id);
	}
}
