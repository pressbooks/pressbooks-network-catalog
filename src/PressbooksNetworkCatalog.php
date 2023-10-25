<?php

namespace PressbooksNetworkCatalog;

use Pressbooks\Container;

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
	 * @throws \JsonException
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

			/**
			 * VITE & Tailwind JIT development
			 * Inspired by https://github.com/andrefelipe/vite-php-setup
			 */
			if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT) {
				// insert hmr into head for live reload
				add_action('wp_head', function () {
					echo '<script type="module" crossorigin src="http://localhost:3000/assets/js/app.js"></script>';
					echo '<link rel="stylesheet" href="http://localhost:3000/assets/css/app.css">';
				});

				return;
			}

			$distUri = plugin_dir_url(__DIR__).'/dist';
			$distPath = plugin_dir_path(__DIR__).'/dist';

			// production version, 'npm run build' must be executed in order to generate assets
			$manifest = json_decode(file_get_contents("$distPath/manifest.json"), true, 512, JSON_THROW_ON_ERROR);

			if (isset($manifest['assets/css/app.css'])) {
				$entry_css = $manifest['assets/css/app.css']['file'];
				wp_enqueue_style('pb-network-catalog/style', "$distUri/$entry_css",
					['aldine/style']); // override Aldine's
			}
			if (isset($manifest['assets/js/app.js'])) {
				$entry_js = $manifest['assets/js/app.js']['file'];
				wp_enqueue_script('pb-network-catalog/script', "$distUri/$entry_js", ['jquery'], null, true);
			}
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
	}
}
