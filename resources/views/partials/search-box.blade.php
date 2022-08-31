<div class="search-box">
    <div class="search">
        <label id="search-input-label" class="sr-only">{{ __('Search', 'pressbooks-network-catalog') }}</label>
        <input type="search" name="search" placeholder="{{ __('Find a book', 'pressbooks-network-catalog') }}"
               aria-labelledby="search-input-label" />
        <button type="submit">{{ __('Search', 'pressbooks-network-catalog') }}</button>
    </div>

    <label id="per-page-label"
           class="sr-only">{{ __('Number of results per page', 'pressbooks-network-catalog') }}</label>
    <select class="results-per-page" aria-labelledby="per-page-label">
        <option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 10) }}</option>
        <option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 20) }}</option>
        <option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 50) }}</option>
    </select>

    <label id="sort-by-label" class="sr-only">{{ __('Sort results by', 'pressbooks-network-catalog') }}</label>
    <select class="results-sort-by" aria-labelledby="sort-by-label">
        <option>{{ __('Sort by relevance', 'pressbooks-network-catalog')}}</option>
        <option>{{ __('Sort by recently updated', 'pressbooks-network-catalog') }}</option>
        <option>{{ __('Sort by title (A-Z)', 'pressbooks-network-catalog') }}</option>
    </select>
</div>

<div>
    <h2 class="result-stats">{{ sprintf(__('%d Results for ‘%s’', 'pressbooks-network-catalog'), 2, $request->search ) }}</h2>

    <div class="applied-filters">
		<span class="applied-filter">
			<span>History</span>
			<span>x</span>
		</span>
        <span class="applied-filter">
			<span>CC BY</span>
			<span>x</span>
		</span>
    </div>
</div>
