<?php

namespace PressbooksNetworkCatalog;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pressbooks\Container;
use PressbooksNetworkCatalog\Filters\Institution;
use PressbooksNetworkCatalog\Filters\License;
use PressbooksNetworkCatalog\Filters\Publisher;
use PressbooksNetworkCatalog\Filters\Subject;

class CatalogManager
{
	private array $filters = [];

	private $request;

	public function handle()
	{
		$this->request = Request::capture();

		$this->filters = [
			'subjects' => Subject::getPossibleValues(),
			'licenses' => License::getPossibleValues(),
			'institutions' => Institution::getPossibleValues(),
			'publishers' => Publisher::getPossibleValues(),
		];
		// Inject active filters into the request object
		$this->request->activeFilters = $this->getActiveFilters();

		return Container::get('Blade')->render(
			'PressbooksNetworkCatalog::catalog', [
				'request' => $this->request,
				'books' => $this->getBooks(),
			] + $this->filters
		);
	}

	/**
	 * Query books list and return array of book objects
	 *
	 * @return array
	 */
	protected function getBooks(): array
	{
		return (new Books())->get();
	}

	protected function getActiveFilters(): Collection
	{
		return (new ActiveFilters($this->filters))->getFilters($this->request);
	}
}
