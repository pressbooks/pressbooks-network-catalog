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
	 * pressbooks-aldine's PSR-4 migration (theme PR #588) changes
	 * '\Aldine\Actions\hide_catalog_content_editor' (a plain function-string
	 * admin_init callback) into [\Aldine\Actions::class, 'hideCatalogContentEditor']
	 * (an array-callable static method). remove_action() matches by exact
	 * callback identity, so both forms must keep working here regardless of
	 * which Aldine version is actually installed.
	 *
	 * @test
	 * @group network-catalog
	 */
	public function it_removes_aldines_hide_catalog_content_editor_hook_old_callback_form(): void
	{
		if (! function_exists('\Aldine\Actions\hide_catalog_content_editor')) {
			require_once __DIR__.'/stubs/aldine-actions-old.php';
		}

		// Firing admin_init directly (rather than via a real wp-admin request)
		// trips WP core's own wp_add_privacy_policy_content() "doing it wrong"
		// notice -- unrelated to what this test is verifying.
		$this->setExpectedIncorrectUsage('wp_add_privacy_policy_content');

		add_action('admin_init', '\Aldine\Actions\hide_catalog_content_editor');
		$this->assertNotFalse(has_action('admin_init', '\Aldine\Actions\hide_catalog_content_editor'));

		PressbooksNetworkCatalog::init();
		do_action('admin_init');

		$this->assertFalse(has_action('admin_init', '\Aldine\Actions\hide_catalog_content_editor'));
	}

	/**
	 * @test
	 * @group network-catalog
	 */
	public function it_removes_aldines_hide_catalog_content_editor_hook_new_callback_form(): void
	{
		if (! class_exists('\Aldine\Actions')) {
			require_once __DIR__.'/stubs/aldine-actions-new.php';
		}

		$this->setExpectedIncorrectUsage('wp_add_privacy_policy_content');

		add_action('admin_init', [\Aldine\Actions::class, 'hideCatalogContentEditor']);
		$this->assertNotFalse(has_action('admin_init', [\Aldine\Actions::class, 'hideCatalogContentEditor']));

		PressbooksNetworkCatalog::init();
		do_action('admin_init');

		$this->assertFalse(has_action('admin_init', [\Aldine\Actions::class, 'hideCatalogContentEditor']));
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
