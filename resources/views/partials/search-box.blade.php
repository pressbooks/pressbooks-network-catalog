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
        <button type="submit">{{ __('Search', 'pressbooks-network-catalog') }}</button>
    </div>

    <div>
        <label id="per-page-label"
               class="sr-only">{{ __('Number of results per page', 'pressbooks-network-catalog') }}</label>
        <select class="results-per-page" aria-labelledby="per-page-label">
            <option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 10) }}</option>
            <option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 20) }}</option>
            <option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 50) }}</option>
        </select>
    </div>

    <div>
        <label id="sort-by-label" class="sr-only">{{ __('Sort results by', 'pressbooks-network-catalog') }}</label>
        <select class="results-sort-by" aria-labelledby="sort-by-label">
            <option>{{ __('Sort by relevance', 'pressbooks-network-catalog')}}</option>
            <option>{{ __('Sort by recently updated', 'pressbooks-network-catalog') }}</option>
            <option>{{ __('Sort by title (A-Z)', 'pressbooks-network-catalog') }}</option>
        </select>
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
