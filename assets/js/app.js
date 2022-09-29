import Alpine from 'alpinejs';
import '../css/app.css';
import PbDatePicker from "./datepicker";
import fakeSpaTransition from "./spa-transitions";

window.Alpine = Alpine;

const form = document.getElementById('network-catalog-form');

form.addEventListener('submit', function (event) {
  const inputs = Array
    .from(event.target.elements)
    .filter(input => ['search', 'pg', 'from', 'to'].includes(input.name));

  // disable pagination when submitting the form since we want to reset it
  inputs
    .filter(input => input.name === 'pg')
    .forEach(input => input.disabled = true);

  // disable search input that is not visible
  inputs
    .filter(input => input.name === 'search')
    .filter(input => input.offsetWidth === 0 && input.offsetHeight === 0)
    .forEach(input => input.disabled = true);

  // disable all inputs that are empty
  inputs
    .filter(input => input.value === '')
    .forEach(input => input.disabled = true);

  return true;
});

window.submitForm = () => {
  document.getElementById('apply-filters').click();
}

// Toggle the "open" class on the hamburger menu
document.querySelector('.js-header-nav-toggle').addEventListener('click', () => {
  document.querySelector('.header__nav').classList.toggle('header__nav--active');
});

document.getElementsByName('pg').forEach(element => {
  element.addEventListener('change', function(event) {
    const search = new URLSearchParams(
        window.location.search
    );

    const params = {...Object.fromEntries(search.entries()), pg: event.target.value};

    const newSearch = new URLSearchParams();

    Object.keys(params).forEach(function(key) {
      newSearch.set(key, params[key]);
    });

    window.location.href = window.location.href.split('?')[0] + `?${newSearch.toString()}`;
  });
});

window.selectableFilters = ({open, items, selected}) => {
  return {
    open,
    items,
    selected,
    search: '',
    displayAmount: 10,
    toggle() {
      this.open = ! this.open;
    },
    empty() {
      return this.filteredItems().length === 0;
    },
    filteredItems() {
      return Object.entries(this.items)
        .filter(
          ([key, value]) => value && value.toLowerCase().includes(this.search.toLowerCase())
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
        return this.close();
      }

      this.$refs.button.focus();

      this.open = true;
    },
    close(focusAfter) {
      if (!this.open) {
        return;
      }

      this.open = false;

      focusAfter && focusAfter.focus();
    }
  };
};

window.hasClampedText = (element) => {
  return element.offsetHeight < element.scrollHeight || element.offsetWidth < element.scrollWidth;
}

window.toggleClass = (element, className) => {
  element.classList.toggle(className);
}

window.removeFilter = (filter) => {
  const attr = ['h5p'].includes(filter) ? 'name' : 'value';
  if(filter === 'from' || filter === 'to') {
    const el = document.querySelector(`input[name="${filter}"]`);
    el.value = '';
    el.dispatchEvent(new Event('change'));
  } else {
    const el = document.querySelector(`input[${attr}="${filter}"]`);
    el.click();
  }
  submitForm();
}

window.reset = () => {
  document.getElementById('network-catalog-form').reset();
  window.location.href = window.location.href.split('?')[0];
}

PbDatePicker();

Alpine.start();

console.log('main.js - start');
