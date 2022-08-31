@php(get_header())

<main>
    <div class="hero">
        <div>
            <div class="hero-sidebar"></div>
            <div class="hero-content">
                <h1>{{ get_the_title() }}</h1>
                <div>
                    {!! wp_kses_post(get_the_content()) !!}
                </div>
            </div>
        </div>
    </div>
    <form method="get" action="{{ $request->url() }}">
        <div class="network-catalog">
            <aside class="side-filters">
                @include('PressbooksNetworkCatalog::partials.sidebar-filters')
            </aside>

            <div>
                @include('PressbooksNetworkCatalog::partials.search-box')

                <div class="book-cards">
					@include('PressbooksNetworkCatalog::components.pagination')

                    @forelse( $books as $book )
                        @include('PressbooksNetworkCatalog::partials.book-card')
                    @empty
                        <p>No books have been added to the catalog yet.</p>
                    @endforelse

					@include('PressbooksNetworkCatalog::components.pagination')
                </div>
            </div>
        </div>
    </form>
</main>

@php(get_footer())
