<div class="search">
    <label id="search-input-label" class="sr-only">{{ __('Search by title, author, keyword', 'pressbooks-network-catalog') }}</label>
    <div class="search-input">
        <input
            type="search"
            name="search_term"
            placeholder="{{ __('Search by title, author, keyword', 'pressbooks-network-catalog') }}"
            value="{{ $request->search_term }}"
            aria-labelledby="search-input-label"
        />
    </div>
    <button id="search" type="submit">
        <span class="sr-only">{{ __('Search', 'pressbooks-network-catalog') }}</span>
        @include('PressbooksNetworkCatalog::icons.search')
    </button>
</div>
