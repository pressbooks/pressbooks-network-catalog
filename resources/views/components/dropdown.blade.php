<div class="dropdown">
	<div
		x-data='dropdown({{ json_encode(['selected' => $request[$name] ?? $default, 'options' => $options]) }})'
		@keydown.escape.prevent.stop="close($refs.button)"
		@focusin.window="! $refs.panel.contains($event.target) && close()"
		x-id="['dropdown-button']"
	>
		<button
			class="trigger"
			:class="open && 'open'"
			x-ref="button"
			@click="toggle()"
			:aria-expanded="open"
			:aria-controls="$id('dropdown-button')"
			type="button"
			aria-label="{{ $label }}"
		>
			{{ $options[$request[$name]] ?? $options[$default] }}

            @include('PressbooksNetworkCatalog::icons.chevron-down')
		</button>

		<input type="hidden" name="{{ $name }}" :value="selected">

		<div
			x-ref="panel"
			x-show="open"
			x-cloak
			x-transition.origin.top.left
			@click.outside="close($refs.button)"
			:id="$id('dropdown-button')"
			class="content"
		>
			<template x-for="[key, value] in Object.entries(options)" :key="key">
				<button @click="selected = key; close($refs.button)">
					<span class="sr-only">{{ __('Select', 'pressbooks-network-catalog') }}</span>
					<span x-text="value"></span>
				</button>
			</template>
		</div>
	</div>
</div>
