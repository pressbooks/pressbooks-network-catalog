<div class="side-filter" x-data="{open: false}">
	<button x-on:click="open = !open">
		<h3>{{ __('Subject', 'pressbooks-network-catalog') }}</h3>
		<span>&#8964;</span>
	</button>
	<ul :class="open ? '' : 'hidden'">
		@foreach($subjects as $key => $subject)
			<li>{{ $subject }}</li>
		@endforeach
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<button x-on:click="open = !open">
		<h3>{{ __('License', 'pressbooks-network-catalog') }}</h3>
		<span>&#8964;</span>
	</button>
	<ul :class="open ? '' : 'hidden'">
		@foreach($licenses as $key => $license)
			<li>{{ $license }}</li>
		@endforeach
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<button x-on:click="open = !open">
		<h3>{{ __('Last Updated', 'pressbooks-network-catalog') }}</h3>
		<span>&#8964;</span>
	</button>
	<ul :class="open ? '' : 'hidden'">
		<li>No available filters at the moment</li>
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<button x-on:click="open = !open">
		<h3>{{ __('Institution', 'pressbooks-network-catalog') }}</h3>
		<span>&#8964;</span>
	</button>
	<ul :class="open ? '' : 'hidden'">
		<li>No available filters at the moment</li>
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<button x-on:click="open = !open">
		<h3>{{ __('Publisher', 'pressbooks-network-catalog') }}</h3>
		<span>&#8964;</span>
	</button>
	<ul :class="open ? '' : 'hidden'">
		<li>No available filters at the moment</li>
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<button x-on:click="open = !open">
		<h3>{{ __('H5P Activites', 'pressbooks-network-catalog') }}</h3>
		<span>&#8964;</span>
	</button>
	<ul :class="open ? '' : 'hidden'">
		<li>No available filters at the moment</li>
	</ul>
</div>
<button class="reset-filters">
	{{ __('Clear filters', 'pressbooks-network-catalog') }}
</button>
