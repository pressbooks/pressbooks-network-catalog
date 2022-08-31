<div class="side-filter selectable" x-data="selectableFilters({open: @json($open ?? false)})">
	<button @click="toggle" :aria-expanded="open" type="button">
		<span>{{ $title }}</span>
		<span>&#8964;</span>
	</button>
	<div x-cloak :class="visibility">
		@if($searchable ?? false)
			<input type="search" placeholder="{{ __('Search', 'pressbooks-network-catalog') }}" />
		@endif

		<ul>
			@forelse($items as $key => $item)
				<li data-key="{{ $key }}">{{ $item }}</li>
			@empty
				<span>{{ __('No available filters at the moment', 'pressbooks-network-catalog') }}</span>
			@endforelse
		</ul>

		<button type="button" class="show-more">Show more +</button>
	</div>
</div>
