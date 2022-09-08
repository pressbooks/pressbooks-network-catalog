<div class="search-box">
    <div class="search">
        <label id="search-input-label" class="sr-only">{{ __('Search', 'pressbooks-network-catalog') }}</label>
		<div class="search-input">
			<input
				type="search"
				name="search"
				placeholder="{{ __('Search by title, author, keyword', 'pressbooks-network-catalog') }}"
				value="{{ $request->search }}"
				aria-labelledby="search-input-label"
			/>
		</div>
		<button id="search" type="submit">
			<span class="sr-only">{{ __('Search', 'pressbooks-network-catalog') }}</span>
			@include('PressbooksNetworkCatalog::icons.search')
		</button>
    </div>

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

<div>
    @if(!empty($request->search) && $request->has('search'))
        <h2 class="result-stats">
            {{ sprintf(__('%d Results for ‘%s’', 'pressbooks-network-catalog'), count($books), $request->search ) }}
        </h2>
    @else
        <h2 class="result-stats">
            {{ sprintf(__('%d Results', 'pressbooks-network-catalog'), count($books)) }}
        </h2>
    @endif

	@if($request->activeFilters->isNotEmpty())
		<div class="applied-filters" x-data>
			@foreach($request->activeFilters as $filter)
				<div class="applied-filter">
					<span>{{ $filter['label'] }}</span>
					<button type="button" class="remove" @click="removeFilter('{{ $filter['key'] }}')">
						<span class="sr-only">{{ sprintf(__('Remove %s filter', 'pressbooks-network-catalog'), $filter['label']) }}</span>
						@include('PressbooksNetworkCatalog::icons.x-mark')
					</button>
				</div>
			@endforeach
		</div>
	@endif
</div>
