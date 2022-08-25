<div class="search-box">
	<div class="search">
		<input type="search" />
		<button>{{ __('Search', 'pressbooks-network-catalog') }}</button>
	</div>

	<select class="results-per-page">
		<option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 10) }}</option>
		<option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 20) }}</option>
		<option>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 50) }}</option>
	</select>

	<select class="results-sort-by">
		<option>{{ __('Sort by relevance', 'pressbooks-network-catalog')}}</option>
		<option>{{ __('Sort by recently updated', 'pressbooks-network-catalog') }}</option>
		<option>{{ __('Sort by title (A-Z)', 'pressbooks-network-catalog') }}</option>
	</select>
</div>

<div>
	<h2 class="result-stats">{{ sprintf(__('%d Results for ‘%s’', 'pressbooks-network-catalog'), 2, 'Historical Agriculture') }}</h2>

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
