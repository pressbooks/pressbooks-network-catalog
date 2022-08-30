@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
    ['title' => __('Subject', 'pressbooks-network-catalog'), 'items' => $subjects, 'searchable' => true]
)

@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
	['title' => __('License', 'pressbooks-network-catalog'), 'items' => $licenses]
)

<div class="side-filter" x-data="{open: false}">
	<button @click="open = !open" :aria-expanded="open">
		<span>{{ __('Last Updated', 'pressbooks-network-catalog') }}</span>
		<span>&#8964;</span>
	</button>
    <div x-cloak :class="!open && 'hidden'">
        <input type="date" name="from"/>
        <input type="date" name="to"/>
    </div>
</div>

@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
	['title' => __('Institution', 'pressbooks-network-catalog'), 'items' => $institutions, 'searchable' => true]
)

@include(
    'PressbooksNetworkCatalog::components.selectable-filter',
	['title' => __('Publisher', 'pressbooks-network-catalog'), 'items' => $publishers, 'searchable' => true]
)

<div class="side-filter" x-data="{open: $store.filters.h5p === true}">
	<button @click="open = !open" :aria-expanded="open">
		<span>{{ __('H5P Activites', 'pressbooks-network-catalog') }}</span>
		<span>&#8964;</span>
	</button>
    <div x-cloak :class="!open && 'hidden'">
        <label for="h5p">
            {{ __('Has H5P activities', 'pressbooks-network-catalog') }}
            <input x-data id="h5p" type="checkbox" name="h5p" value="1"/>
        </label>
    </div>
</div>
<button class="reset-filters" type="reset">
    {{ __('Clear filters', 'pressbooks-network-catalog') }}
</button>
