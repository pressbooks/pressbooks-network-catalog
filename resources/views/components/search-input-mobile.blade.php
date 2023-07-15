<div class="search">
	<label id="search-input-mobile-label" class="sr-only">{{ __('Search by title, author, keyword', 'pressbooks-network-catalog') }}</label>
	<div class="search-input-mobile">
		<input
			type="search"
			name="search_term"
			placeholder="{{ __('Search by title, author, keyword', 'pressbooks-network-catalog') }}"
			value="{{ $request->search_term }}"
			aria-labelledby="search-input-label"
		/>
	</div>
	<button id="search-mobile" type="submit">
		<span class="sr-only">{{ __('Search', 'pressbooks-network-catalog') }}</span>
		@include('PressbooksNetworkCatalog::icons.search')
	</button>
</div>
