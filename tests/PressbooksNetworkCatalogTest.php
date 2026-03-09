<?php

namespace Tests;

use Pressbooks\DataCollector\Book;
use function Pressbooks\Metadata\get_in_catalog_option;
use PressbooksNetworkCatalog\PressbooksNetworkCatalog;
use utilsTrait;

class PressbooksNetworkCatalogTest extends TestCase
{
	use utilsTrait;

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

	/**
	 * @test
	 * @group network-catalog
	 */
	public function it_removes_book_from_catalog_on_blog_deactivation(): void
	{
		$this->_book();

		$blogId = get_current_blog_id();

		update_option(get_in_catalog_option(), 1);

		restore_current_blog();

		update_site_meta($blogId, Book::IN_CATALOG, 1);

		PressbooksNetworkCatalog::init();

		do_action('deactivate_blog', $blogId);

		switch_to_blog($blogId);
		$optionValue = get_option(get_in_catalog_option());
		restore_current_blog();

		$this->assertEquals(0, $optionValue);
		$this->assertEquals(0, get_site_meta($blogId, Book::IN_CATALOG, true));
	}
}
