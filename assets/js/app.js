import Alpine from 'alpinejs';
import '../css/app.css';

window.Alpine = Alpine;

const form = document.getElementById('network-catalog-form');

form.addEventListener('submit', function(event) {
  const inputs = Array
    .from(event.target.getElementsByTagName('input'))
    .filter(input => ['search', 'pg'].includes(input.name));

  inputs
    .filter(input => input.value === '')
    .forEach(input => input.disabled = true);

  return true;
});

window.submitForm = () => {
  document.getElementById('apply-filters').click();
}

window.selectableFilters = ({open, items, selected}) => {
  return {
    open,
    items,
    selected,
    search: '',
    displayAmount: 10,
    toggle() {
      this.open ^= true;
    },
    empty() {
      return this.filteredItems().length === 0;
    },
    filteredItems() {
      return Object.entries(this.items)
        .filter(
          ([key, value]) => value.toLowerCase().includes(this.search.toLowerCase())
        ).slice(0, this.displayAmount);
    },
    showMore() {
      this.displayAmount += 10;
    },
    highlightSearch(value) {
      if (!this.search) {
        return value;
      }

      return value.replaceAll(
        new RegExp(`(${this.search.toLowerCase()})`, 'ig'),
        `<span class="font-bold">$1</span>`
      );
    }
  }
};

window.dropdown = ({selected, options}) => {
  return {
    open: false,
    selected: selected,
    options: options,
    toggle() {
      if (this.open) {
        return this.close()
      }

      this.$refs.button.focus()

      this.open = true
    },
    close(focusAfter) {
      if (! this.open) return

      this.open = false

      focusAfter && focusAfter.focus()
    }
  };
};

window.removeFilter = (filter) => {
  // TODO: Date filter
  const attr = filter === 'h5p' ? 'name' : 'value';
  document.querySelector(`input[${attr}="${filter}"]`).click();

  submitForm();
}

Alpine.start();

console.log('main.js - start');
