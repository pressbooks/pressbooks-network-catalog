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

    protected function enqueueScripts(): void
    {
        add_action('wp_enqueue_scripts', function () {
            if (get_page_template_slug() !== 'page-catalog.php') {
                return;
            }

            /**
             * VITE & Tailwind JIT development
             * Inspired by https://github.com/andrefelipe/vite-php-setup
             */
            if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT) {
                // insert hmr into head for live reload
                add_action('wp_head', function () {
                    echo '<script type="module" crossorigin src="http://127.0.0.1:3000/assets/js/app.js"></script>';
                });

                return;
            }

            $distUri = plugin_dir_url(__DIR__).'/dist';
            $distPath = plugin_dir_path(__DIR__).'/dist';

            // production version, 'npm run build' must be executed in order to generate assets
            $manifest = json_decode(file_get_contents("$distPath/manifest.json"), true, 512, JSON_THROW_ON_ERROR);

            if (is_array($manifest)) {
                $manifest_key = array_keys($manifest);

                if (isset($manifest_key[0])) {
                    // enqueue CSS files
                    foreach ($manifest[$manifest_key[0]]['css'] ?? [] as $css_file) {
                        wp_enqueue_style('pb-network-catalog/style', "$distUri/$css_file", ['aldine/style']); // override Aldine's
                    }

                    // enqueue main JS file
                    $js_file = $manifest[$manifest_key[0]]['file'] ?? null;
                    if (! empty($js_file)) {
                        wp_enqueue_script('pb-network-catalog/script', "$distUri/$js_file", [], '', true);
                    }
                }
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
        add_filter('pb_network_catalog', fn () => (new CatalogManager)->handle());
    }
}
