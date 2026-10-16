<?php

/*
 * Plugin Name: Pressbooks Network Catalog
 * Plugin URI: https://pressbooks.org
 * Requires at least: 6.5
 * Requires Plugins: pressbooks
 * Description: Add a searchable, filterable catalog to the Pressbooks Aldine theme
 * x-release-please-start-version
 * Version: 1.7.0
 * x-release-please-end
 * Author: Pressbooks (Book Oven Inc.)
 * Author URI: https://pressbooks.org
 * Text Domain: pressbooks-network-catalog
 * Domain Path: /languages/
 * License: GPL v3 or later
 * Network: True
 */

use PressbooksNetworkCatalog\PressbooksNetworkCatalog;

// -------------------------------------------------------------------------------------------------------------------
// Composer autoloader (PSR-4 classes)
// -------------------------------------------------------------------------------------------------------------------
// Installed as a root-composer-managed monorepo dependency, this plugin's classes are already
// available via the root `vendor/autoload.php` (required very early by wp-config.php), so this
// is a no-op there. When this plugin is installed standalone instead, it needs its own
// `composer install` to generate a local `vendor/autoload.php`.
if (! class_exists('\PressbooksNetworkCatalog\PressbooksNetworkCatalog')) {
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    } else {
        $title = __('Dependencies Missing', 'pressbooks-network-catalog');
        $body = __('Please run <code>composer install</code> from the root of the Pressbooks Network Catalog plugin directory.', 'pressbooks-network-catalog');
        $message = "<h1>{$title}</h1><p>{$body}</p>";
        wp_die($message, $title);
    }
}

const IS_VITE_DEVELOPMENT = false;

add_action('plugins_loaded', [PressbooksNetworkCatalog::class, 'init']);
