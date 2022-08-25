<?php

namespace PressbooksNetworkCatalog;

use Pressbooks\Container;

class CatalogManager
{
	public function handle()
	{
		return Container::get('Blade')->render(
			'PressbooksNetworkCatalog::catalog', [
				'books' => $this->queryBooks(),
				'filters' => [
					'Subject',
					'License',
					'Last Updated',
					'Institution',
					'Publisher',
					'H5P Activities',
				],
			]
		);
	}

	/**
	 * @return object[]
	 */
	protected function queryBooks(): array
	{
        $books = new Books();
        return $books->get();
	}
}
