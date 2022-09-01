<nav class="pagination" role="navigation" aria-label="{{ __('Pagination navigation', 'pressbooks-network-catalog') }}">
	<ul class="page-list">
		<li class="page-item previous-page">
			@if(($request->pg ?? 1) > 1) {{-- TODO: improve this check? --}}
				<a
					href="{{ $request->fullUrlWithQuery(['pg' => ($request->pg ?? 1) - 1 ]) }}"
					rel="prev"
					aria-label="{{ __('Previous page', 'pressbooks-network-catalog') }}"
				>
					@include('PressbooksNetworkCatalog::icons.chevron-left')
				</a>
			@endif
		</li>

		@foreach([1, 2, 3, '...', 14] as $page)
			@if(is_string($page))
				<li class="page-item disabled" aria-disabled="true">
					<span>{{ $page }}</span>
				</li>
			@endif

			@if($page === (int) ($request->pg ?? 1))
				<li class="page-item active" aria-current="true">
					<span>{{ $page }}</span>
				</li>
			@else
				<li class="page-item">
					<a href="{{ $request->fullUrlWithQuery(['pg' => $page]) }}">{{ $page }}</a>
				</li>
			@endif
		@endforeach

		<li class="page-item next-page">
			<a
				href="{{ $request->fullUrlWithQuery(['pg' => ($request->pg ?? 1) + 1 ]) }}"
				rel="next"
				aria-label="{{ __('Next page', 'pressbooks-network-catalog') }}"
			>
				@include('PressbooksNetworkCatalog::icons.chevron-right')
			</a>
		</li>
	</ul>

	<div class="go-to-page">
		<span>{{ __('Go to page', 'pressbooks-network-catalog') }}</span>
		<div>
			<input type="text" name="pg" />
			<button type="submit">{{ __('Go', 'pressbooks-network-catalog') }}</button>
		</div>
	</div>
</nav>
