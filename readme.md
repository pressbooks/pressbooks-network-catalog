# Pressbooks Network Catalog plugin

Contributors: fdalcin, arzola, richard015ar, steelwagstaff \
Donate link: https://pressbooks.com/ \
Requires at least: 6.0 \
Tested up to: 6.0.3 \
Stable tag: 1.0.0 \
Requires PHP: 7.4 \
License: GPLv3 or later \
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This plugin adds a searchable, filterable catalog to the Pressbooks Aldine theme. Requires Aldine 1.15.0 or later.

## Installation

Run `composer require pressbooks/pressbooks-network-catalog` in your project bedrock.

## Frontend development

The project uses VITE and Tailwind JIT to compile the frontend assets. Run `npm run dev` to compile the assets and uses hot reloading.

Run `npm install` to install the dependencies.

**To enable hot reloading.**

1. Set `IS_VITE_DEVELOPMENT` environment variable to `true` to enable the development server, located in the `pressbooks-netwotk-catalog.php` (main plugin file).

2. Run `npm run dev` this will watch for changes and recompile the assets using hot reloading.

## Build frontend assets

Run `npm run build` to build the frontend assets.

> Don't forget to revert `IS_VITE_DEVELOPMENT` to `false` in the `pressbooks-netwotk-catalog.php` (main plugin file).

## Run tests
Run `composer test` to run the tests.

## Lint coding standards
Run `composer standars` to run the tests.

## Changelog

### 1.0.0

* See: https://github.com/pressbooks/pressbooks-network-catalog/releases/tag/1.0.0
* Full release history available at: https://github.com/pressbooks/pressbooks-network-catalog/releases
