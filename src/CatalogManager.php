<?php

namespace PressbooksNetworkCatalog;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PressbooksNetworkCatalog\Filters\Institution;
use PressbooksNetworkCatalog\Filters\License;
use PressbooksNetworkCatalog\Filters\Publisher;
use PressbooksNetworkCatalog\Filters\Subject;

class CatalogManager
{
	private array $filters = [];

	private $request;

	public function handle(): array
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

		$this->request->replace($this->sanitizeRequestParams($this->request));

		return [
			'request' => $this->request,
			'books' => $books->get(),
			'pagination' => $books->getPagination(),
			'catalogBg' => $this->getBackgroundImage(),
			'catalogHasBooks' => $books->catalogHasBooks(),
		] + $this->filters;
	}

	protected function getActiveFilters(): Collection
	{
		return (new ActiveFilters($this->filters))->getFilters($this->request);
	}

	protected function getBackgroundImage(): string
	{
		return has_post_thumbnail()
			? get_the_post_thumbnail_url()
			: plugin_dir_url(__DIR__).'assets/images/catalogbg.jpg';
	}

	protected function sanitizeRequestParams($request)
	{
		return $request->collect()->map(function ($value) {
			if (is_array($value)) {
				return array_map(function ($param) {
					return $this->sanitize($param);
				}, $value);
			}

			return $this->sanitize($value);
		})->toArray();
	}

	protected function sanitize(string $value): string
	{
		return stripslashes($value);
	}
}
