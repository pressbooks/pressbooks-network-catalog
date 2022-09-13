<?php

namespace Tests\Filters;

use Pressbooks\DataCollector\Book as DataCollector;
use Pressbooks\Metadata;
use PressbooksNetworkCatalog\Filters\License;
use utilsTrait;
use WP_UnitTestCase;

class LicenseTest extends WP_UnitTestCase
{
	use utilsTrait;

	protected DataCollector $collector;

	public function setUp(): void
	{
		parent::setUp();

		$this->_book();

		$this->_openTextbook();

		$this->collector = new DataCollector;
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_retrieves_possible_license_values(): void
	{
		$licenses = License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
			'cc-by' => 'CC BY (Attribution)',
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

		License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
			'cc-by' => 'CC BY (Attribution)',
		];

		$this->assertNotEmpty(get_transient('pb-network-catalog-licenses'));
		$this->assertEquals($expected, get_transient('pb-network-catalog-licenses'));
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_does_not_query_licenses_when_there_are_cached_values(): void
	{
		License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
			'cc-by' => 'CC BY (Attribution)',
		];

		update_post_meta((new Metadata)->getMetaPostId(), 'pb_book_license', 'public-domain');
		update_site_meta(get_current_blog_id(), 'pb_book_license', 'public-domain');

		$this->assertEquals($expected, License::getPossibleValues());
	}

	/**
	 * @test
	 * @group filters
	 */
	public function it_queries_licenses_when_cache_is_cleared(): void
	{
		License::getPossibleValues();

		$expected = [
			'all-rights-reserved' => 'All Rights Reserved',
			'public-domain' => 'Public Domain',
		];

		update_post_meta((new Metadata)->getMetaPostId(), 'pb_book_license', 'public-domain');
		update_site_meta(get_current_blog_id(), 'pb_book_license', 'public-domain');

		delete_transient('pb-network-catalog-licenses');

		$this->assertEquals($expected, License::getPossibleValues());
	}
}
