<?php

namespace Tests\Filters;

use Pressbooks\Book;
use Pressbooks\DataCollector\Book as DataCollector;
use Pressbooks\Metadata;
use PressbooksNetworkCatalog\Filters\Subject;
use Tests\TestCase;
use utilsTrait;

class SubjectTest extends TestCase
{
	use utilsTrait;

	protected DataCollector $collector;

	protected Metadata $metadata;

	public function setUp(): void
	{
		parent::setUp();

		$this->_book();

		$this->collector = new DataCollector;

		$this->metadata = new Metadata;
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_retrieves_possible_subject_values(): void
	{
		$this->assertEmpty(
			Subject::getPossibleValues()
		);

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_primary_subject', 'ABA');
		add_post_meta($meta_id, 'pb_additional_subjects', 'AVP, AVR, AVRQ');

		$this->collector->copyBookMetaIntoSiteTable(
			get_current_blog_id()
		);

		delete_transient('pb-network-catalog-subjects');

		$subjects = Subject::getPossibleValues();

		$expected = [
			'AVRQ' => 'Mechanical musical instruments',
			'AVR' => 'Musical instruments',
			'AVP' => 'Musicians, singers, bands and groups',
			'ABA' => 'Theory of art',
		];

		$this->assertNotEmpty($subjects);

		$this->assertEquals($expected, $subjects);
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_caches_subjects_for_subsequent_queries(): void
	{
		$this->assertEmpty(get_transient('pb-network-catalog-subjects'));

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_primary_subject', 'ABA');
		add_post_meta($meta_id, 'pb_additional_subjects', 'AVP, AVR, AVRQ');

		$this->collector->copyBookMetaIntoSiteTable(
			get_current_blog_id()
		);

		Subject::getPossibleValues();

		$expected = [
			'AVRQ' => 'Mechanical musical instruments',
			'AVR' => 'Musical instruments',
			'AVP' => 'Musicians, singers, bands and groups',
			'ABA' => 'Theory of art',
		];

		$this->assertNotEmpty(get_transient('pb-network-catalog-subjects'));
		$this->assertEquals($expected, get_transient('pb-network-catalog-subjects'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_subjects_when_there_are_cached_values(): void
	{
		$book_id = get_current_blog_id();

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_primary_subject', 'ABA');
		add_post_meta($meta_id, 'pb_additional_subjects', 'AVP, AVR, AVRQ');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		Subject::getPossibleValues();

		$expected = [
			'AVRQ' => 'Mechanical musical instruments',
			'AVR' => 'Musical instruments',
			'AVP' => 'Musicians, singers, bands and groups',
			'ABA' => 'Theory of art',
		];

		update_post_meta($meta_id, 'pb_primary_subject', 'AB');
		update_post_meta($meta_id, 'pb_additional_subjects', 'ABC, AF, AFCC');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		$this->assertEquals($expected, Subject::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_subjects_when_cache_is_cleared(): void
	{
		$book_id = get_current_blog_id();

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_primary_subject', 'ABA');
		add_post_meta($meta_id, 'pb_additional_subjects', 'AVP, AVR, AVRQ');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		Subject::getPossibleValues();

		$expected = [
			'ABC' => 'Conservation, restoration and care of artworks',
			'AFCC' => 'Paintings and painting in watercolours or pastels',
			'AF' => 'The Arts: art forms',
			'AB' => 'The arts: general topics',
		];

		update_post_meta($meta_id, 'pb_primary_subject', 'AB');
		update_post_meta($meta_id, 'pb_additional_subjects', 'ABC, AF, AFCC');

		Book::deleteBookObjectCache();
		delete_transient('pb-network-catalog-subjects');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		$this->assertEquals($expected, Subject::getPossibleValues());
	}
}
