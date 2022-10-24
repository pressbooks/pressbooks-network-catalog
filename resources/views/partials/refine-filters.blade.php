<div class="{{$class}}">
    <button type="button" class="filters-dropdown" id="menu-button" aria-expanded="true" aria-haspopup="true" @click="open = !open" :aria-expanded="open" type="button">
        <span>{{ __('Filters', 'pressbooks-network-catalog') }}</span>
        <!-- Heroicon name: filter -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
    </button>
    @include('PressbooksNetworkCatalog::components.dropdown', [
        'label' => __('Results per page', 'pressbooks-network-catalog'),
        'name' => 'per_page',
        'default' => $request->per_page ?? 10,
        'options' => [
            10 => sprintf(__('%d results', 'pressbooks-network-catalog'), 10),
            20 => sprintf(__('%d results', 'pressbooks-network-catalog'), 20),
            50 => sprintf(__('%d results', 'pressbooks-network-catalog'), 50),
        ]
    ])

    @include('PressbooksNetworkCatalog::components.dropdown', [
        'label' => __('Sort by', 'pressbooks-network-catalog'),
        'name' => 'sort_by',
        'default' => $request->sort_by ?? 'last_updated',
        'dropdown_class' => 'sort',
        'options_prefix' => __('Sort by', 'pressbooks-network-catalog'),
        'options' => [
            'last_updated' => __('Recently updated', 'pressbooks-network-catalog'),
            'title' => __('Title (A-Z)', 'pressbooks-network-catalog'),
        ]
    ])
</div>
