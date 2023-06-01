import Alpine from 'alpinejs';
import { DuetDatePicker } from "@duetds/date-picker/custom-element";
customElements.define("duet-date-picker", DuetDatePicker);

window.Alpine = Alpine;

const form = document.getElementById('network-catalog-form');

const anchorIdRedirection = '#catalog';

const mobileBreakpoint = 768;

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

  const lastUpdateInputs = inputs
    .filter(input => input.name === 'from' || input.name === 'to');

  if(lastUpdateInputs.length === 2) {
    if(new Date(lastUpdateInputs[0].value) > new Date(lastUpdateInputs[1].value)) {
      let dateToInput = document.querySelector('input[id="updated_to"]');
      dateToInput.setCustomValidity('The "To" date must be greater than or equal to the "From" date.');
      dateToInput.valid = false;
      dateToInput.reportValidity();
      event.preventDefault();
      return false;
    }
  }

	// disable duplicated filters according to screen size to avoid duplicated parameters
	// this is needed because we have two sets of filters, one for mobile and one for desktop because of design constraints
	const filtersMobile = document.querySelectorAll('.order-mobile select');
	const filtersDesktop = document.querySelectorAll('.order-desktop select');
	const searchMobile = document.querySelector('.mobile-bar input');
	const searchDesktop = document.querySelector('.results input');
	if(window.innerWidth > mobileBreakpoint) {
		filtersMobile.forEach(el => {
			el.disabled = true;
		});
		searchMobile.disabled = true;
	} else {
		filtersDesktop.forEach(el => {
			el.disabled = true;
		});
		searchDesktop.disabled = true;
	}

  return true;
});

const datepicker = document.getElementsByName('to')[0];
datepicker.addEventListener('duetChange', function(event) {
  let dateToInput = document.querySelector('input[id="updated_to"]');
  dateToInput.setCustomValidity('');
  dateToInput.valid = true;
})

window.submitForm = () => {
  document.getElementById('apply-filters').click();
}

// Toggle the "open" class on the hamburger menu
document.querySelector('.js-header-nav-toggle').addEventListener('click', () => {
  document.querySelector('.header__nav').classList.toggle('header__nav--active');
});


document.getElementsByName('pg').forEach(element => {
  element.addEventListener('change', function(event) {
    const pageRegex = /pg=\d+/;
    const pageParam = `pg=${event.target.value}`;
    const currentSearch = window.location.search;
    const url = window.location.href.split('?')[0];

    if (currentSearch.match(pageRegex)) {
      window.location.href = `${url}${currentSearch.replace(pageRegex, pageParam)}${anchorIdRedirection}`;

      return;
    }

    if (! currentSearch) {
      window.location.href = `${url}?${pageParam}${anchorIdRedirection}`;

      return;
    }

    window.location.href = `${url}${currentSearch}&${pageParam}${anchorIdRedirection}`;
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

window.changeOnSelect = (event) => {
  if(event.target.closest('.order-mobile')) { //disable desktop/mobile select to avoid duplicated parameters
    const select = document.querySelector('.order-desktop select');
    select.disabled = true;
  } else {
    const select = document.querySelector('.order-mobile select');
    select.disabled = true;
  }
  submitForm();
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
  window.location.href = window.location.href.split('?')[0] + `${anchorIdRedirection}`;
}

Alpine.start();

console.log('PB Network Catalog - started');
