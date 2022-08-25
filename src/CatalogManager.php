<?php

namespace PressbooksNetworkCatalog;

use Pressbooks\Container;
use PressbooksNetworkCatalog\Filters\Institution;
use PressbooksNetworkCatalog\Filters\License;
use PressbooksNetworkCatalog\Filters\Subject;

class CatalogManager
{
	public function handle()
	{
		return Container::get('Blade')->render(
			'PressbooksNetworkCatalog::catalog', [
				'books' => $this->queryBooks(),
				'subjects' => Subject::getPossibleValues(),
				'licenses' => License::getPossibleValues(),
				'institutions' => Institution::getPossibleValues(),
				'publishers' => [],
			]
		);
	}

	/**
	 * Query books list and return array of book objects
	 *
	 * @return array
	 */
	protected function queryBooks(): array
	{
		$books = new Books();

		return $books->get();
	}
}
