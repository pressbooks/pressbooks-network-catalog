<?php

namespace Tests;

use PressbooksNetworkCatalog\PressbooksNetworkCatalog;
use WP_UnitTestCase;

class PressbooksNetworkCatalogTest extends WP_UnitTestCase
{
	/**
	 * @test
	 * @group network-catalog
	 */
	public function it_adds_the_network_catalog_filter(): void
	{
		$this->assertFalse(
			has_action('pb_network_catalog')
		);

		PressbooksNetworkCatalog::init();

		$this->assertTrue(
			has_action('pb_network_catalog')
		);
	}
}
