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
        $this->setUpViteConfig();

        $this->setUpBlade();

        $this->addHooks();
    }

    protected function setUpViteConfig(): void
    {
        /**
         * VITE & Tailwind JIT development
         * Inspired by https://github.com/andrefelipe/vite-php-setup
         */
        add_action('wp_enqueue_scripts', function () {
            if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT) {
                // insert hmr into head for live reload
                add_action('wp_head', function () {
                    echo '<script type="module" crossorigin src="http://127.0.0.1:3000/index.js"></script>';
                });

                return;
            }

            $distUri = plugin_dir_url(__DIR__).'/dist';
            $distPath = plugin_dir_path(__DIR__).'/dist';

            // production version, 'npm run build' must be executed in order to generate assets
            $manifest = json_decode(file_get_contents("$distPath/manifest.json"), true);

            if (is_array($manifest)) {
                $manifest_key = array_keys($manifest);

                if (isset($manifest_key[0])) {
                    // enqueue CSS files
                    foreach ($manifest[$manifest_key[0]]['css'] ?? [] as $css_file) {
                        wp_enqueue_style('main', "$distUri/$css_file");
                    }

                    // enqueue main JS file
                    $js_file = $manifest[$manifest_key[0]]['file'] ?? null;
                    if (! empty($js_file)) {
                        wp_enqueue_script('main', "$distUri/$js_file", [], '', true);
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
