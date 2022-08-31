@include(
    'PressbooksNetworkCatalog::components.selectable-filter', [
        'title' => __('Subject', 'pressbooks-network-catalog'),
        'items' => $subjects,
        'searchable' => true,
        'name'=> 'subjects',
        'open' => $request->has('subjects'),
        'selected' => $request->get('subjects'),
	]
)

@include(
    'PressbooksNetworkCatalog::components.selectable-filter', [
        'title' => __('License', 'pressbooks-network-catalog'),
        'items' => $licenses,
        'name' => 'licenses',
        'open' => $request->has('licenses'),
        'selected' => $request->get('licenses'),
	]
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
    'PressbooksNetworkCatalog::components.selectable-filter', [
        'title' => __('Institution', 'pressbooks-network-catalog'),
        'items' => $institutions,
        'searchable' => true,
        'name' => 'institutions',
        'open' => $request->has('institutions'),
        'selected' => $request->get('institutions'),
	]
)

@include(
    'PressbooksNetworkCatalog::components.selectable-filter', [
        'title' => __('Publisher', 'pressbooks-network-catalog'),
        'items' => $publishers,
        'searchable' => true,
        'name' => 'publishers',
        'open' => $request->has('publishers'),
        'selected' => $request->get('publishers'),
	]
)

<div class="side-filter checkbox">
	<label>
		<span>{{ __('Has H5P Activities', 'pressbooks-network-catalog') }}</span>
		<input
			id="h5p"
			name="h5p"
			type="checkbox"
			value="1"
			@if($request->has('h5p')) {{-- This can be replaced by @checked once we move to illuminate 9.x --}}
				checked
			@endif
		/>
	</label>
</div>

<button class="submit-filters" type="submit">
    {{ __('Filter books', 'pressbooks-network-catalog') }}
</button>

<button class="reset-filters" type="reset">
    {{ __('Clear filters', 'pressbooks-network-catalog') }}
</button>
