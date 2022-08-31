<div class="side-filter selectable"
     x-data='selectableFilters({open: @json($open ?? false), items: @json($items), selected: @json($selected ?? [])})'>
    <button @click="toggle" :aria-expanded="open" type="button">
        <span>{{ $title }}</span>
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
</svg>
        </span>
    </button>
    <div x-cloak :class="visibility">
        @if($searchable ?? false)
            <label id="search-{{ $name }}-label" class="sr-only">{{ $title }}</label>
            <input type="search" x-model="search" placeholder="{{ __('Search', 'pressbooks-network-catalog') }}"
                   aria-labelledby="search-{{ $name }}-label"/>
        @endif

        <ul>
            <template x-for="[key, value] in filteredItems()" :key="key">
                <li>
                    <label>
                        <span x-html="highlightSearch(value)"></span>
                        <input
                            type="checkbox"
                            name="{{ $name }}[]"
                            :value="key"
                            :checked="selected.includes(key)"
                        />
                    </label>
                </li>
            </template>
            <span x-show="empty()">{{ __('No available filters at the moment', 'pressbooks-network-catalog') }}</span>
        </ul>

        <button x-show="Object.entries(items).length > displayAmount" @click="showMore()" type="button"
                class="show-more">Show more +
        </button>
    </div>
</div>
