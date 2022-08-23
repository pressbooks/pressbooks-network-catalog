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

		add_filter('pb_network_catalog', fn () => (new CatalogManager)->handle());
	}
}
