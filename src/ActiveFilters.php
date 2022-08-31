<?php

namespace PressbooksNetworkCatalog;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ActiveFilters
{
	private array $filters;

	private Collection $items;

	public function __construct(array $filters = [])
	{
		$this->filters = $filters;
		$this->items = collect();
	}

	public function getFilters(Request $request): Collection
	{
		foreach ($this->filters as $filter => $values) {
			if ($request->has($filter)) {
				collect($request->{$filter})->each(function ($item) use ($filter) {
					$this->items->push([
						'key' => $item,
						'label' => $this->getFilterValue($filter, $item),
						'type' => $filter,
					]);
				});
			}
		}
		if ($request->has('h5p')) {
			$this->items->push([
				'key' => 'h5p',
				'label' => __('Has H5P Activities', 'pressbooks-network-catalog'),
				'type' => 'h5p',
			]);
		}

		return $this->items;
	}

	public function getFilterValue(string $filterName, string $filterValue)
	{
		return $this->filters[$filterName][$filterValue];
	}
}
