<div class="book-card">
	<div class="book-cover">
		<img src="{{ $book->cover }}" alt="{{ sprintf(__('%s book cover', 'pressbooks-network-catalog'), $book->title) }}" />
	</div>
	<div class="book-info">
		<h2><a href="{{ $book->url }}">{{ $book->title }}</a></h2>
		<p>
			<span>{{ $book->license }}&nbsp;</span>
			@if( $book->h5pCount)
				<span>
					<a href="{{ "$book->url/h5p-listing" }}">
						{{ sprintf(__('%d H5P Activities', 'pressbooks-network-catalog'), $book->h5pCount) . ' ' }}
					</a>
				</span>
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

			<p>
				<span>{{ __('Last updated:', 'pressbooks-network-catalog') }}</span> {{ \Illuminate\Support\Carbon::create($book->updatedAt)->format('d/m/Y') }}
			</p>
		</div>

		@if($book->description || $book->shortDescription)
			<div x-data="{showRead: true}">
                <div class="book-description line-clamp">
                    @if($book->description)
                        {!! $book->description !!}
                    @elseif($book->shortDescription)
                        {!! $book->shortDescription !!}
                    @endif
                </div>
                <a class="read-more" @click="window.toggleClass($el.previousElementSibling,'line-clamp'); showRead=!showRead " x-show="window.hasClampedText($el.previousElementSibling)" x-text="showRead? 'Read more' : 'Show less' "></a>
            </div>
        @endif
	</div>
</div>
