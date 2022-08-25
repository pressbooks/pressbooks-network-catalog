<div class="search-box">
	<div class="search">
		<input type="search" />
		<button>{{ __('Search', 'pressbooks-network-catalog') }}</button>
	</div>

	<div class="results-per-page">
		<span>{{ sprintf(__('%d results', 'pressbooks-network-catalog'), 10) }}</span>
		<span>&#8964</span>
	</div>

	<div class="results-sort-by">
		<span>{{ __('Sort by relevance', 'pressbooks-network-catalog') }}</span>
		<span>&#8964</span>
	</div>
</div>

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
