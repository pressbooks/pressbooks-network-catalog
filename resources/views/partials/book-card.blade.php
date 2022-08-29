<div class="book-card">
	<div class="book-cover">
		<img src="{{ $book->cover }}" alt="{{ sprintf(__('%s book cover', 'pressbooks-network-catalog'), $book->title) }}" />
	</div>
	<div class="book-info">
		<h2>{{ $book->title }}</h2>
		<p>
			<span>{{ $book->license }}</span>
			@if( $book->h5pCount)
				<span>{{ sprintf(__('%d H5P Activites', 'pressbooks-network-catalog'), $book->h5pCount) }}</span>
			@endif
			<span>{{ $book->language }}</span>
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
				<span>{{ __('Last updated:', 'pressbooks-network-catalog') }}</span> {{ $book->updatedAt }}
			</p>
		</div>

		@if($book->description)
			<p class="book-description">
				{!! $book->description !!}
			</p>
		@endif
	</div>
</div>
