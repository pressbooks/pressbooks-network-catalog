@php(get_header())

<main>
    <div class="hero" style="background-image: url({{$catalogBg}})">
        <div>
            <div class="hero-content">
                <h1>{!! wp_kses_post(get_the_title()) !!}</h1>
                <div>
                    {!! wp_kses_post(get_the_content()) !!}
                </div>
            </div>
        </div>
    </div>
    <form method="get" action="{{ $request->url() }}" id="network-catalog-form">
        <div class="network-catalog">
            <aside class="side-filters">
                @include('PressbooksNetworkCatalog::partials.sidebar-filters')
            </aside>

            <div data-barba="container" data-barba-namespace="home">
                @include('PressbooksNetworkCatalog::partials.search-box')

                <div class="book-cards">
					@include('PressbooksNetworkCatalog::components.pagination', ['placement' => 'Top'])

                    @forelse( $books as $book )
                        @include('PressbooksNetworkCatalog::partials.book-card')
                    @empty
                        <p>No books have been added to the catalog yet.</p>
                    @endforelse

					@include('PressbooksNetworkCatalog::components.pagination', ['placement' => 'Bottom'])
                </div>
            </div>
        </div>
    </form>
</main>

@php(get_footer())
