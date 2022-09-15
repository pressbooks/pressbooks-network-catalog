<?php

namespace Tests;

use PressbooksNetworkCatalog\PressbooksNetworkCatalog;

class PressbooksNetworkCatalogTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$this->resetSingletonInstance(PressbooksNetworkCatalog::class);
	}

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
