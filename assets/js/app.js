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
    .filter(input => ['search', 'pg', 'from', 'to', 'published_from', 'published_to', 'updated_from', 'updated_to'].includes(input.name));

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

  // Validate each date pair separately (published and updated)
  const publishedFrom = event.target.elements['published_from'];
  const publishedTo = event.target.elements['published_to'];
  if (publishedFrom && publishedTo && publishedFrom.value && publishedTo.value) {
    if (new Date(publishedFrom.value) > new Date(publishedTo.value)) {
      publishedTo.setCustomValidity('The "To" date must be greater than or equal to the "From" date.');
      publishedTo.valid = false;
      publishedTo.reportValidity();
      event.preventDefault();
      return false;
    }
  }

  const updatedFrom = event.target.elements['updated_from'];
  const updatedTo = event.target.elements['updated_to'];
  if (updatedFrom && updatedTo && updatedFrom.value && updatedTo.value) {
    if (new Date(updatedFrom.value) > new Date(updatedTo.value)) {
      updatedTo.setCustomValidity('The "To" date must be greater than or equal to the "From" date.');
      updatedTo.valid = false;
      updatedTo.reportValidity();
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

// When any date picker changes, clear validity messages (no global date_field needed)
document.querySelectorAll('duet-date-picker').forEach(el => {
  el.addEventListener('duetChange', function(e) {
    const id = el.getAttribute('identifier') || '';
    // Clear any matching underlying input validity messages if present
    const maybePublishedTo = document.querySelector('input[name="published_to"]');
    if (maybePublishedTo) { maybePublishedTo.setCustomValidity(''); maybePublishedTo.valid = true; }
    const maybeUpdatedTo = document.querySelector('input[name="updated_to"]');
    if (maybeUpdatedTo) { maybeUpdatedTo.setCustomValidity(''); maybeUpdatedTo.valid = true; }
  });
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
  // Allow keys like 'publication_date:from' or 'publication_date:to'
  let field = filter;
  let suffix = null;
  if(filter.includes(':')) {
    [field, suffix] = filter.split(':');
  }

  // date keys are removed by clearing the corresponding input (now distinct names)
  if(suffix === 'from' || suffix === 'to') {
    let name = null;
    if(field === 'publication_date') {
      name = suffix === 'from' ? 'published_from' : 'published_to';
    } else if(field === 'last_updated') {
      name = suffix === 'from' ? 'updated_from' : 'updated_to';
    } else {
      // legacy fallback
      name = suffix;
    }
    const input = document.querySelector(`input[name="${name}"]`);
    if(input) {
      input.value = '';
      input.dispatchEvent(new Event('change'));
    } else {
      // fallback: try duet date-picker by identifier
      const identifier = name.replace('_', '_');
      const duet = document.querySelector(`duet-date-picker[identifier="${identifier}"]`);
      if(duet) {
        duet.setAttribute('value', '');
      }
    }
  } else {
    const attr = ['h5p'].includes(field) ? 'name' : 'value';
    const el = document.querySelector(`input[${attr}="${field}"]`);
    if(el) el.click();
  }
  submitForm();
}

window.reset = () => {
  document.getElementById('network-catalog-form').reset();
  window.location.href = window.location.href.split('?')[0] + `${anchorIdRedirection}`;
}

Alpine.start();

console.log('PB Network Catalog - started');
