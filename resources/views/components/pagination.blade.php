@unless($pagination['totalPages'] === 1)
    <nav
        class="pagination"
        role="navigation"
        aria-label="{{ sprintf(__('%s pagination', 'pressbooks-network-catalog'), $placement) }}"
    >
        <ul class="page-list">
            @if($pagination['currentPage'] > 1)
                <li class="page-item previous-page">
                    <a
                        href="{{ $request->fullUrlWithQuery(['pg' => $pagination['currentPage'] - 1]) }}"
                        rel="prev"
                        aria-label="{{ __('Go to previous page', 'pressbooks-network-catalog') }}"
                    >
                        @include('PressbooksNetworkCatalog::icons.chevron-left')
                    </a>
                </li>
            @endif

            @foreach($pagination['elements'] as $page)
                @if(is_string($page))
                    <li class="page-item disabled" aria-disabled="true">
                        <span>{{ $page }}</span>
                    </li>
                @else
                    @if($page === $pagination['currentPage'])
                        <li class="page-item active" aria-current="true">
                            <a href="{{ $request->fullUrlWithQuery(['pg' => $page]) }}">
                                <span class="sr-only">{{ __('Current page, page', 'pressbooks-network-catalog') }}</span>
                                {{ $page }}
                            </a>
                        </li>
                    @else
                        <li class="page-item">
                            <a href="{{ $request->fullUrlWithQuery(['pg' => $page]) }}">
                                <span class="sr-only">{{ __('Go to page', 'pressbooks-network-catalog') }}</span>
                                {{ $page }}
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach

            @if($pagination['currentPage'] < $pagination['totalPages'])
                <li class="page-item next-page">
                    <a
                        href="{{ $request->fullUrlWithQuery(['pg' => $pagination['currentPage'] + 1]) }}"
                        rel="next"
                        aria-label="{{ __('Go to next page', 'pressbooks-network-catalog') }}"
                    >
                        @include('PressbooksNetworkCatalog::icons.chevron-right')
                    </a>
                </li>
            @endif
        </ul>

        <div class="go-to-page" x-data x-id="['pagination-go-to']">
            <label :for="$id('pagination-go-to')">{{ __('Go to page', 'pressbooks-network-catalog') }}</label>
            <div>
                <div class="go-to-page-input">
                    <input type="text" name="pg" :id="$id('pagination-go-to')" placeholder="##" />
                </div>
                <button type="submit">{{ __('Go', 'pressbooks-network-catalog') }}</button>
            </div>
        </div>
    </nav>
@endunless
