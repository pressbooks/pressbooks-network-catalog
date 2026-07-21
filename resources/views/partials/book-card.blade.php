<div class="book-card">
	<div class="book-cover">
		<a href="{{ $book->url }}" aria-hidden="true" tabindex="-1"><img src="{{ $book->cover }}" alt="{{ sprintf(__('%s book cover', 'pressbooks-network-catalog'), $book->title) }}" /></a>
	</div>
	<div class="book-info">
		<h2><a href="{{ $book->url }}">{!! $book->title !!}</a></h2>
		<p>
			<span>{{ pb_decode( $book->license ) }}&nbsp;</span>
			@if( $book->h5pCount)
				<span>
					<a href="{{ "$book->url/h5p-listing" }}">
						{{ sprintf(__('%d H5P Activities', 'pressbooks-network-catalog'), $book->h5pCount) }}
					</a>
				</span>&nbsp;
			@endif
			<span>&nbsp;{{ $book->language }}</span>
		</p>

		<div class="book-extra-info">
			@if($book->authors)
				<p>
					<span>{{ __('Author(s):', 'pressbooks-network-catalog') }}</span> {{ $book->authors }}
				</p>
			@endif

			@if($book->editors)
				<p>
					<span>{{ __('Editor(s):', 'pressbooks-network-catalog') }}</span> {{ $book->editors }}
				</p>
			@endif

			@if($book->subjects)
				<p>
					<span>{{ __('Subject(s):', 'pressbooks-network-catalog') }}</span> {{ $book->subjects }}
				</p>
			@endif

			@if($book->institutions)
				<p>
					<span>{{ __('Institution(s):', 'pressbooks-network-catalog') }}</span> {{ $book->institutions }}
				</p>
			@endif

			@if($book->publisher)
				<p>
					<span>{{ __('Publisher:', 'pressbooks-network-catalog') }}</span> {{ $book->publisher }}
				</p>
			@endif

			@if($book->publicationDate)
				<p>
					<span>{{ __('Publication date:', 'pressbooks-network-catalog') }}</span> {{ \Illuminate\Support\Carbon::create($book->publicationDate)->format('Y-m-d') }}
				</p>
			@endif

			<p>
				<span>{{ __('Last updated:', 'pressbooks-network-catalog') }}</span> {{ \Illuminate\Support\Carbon::create($book->updatedAt)->format('Y-m-d') }}
			</p>
		</div>

		@if($book->description || $book->shortDescription)
			<div x-data="{showRead: true}">
                <div class="book-description line-clamp" id="book-description-{{ $book->id }}">
                    @if($book->description)
                        {!! $book->description !!}
                    @elseif($book->shortDescription)
                        {!! pb_decode($book->shortDescription) !!}
                    @endif
                </div>
                <button type="button" class="read-more" @click="window.toggleClass($el.previousElementSibling,'line-clamp'); showRead=!showRead " x-show="window.hasClampedText($el.previousElementSibling)" :aria-expanded="!showRead" aria-controls="book-description-{{ $book->id }}" x-text="showRead? '{{ __( 'Read more', 'pressbooks-network-catalog' ) }}' : '{{ __( 'Show less', 'pressbooks-network-catalog' ) }}' "></button>
            </div>
        @endif
	</div>
</div>
