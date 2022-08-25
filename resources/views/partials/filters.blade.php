<div class="side-filter" x-data="{open: false}">
	<h3>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Subject', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</h3>
	<ul x-cloak x-bind:class="!open && 'hidden'">
		@foreach($subjects as $key => $subject)
			<li>{{ $subject }}</li>
		@endforeach
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<h3>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('License', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</h3>
	<ul x-cloak x-bind:class="!open && 'hidden'">
		@foreach($licenses as $key => $license)
			<li>{{ $license }}</li>
		@endforeach
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<h3>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Last Updated', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</h3>
	<ul x-cloak x-bind:class="!open && 'hidden'">
		<li>No available filters at the moment</li>
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<h3>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Institution', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</h3>
	<ul x-cloak x-bind:class="!open && 'hidden'">
		@foreach($institutions as $institution)
			<li>{{ $institution }}</li>
		@endforeach
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<h3>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Publisher', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</h3>
	<ul x-cloak x-bind:class="!open && 'hidden'">
		<li>No available filters at the moment</li>
	</ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<h3>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('H5P Activites', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</h3>
	<ul x-cloak x-bind:class="!open && 'hidden'">
		<li>No available filters at the moment</li>
	</ul>
</div>
<button class="reset-filters">
	{{ __('Clear filters', 'pressbooks-network-catalog') }}
</button>
