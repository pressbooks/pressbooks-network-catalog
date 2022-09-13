<?php

namespace Tests\Filters;

use Pressbooks\Book;
use Pressbooks\DataCollector\Book as DataCollector;
use Pressbooks\Metadata;
use PressbooksNetworkCatalog\Filters\Institution;
use utilsTrait;
use WP_UnitTestCase;

class InstitutionTest extends WP_UnitTestCase
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
	public function it_retrieves_possible_institution_values(): void
	{
		$this->assertEmpty(
			Institution::getPossibleValues()
		);

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-001');
		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-002');

		$this->collector->copyBookMetaIntoSiteTable(
			get_current_blog_id()
		);

		$institutions = Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
			'Algonquin College' => 'Algonquin College',
		];

		$this->assertNotEmpty($institutions);

		$this->assertEquals($expected, $institutions);
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_caches_institutions_for_subsequent_queries(): void
	{
		$this->assertEmpty(get_transient('pb-network-catalog-institutions'));

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-001');
		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-002');

		$this->collector->copyBookMetaIntoSiteTable(
			get_current_blog_id()
		);

		Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
			'Algonquin College' => 'Algonquin College',
		];

		$this->assertNotEmpty(get_transient('pb-network-catalog-institutions'));
		$this->assertEquals($expected, get_transient('pb-network-catalog-institutions'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_institutions_when_there_are_cached_values(): void
	{
		$book_id = get_current_blog_id();

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-001');
		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-002');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
			'Algonquin College' => 'Algonquin College',
		];

		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-003');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		$this->assertEquals($expected, Institution::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_institutions_when_cache_is_cleared(): void
	{
		$book_id = get_current_blog_id();

		$meta_id = $this->metadata->getMetaPostId();

		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-001');
		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-002');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		Institution::getPossibleValues();

		$expected = [
			'Algoma University' => 'Algoma University',
			'Algonquin College' => 'Algonquin College',
			'Assumption University' => 'Assumption University',
		];

		add_post_meta($meta_id, 'pb_institutions', 'CA-ON-003');

		Book::deleteBookObjectCache();
		delete_transient('pb-network-catalog-institutions');

		$this->collector->copyBookMetaIntoSiteTable($book_id);

		$this->assertEquals($expected, Institution::getPossibleValues());
	}
}
