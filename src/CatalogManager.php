<?php

namespace PressbooksNetworkCatalog;

use Illuminate\Http\Request;
use Pressbooks\Container;
use PressbooksNetworkCatalog\Filters\Institution;
use PressbooksNetworkCatalog\Filters\License;
use PressbooksNetworkCatalog\Filters\Publisher;
use PressbooksNetworkCatalog\Filters\Subject;

class CatalogManager
{
	public function handle()
	{
		$request = Request::capture();

		return Container::get('Blade')->render(
			'PressbooksNetworkCatalog::catalog', [
				'request' => $request,
				'books' => $this->queryBooks(),
				'subjects' => Subject::getPossibleValues(),
				'licenses' => License::getPossibleValues(),
				'institutions' => Institution::getPossibleValues(),
				'publishers' => Publisher::getPossibleValues(),
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
		return (new Books())->get();
	}
}
