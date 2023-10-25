<?php
/*
 * Plugin Name: Pressbooks Network Catalog
 * Plugin URI: https://pressbooks.org
 * Description: Add a searchable, filterable catalog to the Pressbooks Aldine theme
 * Version: 1.3.4
 * Author: Pressbooks (Book Oven Inc.)
 * Author URI: https://pressbooks.org
 * Text Domain: pressbooks-network-catalog
 * Domain Path: /languages/
 * License: GPL v3 or later
 * Network: True
 */

use PressbooksNetworkCatalog\PressbooksNetworkCatalog;

const IS_VITE_DEVELOPMENT = false;

if ( ! class_exists( 'Pressbooks\Book' ) ) {
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	} else {
		$title = __( 'Missing dependencies', 'pressbooks-network-catalog' );
		$body = __(
			'Please run <code>composer install</code> from the root of the Pressbooks Network Catalog plugin directory.',
			'pressbooks-network-catalog'
		);

		wp_die( "<h1>{$title}</h1><p>{$body}</p>" );
	}
}

add_action( 'plugins_loaded', [PressbooksNetworkCatalog::class, 'init']);
