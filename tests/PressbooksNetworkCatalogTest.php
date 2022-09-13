<?php

namespace Tests;

use PressbooksNetworkCatalog\PressbooksNetworkCatalog;

class PressbooksNetworkCatalogTest extends \WP_UnitTestCase
{
	use \utilsTrait;

	public function setUp(): void
	{
		add_action('admin_init', '\Aldine\Actions\hide_catalog_content_editor');
	}

	/**
	 * @test
	 * @group network-catalog
	 */
	public function it_adds_the_network_catalog_filter(): void
	{
		$this->assertNull(
			$this->filter('pb_network_catalog')
		);

		PressbooksNetworkCatalog::init();

		$this->assertInstanceOf(
			\WP_Hook::class, $this->filter('pb_network_catalog')
		);
	}

	/**
	 * @param string $name
	 * @return \WP_Hook|null
	 */
	protected function filter(string $name): ?\WP_Hook
	{
		global $wp_filter;

		return $wp_filter[$name] ?? null;
	}
}
