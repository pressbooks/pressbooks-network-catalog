@php(get_header())

<main>
	<div class="hero" style="background-image: url({{$catalogBg}})">
		<div>
			<div class="hero-content">
				<h1 class="catalog-header">{!! wp_kses_post(get_the_title()) !!}</h1>
				<div class="catalog-content">
					{!! the_content() !!}
				</div>
			</div>
		</div>
	</div>
	<div id="catalog">
		<form method="get" action="{{ $request->url() }}#catalog" id="network-catalog-form">
			<section class="mobile-bar search-box" aria-label="{{ __('Search', 'pressbooks-network-catalog') }}">
				@include('PressbooksNetworkCatalog::components.search-input')
			</section>
			<div class="network-catalog">
				<section class="side-filters" aria-label="{{ __('Filters', 'pressbooks-network-catalog') }}">
					@include('PressbooksNetworkCatalog::partials.sidebar-filters')
				</section>

				<div>
					<section aria-label="{{ __('Search, pagination, and sorting', 'pressbooks-network-catalog') }}">
						@include('PressbooksNetworkCatalog::partials.search-box')
					</section>

					@include('PressbooksNetworkCatalog::components.pagination', ['placement' => 'top'])

					<section class="book-cards" aria-label="{{ __('Book list', 'pressbooks-network-catalog') }}">
						@forelse( $books as $book )
							@include('PressbooksNetworkCatalog::partials.book-card')
						@empty
							@if($catalogHasBooks)
								<p>{{ __('Sorry, no results were found. You may want to check your spelling, use alternative terms with similar meanings, or clear one or more filters.', 'pressbooks-network-catalog') }}</p>
							@else
								<p>{{ __('No books have been added to the catalog yet.', 'pressbooks-network-catalog') }}</p>
							@endif
						@endforelse
					</section>

					@include('PressbooksNetworkCatalog::components.pagination', ['placement' => 'bottom'])
				</div>
			</div>
		</form>
	</div>
</main>

@php(get_footer())
