@php( get_header() )

<main class="network-catalog">
	<aside class="side-filters">
		@include('PressbooksNetworkCatalog::partials.filters')
	</aside>

	<div>
		@include('PressbooksNetworkCatalog::partials.search')

		<div class="book-cards">
			@forelse( $books as $book )
				@include('PressbooksNetworkCatalog::partials.book')
			@empty
				<p>No books have been added to the catalog yet.</p>
			@endforelse
		</div>
	</div>
</main>

@php( get_footer() )
