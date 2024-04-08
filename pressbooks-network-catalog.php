<?php
/*
 * Plugin Name: Pressbooks Network Catalog
 * Plugin URI: https://pressbooks.org
 * Requires at least: 6.5
 * Requires Plugins: pressbooks
 * Description: Add a searchable, filterable catalog to the Pressbooks Aldine theme
 * Version: 1.3.5
 * Author: Pressbooks (Book Oven Inc.)
 * Author URI: https://pressbooks.org
 * Text Domain: pressbooks-network-catalog
 * Domain Path: /languages/
 * License: GPL v3 or later
 * Network: True
 */

use PressbooksNetworkCatalog\PressbooksNetworkCatalog;

const IS_VITE_DEVELOPMENT = false;

add_action( 'plugins_loaded', [PressbooksNetworkCatalog::class, 'init']);
