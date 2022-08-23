<?php
/*
 * Plugin Name: Pressbooks Network Catalog
 * Plugin URI: https://pressbooks.org
 * Description: A books catalog for Pressbooks Network.
 * Version: 1.0.0
 * Author: Pressbooks (Book Oven Inc.)
 * Author URI: https://pressbooks.org
 * Text Domain: pressbooks-network-catalog
 * License: GPL v3 or later
 * Network: True
 */

use PressbooksNetworkCatalog\PressbooksNetworkCatalog;

require __DIR__ . '/vendor/autoload.php';

add_action( 'plugins_loaded', [PressbooksNetworkCatalog::class, 'init']);
