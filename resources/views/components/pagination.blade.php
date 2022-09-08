@unless($pagination['currentPage'] === $pagination['totalPages'])
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
                        aria-label="{{ __('Previous page', 'pressbooks-network-catalog') }}"
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
                    @if($page === (int) ($request->pg ?? 1))
                        <li class="page-item active" aria-current="true">
                            <span>{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a href="{{ $request->fullUrlWithQuery(['pg' => $page]) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endif
            @endforeach

            @if($pagination['currentPage'] < $pagination['totalPages'])
                <li class="page-item next-page">
                    <a
                        href="{{ $request->fullUrlWithQuery(['pg' => $pagination['currentPage'] + 1]) }}"
                        rel="next"
                        aria-label="{{ __('Next page', 'pressbooks-network-catalog') }}"
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
                    <input type="text" name="pg" :id="$id('pagination-go-to')" />
                </div>
                <button type="submit">{{ __('Go', 'pressbooks-network-catalog') }}</button>
            </div>
        </div>
    </nav>
@endunless
