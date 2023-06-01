<div class="search-box">
	@include('PressbooksNetworkCatalog::components.search-input')

	@include('PressbooksNetworkCatalog::partials.refine-filters', ['class' => 'order-desktop'])
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
				<button type="button" class="remove" @click="removeFilter('{{ addslashes($filter['key']) }}')">
					<span class="sr-only">{{ sprintf(__('Remove %s filter', 'pressbooks-network-catalog'), $filter['label']) }}</span>
					@include('PressbooksNetworkCatalog::icons.x-mark')
				</button>
			</div>
		@endforeach
	</section>
@endif
