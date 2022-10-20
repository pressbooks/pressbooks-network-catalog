@unless($pagination['totalPages'] <= 1)
	<nav
		class="pagination {{$placement}}"
		role="navigation"
		aria-label="{{ sprintf(__('%s pagination', 'pressbooks-network-catalog'), $placement) }}"
		x-data x-id="['pagination-dropdown']"
	>
		@if($pagination['currentPage'] > 1)
			<a
				class="pagination-item"
				rel="prev"

				href="{{ $request->fullUrlWithQuery(['pg' => $pagination['previousPage']]) }}#catalog"
				disabled
			>
				<span class="sr-only">{{ __('Go to previous page', 'pressbooks-network-catalog') }}</span>
				@include('PressbooksNetworkCatalog::icons.chevron-left')
			</a>
		@endif

		<label class="sr-only" :for="$id('pagination-dropdown')">{{ __('Go to page', 'pressbooks-network-catalog') }}</label>
		<select name="pg" :id="$id('pagination-dropdown')">
			@foreach(range(1, $pagination['totalPages']) as $page)
				<option
					{{ $page === $pagination['currentPage'] ? 'selected' : '' }}
					value="{{ $page }}"
				>
					{{ sprintf(__('%d of %d', 'pressbooks-network-catalog'), $page, $pagination['totalPages']) }}
				</option>
			@endforeach
		</select>

		@if($pagination['currentPage'] < $pagination['totalPages'])
			<a
				class="pagination-item"
				rel="next"
				href="{{ $request->fullUrlWithQuery(['pg' => $pagination['nextPage']]) }}#catalog"
			>
				<span class="sr-only">{{ __('Go to next page', 'pressbooks-network-catalog') }}</span>
				@include('PressbooksNetworkCatalog::icons.chevron-right')
			</a>
		@endif
	</nav>
@endunless
