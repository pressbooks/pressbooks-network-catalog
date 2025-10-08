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
    .filter(input => ['search', 'pg', 'published_from', 'published_to', 'updated_from', 'updated_to'].includes(input.name));

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

  // Validate date pairs (published and updated) using a small helper
  const validateDatePair = (fromName, toName) => {
    const fromEl = event.target.elements[fromName];
    const toEl = event.target.elements[toName];
    if (!fromEl || !toEl) return true;

    // clear any previous custom validity
    toEl.setCustomValidity('');

    if (!fromEl.value || !toEl.value) return true; // nothing to validate

    const fromDate = new Date(fromEl.value);
    const toDate = new Date(toEl.value);

    // basic validity check
    if (isNaN(fromDate) || isNaN(toDate)) {
      toEl.setCustomValidity('Please provide valid dates.');
      toEl.reportValidity();
      event.preventDefault();
      return false;
    }

    if (fromDate > toDate) {
      toEl.setCustomValidity('The "To" date must be greater than or equal to the "From" date.');
      toEl.reportValidity();
      event.preventDefault();
      return false;
    }

    return true;
  };

  if (!validateDatePair('published_from', 'published_to')) return false;
  if (!validateDatePair('updated_from', 'updated_to')) return false;

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
    if (!id) return;

    // Try to clear validity on the underlying input with the same name
    const underlying = document.querySelector(`input[name="${id}"]`);
    if (underlying) {
      underlying.setCustomValidity('');
      underlying.valid = true;
      // notify any listeners that the value/validity changed
      underlying.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // If a "_from" field changed, also clear the paired "_to" validity so users can revalidate
    if (id.endsWith('_from')) {
      const toName = id.replace('_from', '_to');
      const maybeTo = document.querySelector(`input[name="${toName}"]`);
      if (maybeTo) {
        maybeTo.setCustomValidity('');
        maybeTo.valid = true;
      }
    }
  });
});

window.submitForm = () => {
  document.getElementById('apply-filters').click();
}

// Toggle the "open" class on the hamburger menu
const headerToggle = document.querySelector('.js-header-nav-toggle');
if (headerToggle) {
  const headerNav = document.querySelector('.header__nav');
  headerToggle.addEventListener('click', () => {
    if (headerNav) headerNav.classList.toggle('header__nav--active');
  });
}


const pgElements = document.querySelectorAll('[name="pg"]');
pgElements.forEach(element => {
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

window.removeFilter = async (filter) => {
  if (!filter) return;

  let field = filter;
  let suffix = null;
  if (filter.includes(':')) {
    [field, suffix] = filter.split(':');
  }

  const clearInputValue = (selector) => {
    const el = document.querySelector(selector);
    if (!el) return false;

    if (el.tagName.toLowerCase() === 'duet-date-picker') {
      el.setAttribute('value', '');
    } else if ('value' in el) {
      el.value = '';
      el.dispatchEvent(new Event('change', { bubbles: true }));
    }
    return true;
  };

  // Handle date filters (publication_date:from, last_updated:to, etc.)
  if (suffix === 'from' || suffix === 'to') {
    let name;
    switch (field) {
      case 'publication_date':
        name = suffix === 'from' ? 'published_from' : 'published_to';
        break;
      case 'last_updated':
        name = suffix === 'from' ? 'updated_from' : 'updated_to';
        break;
      default:
        name = suffix; // legacy fallback
    }

    // Try to clear input or duet-date-picker
    const cleared =
      clearInputValue(`input[name="${name}"]`) ||
      clearInputValue(`duet-date-picker[identifier="${name}"]`);

    if (!cleared) {
      console.warn(`Could not find input or date-picker for ${name}`);
    }

  // Handle other filters
  } else {
    const attr = field === 'h5p' ? 'name' : 'value';
    const el = document.querySelector(`input[${attr}="${field}"]`);
    if (el) {
      el.click();
    } else {
      console.warn(`No input found for filter "${field}"`);
    }
  }

  // Debounce form submission slightly
  if (typeof submitForm === 'function') {
    await new Promise((r) => setTimeout(r, 100));
    submitForm();
  } else {
    console.error('submitForm() is not defined.');
  }
};


window.reset = () => {
  document.getElementById('network-catalog-form').reset();
  window.location.href = window.location.href.split('?')[0] + `${anchorIdRedirection}`;
}

Alpine.start();

console.log('PB Network Catalog - started');
