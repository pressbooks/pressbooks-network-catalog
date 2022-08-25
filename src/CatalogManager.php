<?php

namespace PressbooksNetworkCatalog;

use Pressbooks\Container;
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
				'last_updated' => [],
				'institutions' => [],
				'publishers' => [],
				'h5p_activities' => [],
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
