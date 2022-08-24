<?php

namespace PressbooksNetworkCatalog;

use Pressbooks\Container;

class PressbooksNetworkCatalog
{
    public static function init(): void
    {
        Container::get('Blade')
            ->addNamespace(
                'PressbooksNetworkCatalog',
                dirname(__DIR__).'/resources/views'
            );
        (new self())->setUp();
        add_filter('pb_network_catalog', fn() => (new CatalogManager)->handle());
    }

    public function setUp()
    {
        /**
         * VITE & Tailwind JIT development
         * Inspired by https://github.com/andrefelipe/vite-php-setup
         *
         */
        define('DIST_DEF', 'dist');
        define('DIST_URI', plugin_dir_url(__DIR__).DIST_DEF);
        define('DIST_PATH', plugin_dir_path(__DIR__).DIST_DEF);
        define('JS_DEPENDENCY', []);
        define('JS_LOAD_IN_FOOTER', true);
        define('VITE_SERVER', 'http://127.0.0.1:3000');
        define('VITE_ENTRY_POINT', '/index.js');

        add_action('wp_enqueue_scripts', function () {

            if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {

                // insert hmr into head for live reload
                add_action('wp_head', function () {
                    echo '<script type="module" crossorigin src="'.VITE_SERVER.VITE_ENTRY_POINT.'"></script>';
                });
            } else {

                // production version, 'npm run build' must be executed in order to generate assets
                $manifest = json_decode(file_get_contents(DIST_PATH.'/manifest.json'), true);

                if (is_array($manifest)) {
                    $manifest_key = array_keys($manifest);
                    if (isset($manifest_key[0])) {

                        // enqueue CSS files
                        foreach (@$manifest[$manifest_key[0]]['css'] as $css_file) {
                            wp_enqueue_style('main', DIST_URI.'/'.$css_file);
                        }
                        // enqueue main JS file
                        $js_file = @$manifest[$manifest_key[0]]['file'];
                        if (! empty($js_file)) {
                            wp_enqueue_script('main', DIST_URI.'/'.$js_file, JS_DEPENDENCY, '', JS_LOAD_IN_FOOTER);
                        }
                    }
                }
            }
            wp_dequeue_script('aldine_scripts');
        });
    }
}
