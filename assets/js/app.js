import Alpine from 'alpinejs';
import '../css/app.css';

window.Alpine = Alpine;

const form = document.getElementById('network-catalog-form');

form.addEventListener('submit', function(event) {
  const inputs = Array
    .from(event.target.getElementsByTagName('input'))
    .filter(input => ['search', 'page'].includes(input.name));

  inputs
    .filter(input => input.value === '')
    .forEach(input => input.disabled = true);

  return true;
});

window.selectableFilters = ({open, items, selected}) => {
  return {
    open,
    items,
    selected,
    search: '',
    displayAmount: 10,
    visibility() {
      if (this.open) {
        return '';
      }

      return 'hidden';
    },
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
        '<span class="font-bold">$1</span>'
      )
    }
  }
}

window.removeFilter = (filter) => {
  // TODO: Date filter
  const attr = filter === 'h5p' ? 'name' : 'value';
  document.querySelector(`input[${attr}="${filter}"]`).click();

  document.getElementById('network-catalog-form').submit();
}

Alpine.start();

console.log('main.js - start');
