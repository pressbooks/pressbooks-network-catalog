# Pressbooks Network Catalog plugin

Contributors: fdalcin, arzola, richard015ar, steelwagstaff \
Donate link: https://pressbooks.com/ \
Requires at least: 6.3.2 \
Tested up to: 6.3.2 \
Stable tag: 1.3.4 \
Requires PHP: 8.1 \
License: GPLv3 or later \
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This plugin adds a searchable, filterable catalog to the Pressbooks Aldine theme. 

## Requirements 
* PHP >=8.1 
* WordPress >= 6.3.2 
* Pressbooks >= 6.9.0
* Aldine >= 1.19.0

## Installation

Run `composer require pressbooks/pressbooks-network-catalog` in your project bedrock.

## Frontend development

Run `npm install` to install the dependencies.

The project uses VITE and Tailwind JIT to compile the frontend assets. Run `npm run dev` to compile the assets and use hot reloading.

**To enable hot reloading:**

1. Set `IS_VITE_DEVELOPMENT` environment variable to `true` to enable the development server, located in the `pressbooks-netwotk-catalog.php` (main plugin file).

2. Run `npm run dev` this will watch for changes and recompile the assets using hot reloading.

## Build frontend assets

**If you've set up hot reloading, don't forget to revert `IS_VITE_DEVELOPMENT` to `false` in the `pressbooks-network-catalog.php` (main plugin file).**

Run `npm run build` to build the frontend assets.

## Run tests
Run `composer test` to run the tests.

## Lint coding standards
Run `composer standards` to run the tests.

## Changelog

### 1.3.4
* See: https://github.com/pressbooks/pressbooks-network-catalog/releases/tag/1.3.4
* Full release history available at: https://github.com/pressbooks/pressbooks-network-catalog/releases

## Upgrade Notices
### Pressbooks Network Catalog 1.3.4
* Pressbooks Network Catalog requires WordPress >= 6.3.2

### Pressbooks Network Catalog 1.3.0
* Pressbooks Network Catalog requires PHP >= 8.1

### Pressbooks Network Catalog 1.1.0
* Pressbooks Network Catalog requires PHP >= 8.0
* Pressbooks Network Catalog requires WordPress >= 6.1.1
* Pressbooks Network Catalog requires Pressbooks >= 6.9.0
* Pressbooks Network Catalog requires Aldine >= 1.19.0
