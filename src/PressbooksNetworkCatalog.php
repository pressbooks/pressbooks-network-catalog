<?php

namespace PressbooksNetworkCatalog;

use Pressbooks\Container;
use Pressbooks\DataCollector\Book;
use function Pressbooks\Metadata\get_in_catalog_option;
use PressbooksFrontendTools\Assets;
use PressbooksFrontendTools\AssetType;

class PressbooksNetworkCatalog
{
	protected static ?PressbooksNetworkCatalog $instance = null;

	public static function init(): self
	{
		if (! static::$instance) {
			static::$instance = new static;

			static::$instance->setUp();
		}

		return static::$instance;
	}

	public function setUp(): void
	{
		$this->enqueueScripts();

		$this->setUpBlade();

		$this->addHooks();
	}

	/**
	 * @return void
	 * @codeCoverageIgnore
	 */
	protected function enqueueScripts(): void
	{
		add_action('wp_enqueue_scripts', function () {
			if (get_page_template_slug() !== 'page-catalog.php') {
				return;
			}

			// Remove old catalog.js scripts
			add_action('wp_print_scripts', function () {
				wp_dequeue_script('aldine/script');
			}, 100);

			$assets = new Assets('pressbooks-network-catalog', AssetType::PLUGIN);
			$assets->enqueue('assets/js/app.js', 'pb-network-catalog-script', [
				'dependencies' => ['jquery', 'duet-date-picker'],
				'css-dependencies' => ['aldine/style'],
			]);
		});
	}

	protected function setUpBlade(): void
	{
		Container::get('Blade')
			->addNamespace(
				'PressbooksNetworkCatalog',
				dirname(__DIR__).'/resources/views'
			);
	}

	protected function addHooks(): void
	{
		add_filter('pb_network_catalog', function () {
			$data = (new CatalogManager)->handle();

			return Container::get('Blade')->render('PressbooksNetworkCatalog::catalog', $data);
		});

		add_filter(
			'admin_init', fn () => remove_action('admin_init', '\Aldine\Actions\hide_catalog_content_editor'), 1
		);

		add_action('init', function () {
			load_plugin_textdomain('pressbooks-network-catalog', false, 'pressbooks-network-catalog/languages');
		});

		add_action('deactivate_blog', function ($blogId) {
			switch_to_blog($blogId);

			update_option(get_in_catalog_option(), 0);
			update_site_meta($blogId, Book::IN_CATALOG, 0);

			restore_current_blog();
		});
	}
}
