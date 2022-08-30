@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
    ['title' => __('Subject', 'pressbooks-network-catalog'), 'items' => $subjects, 'searchable' => true]
)

@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
	['title' => __('License', 'pressbooks-network-catalog'), 'items' => $licenses]
)

{{-- TODO: add it back once we've defined the datepicker --}}
{{--<div class="side-filter" x-data="{open: false}">--}}
{{--	<button @click="open = !open" :aria-expanded="open">--}}
{{--		<span>{{ __('Last Updated', 'pressbooks-network-catalog') }}</span>--}}
{{--		<span>&#8964;</span>--}}
{{--	</button>--}}
{{--    <div x-cloak :class="!open && 'hidden'">--}}
{{--        <input type="date" name="from"/>--}}
{{--        <input type="date" name="to"/>--}}
{{--    </div>--}}
{{--</div>--}}

@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
	['title' => __('Institution', 'pressbooks-network-catalog'), 'items' => $institutions, 'searchable' => true]
)

@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
	['title' => __('Publisher', 'pressbooks-network-catalog'), 'items' => $publishers, 'searchable' => true]
)

<div class="side-filter checkbox">
	<button x-data @click="$store.filters.toggle('h5p')">
		<span>{{ __('Has H5P Activities', 'pressbooks-network-catalog') }}</span>
		<input
			id="h5p"
			name="h5p"
			type="checkbox"
			:checked="$store.filters.h5p"
			x-model="$store.filters.h5p"
			aria-labelledby="has-h5p-label"
		/>
	</button>
</div>

<button class="reset-filters">
    {{ __('Clear filters', 'pressbooks-network-catalog') }}
</button>
