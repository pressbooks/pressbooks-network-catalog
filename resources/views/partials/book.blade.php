<div class="book-card">
	<div class="book-cover">
		<img src="{{ $book->cover }}" />
	</div>
	<div class="book-info">
		<h2>{{ $book->title }}</h2>
		<p>
			<span>{{ $book->license }}</span>
			<span>{{ $book->h5p_count ?: 'No' }} H5P Activities</span>
			<span>{{ $book->language }}</span>
		</p>

		<div class="book-extra-info">
			@if($book->authors)
				<p>
					<span>Author(s):</span> {{ $book->authors }}
				</p>
			@endif

			@if($book->editors)
				<p>
					<span>Editors(s):</span> {{ $book->editors }}
				</p>
			@endif

			@if($book->subjects)
				<p>
					<span>Subject(s):</span> {{ $book->subjects }}
				</p>
			@endif

			@if($book->institutions)
				<p>
					<span>Institution(s):</span> {{ $book->institutions }}
				</p>
			@endif

			@if($book->publisher)
				<p>
					<span>Publisher:</span> {{ $book->publisher }}
				</p>
			@endif

			<p>
				<span>Last updated:</span> {{ $book->updated_at }}
			</p>
		</div>

		<p class="book-description">
			{{ $book->description }}
		</p>
	</div>
</div>
