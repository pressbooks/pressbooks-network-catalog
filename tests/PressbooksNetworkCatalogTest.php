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

	/**
	 * @test
	 * group network-catalog
	 */
	public function it_renders_the_new_catalog_page(): void
	{
		PressbooksNetworkCatalog::init();

		$content = apply_filters('pb_network_catalog', null);

		$this->assertNotEmpty($content);
		$this->assertStringContainsString('<div class="network-catalog">', $content);
	}
}
