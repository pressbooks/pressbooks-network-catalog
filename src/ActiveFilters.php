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
					$value = stripslashes($item);
					if ($this->filters[$filter][$value] ?? false) {
						$this->items->push([
							'key' => $value,
							'label' => $this->getFilterValue($filter, $value),
							'type' => $filter,
						]);
					}
				});
			}
		}
		if ($request->has('h5p')) {
			$this->items->push([
				'key' => 'h5p',
				'label' => __('H5P Activities', 'pressbooks-network-catalog'),
				'type' => 'h5p',
			]);
		}

		// Publication date pickers
		if ($request->has('published_from') && ! empty($request->published_from)) {
			$this->items->push([
				'key' => 'publication_date:from',
				'label' => sprintf(__('Publication date — From: %s', 'pressbooks-network-catalog'), $request->published_from),
				'type' => 'date',
			]);
		}

		if ($request->has('published_to') && ! empty($request->published_to)) {
			$this->items->push([
				'key' => 'publication_date:to',
				'label' => sprintf(__('Publication date — To: %s', 'pressbooks-network-catalog'), $request->published_to),
				'type' => 'date',
			]);
		}

		// Last-updated pickers
		if ($request->has('updated_from') && ! empty($request->updated_from)) {
			$this->items->push([
				'key' => 'last_updated:from',
				'label' => sprintf(__('Last updated — From: %s', 'pressbooks-network-catalog'), $request->updated_from),
				'type' => 'date',
			]);
		}

		if ($request->has('updated_to') && ! empty($request->updated_to)) {
			$this->items->push([
				'key' => 'last_updated:to',
				'label' => sprintf(__('Last updated — To: %s', 'pressbooks-network-catalog'), $request->updated_to),
				'type' => 'date',
			]);
		}

		return $this->items;
	}

	public function getFilterValue(string $filterName, string $filterValue)
	{
		return $this->filters[$filterName][$filterValue];
	}
}
