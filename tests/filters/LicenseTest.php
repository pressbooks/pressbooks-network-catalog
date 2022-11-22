<?php

namespace Tests\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use PressbooksNetworkCatalog\Filters\License;
use Tests\TestCase;
use utilsTrait;

class LicenseTest extends TestCase
{
	use utilsTrait;

	/**
	 * @test
	 * @group filters
	 */
	public function it_retrieves_possible_license_values(): void
	{
		$this->assertEmpty(
			License::getPossibleValues()
		);

		update_site_meta( 1, DataCollector::LICENSE, 'all-rights-reserved' );
		update_site_meta( 2, DataCollector::LICENSE, 'cc-by' );

		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );
		update_site_meta( 2, DataCollector::IN_CATALOG, 0 );

		delete_transient('pb-network-catalog-licenses');

		$licenses = License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
		];

		$this->assertNotEmpty($licenses);

		$this->assertEquals($expected, $licenses);
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_caches_licenses_for_subsequent_queries(): void
	{
		$this->assertEmpty(get_transient('pb-network-catalog-licenses'));

		update_site_meta( 1, DataCollector::LICENSE, 'all-rights-reserved' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
		];

		update_site_meta( 2, DataCollector::LICENSE, 'cc-by' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		$this->assertNotEmpty(get_transient('pb-network-catalog-licenses'));

		$this->assertEquals($expected, get_transient('pb-network-catalog-licenses'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_licenses_when_there_are_cached_values(): void
	{
		update_site_meta( 1, DataCollector::LICENSE, 'all-rights-reserved' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
		];

		update_site_meta( 2, DataCollector::LICENSE, 'cc-by' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		$this->assertEquals($expected, License::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_licenses_when_cache_is_cleared(): void
	{
		update_site_meta( 1, DataCollector::LICENSE, 'all-rights-reserved' );
		update_site_meta( 1, DataCollector::IN_CATALOG, 1 );

		License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
			'public-domain' => 'Public Domain',
		];

		update_site_meta( 2, DataCollector::LICENSE, 'public-domain' );
		update_site_meta( 2, DataCollector::IN_CATALOG, 1 );

		delete_transient('pb-network-catalog-licenses');

		$this->assertEquals($expected, License::getPossibleValues());
	}
}
