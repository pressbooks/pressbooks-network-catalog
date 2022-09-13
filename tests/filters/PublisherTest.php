<?php

namespace Tests\Filters;

use Pressbooks\Book;
use Pressbooks\DataCollector\Book as DataCollector;
use Pressbooks\Metadata;
use PressbooksNetworkCatalog\Filters\Publisher;
use utilsTrait;
use WP_UnitTestCase;

class PublisherTest extends WP_UnitTestCase
{
	use utilsTrait;

	protected DataCollector $collector;

	protected Metadata $metadata;

	public function setUp(): void
	{
		parent::setUp();

		$this->collector = new DataCollector;

		$this->metadata = new Metadata;
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_retrieves_possible_publisher_values(): void
	{
		$this->assertEmpty(
			Publisher::getPossibleValues()
		);

		$this->_book();

		$firstBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Pressbooks'
		);

		$this->_book();

		$secondBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Some Random Publisher'
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$firstBookId
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$secondBookId
		);

		$publishers = Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
			'Some Random Publisher' => 'Some Random Publisher',
		];

		$this->assertNotEmpty($publishers);

		$this->assertEquals($expected, $publishers);
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_caches_publishers_for_subsequent_queries(): void
	{
		$this->assertEmpty(get_transient('pb-network-catalog-publishers'));

		$this->_book();

		$firstBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Pressbooks'
		);

		$this->_book();

		$secondBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Some Random Publisher'
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$firstBookId
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$secondBookId
		);

		Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
			'Some Random Publisher' => 'Some Random Publisher',
		];

		$this->assertNotEmpty(get_transient('pb-network-catalog-publishers'));
		$this->assertEquals($expected, get_transient('pb-network-catalog-publishers'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_publishers_when_there_are_cached_values(): void
	{
		$this->_book();

		$firstBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Pressbooks'
		);

		$this->_book();

		$secondBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Some Random Publisher'
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$firstBookId
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$secondBookId
		);

		Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
			'Some Random Publisher' => 'Some Random Publisher',
		];

		update_post_meta($this->metadata->getMetaPostId(), 'pb_publisher', 'Another Random Publisher');

		$this->collector->copyBookMetaIntoSiteTable($secondBookId);

		$this->assertEquals($expected, Publisher::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_publishers_when_cache_is_cleared(): void
	{
		$this->_book();

		$firstBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Pressbooks'
		);

		$this->_book();

		$secondBookId = get_current_blog_id();

		add_post_meta(
			$this->metadata->getMetaPostId(), 'pb_publisher', 'Some Random Publisher'
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$firstBookId
		);

		$this->collector->copyBookMetaIntoSiteTable(
			$secondBookId
		);

		Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
			'Another Random Publisher' => 'Another Random Publisher',
		];

		update_post_meta($this->metadata->getMetaPostId(), 'pb_publisher', 'Another Random Publisher');

		Book::deleteBookObjectCache();
		delete_transient('pb-network-catalog-publishers');

		$this->collector->copyBookMetaIntoSiteTable($secondBookId);

		$this->assertEquals($expected, Publisher::getPossibleValues());
	}
}
