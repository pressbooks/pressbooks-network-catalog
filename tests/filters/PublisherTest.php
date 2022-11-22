<?php

namespace Tests\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Filters\Publisher;
use Tests\TestCase;
use utilsTrait;

class PublisherTest extends TestCase
{
	use utilsTrait;

	/**
	 * @test
	 * @group filters
	 */
	public function it_retrieves_possible_publisher_values(): void
	{
		$this->assertEmpty(
			Publisher::getPossibleValues()
		);

		update_site_meta( 1, DataCollector::PUBLISHER, 'Pressbooks' );
		update_site_meta( 2, DataCollector::PUBLISHER, 'Tolkien Publishers' );

		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );
		update_site_meta( 2, DataCollector::IN_CATALOG, 0 );

		delete_transient('pb-network-catalog-publishers');

		$publishers = Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
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

		update_site_meta( 1, DataCollector::PUBLISHER, 'Pressbooks' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
		];

		update_site_meta( 2, DataCollector::PUBLISHER, 'Tolkien Publishers' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		$this->assertNotEmpty(get_transient('pb-network-catalog-publishers'));

		$this->assertEquals($expected, get_transient('pb-network-catalog-publishers'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_publishers_when_there_are_cached_values(): void
	{
		update_site_meta( 1, DataCollector::PUBLISHER, 'Pressbooks' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
		];

		update_site_meta( 2, DataCollector::PUBLISHER, 'Tolkien Publishers' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		$this->assertEquals($expected, Publisher::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_publishers_when_cache_is_cleared(): void
	{
		update_site_meta( 1, DataCollector::PUBLISHER, 'Pressbooks' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		Publisher::getPossibleValues();

		$expected = [
			'Pressbooks' => 'Pressbooks',
			'Tolkien Publishers' => 'Tolkien Publishers',
		];

		update_site_meta( 2, DataCollector::PUBLISHER, 'Tolkien Publishers' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		delete_transient('pb-network-catalog-publishers');

		$this->assertEquals($expected, Publisher::getPossibleValues());
	}
}
