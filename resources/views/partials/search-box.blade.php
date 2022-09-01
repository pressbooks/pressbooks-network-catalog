<div class="search-box">
    <div class="search">
        <label id="search-input-label" class="sr-only">{{ __('Search', 'pressbooks-network-catalog') }}</label>
        <input
			type="search"
			name="search"
			placeholder="{{ __('Search by title, author, keyword', 'pressbooks-network-catalog') }}"
			value="{{ $request->search }}"
			aria-labelledby="search-input-label"
		/>
        <button id="search" type="submit">{{ __('Search', 'pressbooks-network-catalog') }}</button>
    </div>

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
    	'default' => 'relevance',
    	'options' => [
    		'relevance' => __('Sort by relevance', 'pressbooks-network-catalog'),
    		'last_updated' => __('Sort by recently updated', 'pressbooks-network-catalog'),
    		'title' => __('Sort by title (A-Z)', 'pressbooks-network-catalog'),
		]
	])
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

    <div class="applied-filters" x-data="">
        @foreach($request->activeFilters as $filter)
            <div class="applied-filter">
                <span>{{ $filter['label'] }}</span>
                <a class="remove" @click="removeFilter('{{$filter['key']}}')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        @endforeach
    </div>
</div>
