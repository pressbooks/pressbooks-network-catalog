<div class="search-box">
	@include('PressbooksNetworkCatalog::components.search-input')

	<div class="refine-filters">
		@include('PressbooksNetworkCatalog::components.dropdown', [
			'label' => __('Results per page', 'pressbooks-network-catalog'),
			'name' => 'per_page',
			'default' => 10,
			'options' => [
				10 => sprintf(__('%d results', 'pressbooks-network-catalog'), 10),
				20 => sprintf(__('%d results', 'pressbooks-network-catalog'), 20),
				50 => sprintf(__('%d results', 'pressbooks-network-catalog'), 50),
			]
		])

		@include('PressbooksNetworkCatalog::components.dropdown', [
			'label' => __('Sort by', 'pressbooks-network-catalog'),
			'name' => 'sort_by',
			'default' => 'last_updated',
			'options' => [
				//'relevance' => __('Sort by relevance', 'pressbooks-network-catalog'),
				'last_updated' => __('Sort by recently updated', 'pressbooks-network-catalog'),
				'title' => __('Sort by title (A-Z)', 'pressbooks-network-catalog'),
			]
		])
	</div>
</div>

@if(!empty($request->search) && $request->has('search'))
	<span class="result-stats">
		{{ sprintf(_n('%d result for ‘%s’', '%d results for ‘%s’', $pagination['total'], 'pressbooks-network-catalog'), $pagination['total'], $request->search ) }}
	</span>
@elseif($pagination['currentPage'] <= $pagination['totalPages'])
	<span class="result-stats">
		{{ sprintf(_n('%d result', '%d results', $pagination['total'], 'pressbooks-network-catalog'), $pagination['total']) }}
	</span>
@endif

@if($request->activeFilters->isNotEmpty())
	<section class="applied-filters" x-data aria-label="{{ __('Applied filters', 'pressbooks-network-catalog') }}">
		@foreach($request->activeFilters as $filter)
			<div class="applied-filter">
				<span>{{ $filter['label'] }}</span>
				<button type="button" class="remove" @click="removeFilter('{{ $filter['key'] }}')">
					<span class="sr-only">{{ sprintf(__('Remove %s filter', 'pressbooks-network-catalog'), $filter['label']) }}</span>
					@include('PressbooksNetworkCatalog::icons.x-mark')
				</button>
			</div>
		@endforeach
	</section>
@endif
