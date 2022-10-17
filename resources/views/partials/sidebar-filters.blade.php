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

<div class="side-filter" x-data="{open: {{ !empty($request->from) || !empty($request->to) ? 'true' : 'false'}}}">
	<button @click="open = !open" :aria-expanded="open" type="button">
		<span>{{ __('Last Updated', 'pressbooks-network-catalog') }}</span>
		@include('PressbooksNetworkCatalog::icons.chevron-down')
	</button>
	<div id="last-updated-wrapper" x-cloak :class="!open && 'hidden'">
            <div>
                <label>From</label>
                <duet-date-picker identifier="updated_from" name="from" value="{{$request->from ?? ''}}"></duet-date-picker>
                <label>To</label>
                <duet-date-picker identifier="updated_to" name="to" value="{{$request->to ?? ''}}"></duet-date-picker>
            </div>
	</div>
</div>

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
		<span>{{ __('H5P Activities', 'pressbooks-network-catalog') }}</span>
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

<div class="buttons">
	<button id="apply-filters" class="submit-filters" type="submit">
		{{ __('Apply filters', 'pressbooks-network-catalog') }}
	</button>

	<button id="clear-filters" class="reset-filters" type="button" @click="window.reset()" x-data>
		{{ __('Clear filters', 'pressbooks-network-catalog') }}
	</button>
</div>
