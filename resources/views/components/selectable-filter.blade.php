<div
	class="side-filter selectable"
	x-data='selectableFilters(@json([
    	'open' => $open ?? false,
    	'items' => $items,
    	'selected' => $selected ?? []
	]))'
>
	<button @click="toggle" :aria-expanded="open" type="button">
		<span>{{ $title }}</span>
		<span class="icon">
			@include('PressbooksNetworkCatalog::icons.chevron-down')
		</span>
	</button>
	<div x-show="open" x-cloak>
		@if($searchable ?? false)
			<label id="search-{{ $name }}-label" class="sr-only">{{ $title }}</label>
			<input
				type="search"
				x-model="search"
				placeholder="{{ __('Search', 'pressbooks-network-catalog') }}"
				aria-labelledby="search-{{ $name }}-label"
			/>
		@endif

		<ul>
			<template x-for="[key, value] in filteredItems()" :key="key">
				<li>
					<label>
						<span x-html="highlightSearch(value)"></span>
						<input
							type="checkbox"
							name="{{ $name }}[]"
							:value="key"
							:checked="selected.includes(key)"
						/>
					</label>
				</li>
			</template>
			<span x-show="empty()">{{ __('No available filters at the moment', 'pressbooks-network-catalog') }}</span>
		</ul>

		<button
			type="button"
			class="show-more"
			x-show="Object.entries(items).length > displayAmount"
			@click="showMore()"
		>
			{{ __('Show more +', 'pressbooks-network-catalog') }}
		</button>
	</div>
</div>
