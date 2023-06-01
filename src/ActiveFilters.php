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

		if ($request->from) {
			$this->items->push([
				'key' => 'from',
				'label' => sprintf(__('From: %s', 'pressbooks-network-catalog'), $request->from),
				'type' => 'date',
			]);
		}

		if ($request->to) {
			$this->items->push([
				'key' => 'to',
				'label' => sprintf(__('To: %s', 'pressbooks-network-catalog'), $request->to),
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
