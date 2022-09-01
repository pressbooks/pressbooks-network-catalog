<div class="dropdown">
	<div
		x-data="dropdown()"
		@keydown.escape.prevent.stop="close($refs.button)"
		@focusin.window="! $refs.panel.contains($event.target) && close()"
		x-id="['dropdown-button']"
	>
		<button
			x-ref="button"
			@click="toggle()"
			:aria-expanded="open"
			:aria-controls="$id('dropdown-button')"
			type="button"
			aria-label="{{ $label }}"
		>
			{{ $options[$request[$name] ?? $default] }}

			<span>
				@include('PressbooksNetworkCatalog::icons.chevron-down')
			</span>
		</button>

		<div
			x-ref="panel"
			x-show="open"
			x-cloak
			x-transition.origin.top.left
			@click.outside="close($refs.button)"
			:id="$id('dropdown-button')"
			class="content"
		>
			@foreach($options as $key => $value)
				<a href="{{ $request->fullUrlWithQuery([$name => $key]) }}">
					<span class="sr-only">{{ __('Select', 'pressbooks-network-catalog') }}</span>
					{{ $value }}
				</a>
			@endforeach
		</div>
	</div>
</div>
