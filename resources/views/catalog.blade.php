@php( get_header() )

<main class="app">
	<aside id="filters" style="padding: 2rem 0; min-width: 300px;">
		@include('PressbooksNetworkCatalog::partials.filters')
	</aside>

	<div style="margin-left: 2rem; flex: 1;">
		<div style="padding: 2rem 0;">
			@include('PressbooksNetworkCatalog::partials.search')
		</div>

		<div id="books">
			@forelse( $books as $book )
				@include('PressbooksNetworkCatalog::partials.book')
			@empty
				<p>No books have been added to the catalog yet.</p>
			@endforelse
		</div>
	</div>
</main>

@php( get_footer() )
