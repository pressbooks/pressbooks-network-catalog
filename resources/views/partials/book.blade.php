<article style="margin: 2rem 0; display: flex; align-items: start; background-color: #ffffff; border: 1px solid #cccccc; padding: 1.5rem; max-width: none;">
	<div style="min-width: 300px;">
		<img src="{{ $book->cover }}" style="width: 100%; max-width: 300px;"/>
	</div>
	<div style="margin: 0 1.5rem;">
		<h2 style="text-align: left; text-transform: capitalize; font-size: 1.5rem; margin: 0">{{ $book->title }}</h2>
		<p style="margin: 0.25rem 0;">
			<span>{{ $book->license }}</span>
			| <span>{{ $book->h5p_count ?: 'No' }} H5P Activities</span>
			| <span>{{ $book->language }}</span>
		</p>

		<div style="margin-top: 2rem;">
			@if($book->authors)
				<p style="margin: 0.5rem 0;">
					<span style="font-weight: 600;">Author(s):</span> <span>{{ $book->authors }}</span>
				</p>
			@endif

			@if($book->editors)
				<p style="margin: 0.5rem 0;">
					<span style="font-weight: 600;">Editors(s):</span> <span>{{ $book->editors }}</span>
				</p>
			@endif

			@if($book->subjects)
				<p style="margin: 0.5rem 0;">
					<span style="font-weight: 600;">Subject(s):</span> <span>{{ $book->subjects }}</span>
				</p>
			@endif

			@if($book->institutions)
				<p style="margin: 0.5rem 0;">
					<span style="font-weight: 600;">Institution(s):</span> <span>{{ $book->institutions }}</span>
				</p>
			@endif

			@if($book->publisher)
				<p style="margin: 0.5rem 0;">
					<span style="font-weight: 600;">Publisher:</span> <span>{{ $book->publisher }}</span>
				</p>
			@endif

			<p style="margin: 0.5rem 0;">
				<span style="font-weight: 600;">Last updated:</span> <span>{{ $book->updated_at }}</span>
			</p>
		</div>

		<div style="margin-top: 2rem;">
			{{ $book->description }}
		</div>
	</div>
</article>
