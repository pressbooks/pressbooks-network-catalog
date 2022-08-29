<div class="side-filter" x-data="{open: false}">
	<span>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Subject', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</span>
    <ul x-cloak x-bind:class="!open && 'hidden'">
        @foreach($subjects as $key => $subject)
            <li>{{ $subject }}</li>
        @endforeach
    </ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<span>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('License', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</span>
    <ul x-cloak x-bind:class="!open && 'hidden'">
        @foreach($licenses as $key => $license)
            <li>{{ $license }}</li>
        @endforeach
    </ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<span>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Last Updated', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</span>
    <div x-cloak x-bind:class="!open && 'hidden'">
        <input type="date" name="from"/>
        <input type="date" name="to"/>
    </div>
</div>
<div class="side-filter" x-data="{open: false}">
	<span>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Institution', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</span>
    <ul x-cloak x-bind:class="!open && 'hidden'">
        @foreach($institutions as $institution)
            <li>{{ $institution }}</li>
        @endforeach
    </ul>
</div>
<div class="side-filter" x-data="{open: false}">
	<span>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('Publisher', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</span>
    <ul x-cloak x-bind:class="!open && 'hidden'">
        <li>No available filters at the moment</li>
    </ul>
</div>
<div class="side-filter" x-data="{open: $store.filters.h5p === true}">
	<span>
		<button x-on:click="open = !open" x-bind:aria-expanded="open">
			<span>{{ __('H5P Activites', 'pressbooks-network-catalog') }}</span>
			<span>&#8964;</span>
		</button>
	</span>
    <div x-cloak x-bind:class="!open && 'hidden'">
        <label for="h5p">
            {{ __('Has H5P activities', 'pressbooks-network-catalog') }}
            <input x-data id="h5p" type="checkbox" name="h5p" :checked="$store.filters.h5p === true"
                   x-model="$store.filters.h5p" @change="$store.filters.toggle('h5p')"/>
        </label>
    </div>
</div>
<button class="reset-filters">
    {{ __('Clear filters', 'pressbooks-network-catalog') }}
</button>
