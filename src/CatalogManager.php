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

		$books = new Books($this->filters);

		$this->request->replace($this->request->collect()->map(function ($value) {
			if (is_array($value)) {
				return array_map(function ($param) {
					return $this->sanitize($param);
				}, $value);
			}

			return $this->sanitize($value);
		})->toArray());

		return Container::get('Blade')->render(
			'PressbooksNetworkCatalog::catalog', [
				'request' => $this->request,
				'books' => $books->get(),
				'pagination' => $books->getPagination(),
				'catalogBg' => $this->getBackgroundImage(),
			] + $this->filters
		);
	}

	protected function getActiveFilters(): Collection
	{
		return (new ActiveFilters($this->filters))->getFilters($this->request);
	}

	protected function getBackgroundImage(): string
	{
		return plugin_dir_url(__DIR__).'assets/images/catalogbg.jpg';
	}

	protected function sanitize($value): string
	{
		return is_string($value) ? filter_var($value, FILTER_SANITIZE_STRING) : $value;
	}
}
