import Alpine from 'alpinejs';
import '../css/app.css';

window.Alpine = Alpine;

const falsy = (value) => {
  let values = [
    null,
    undefined,
    0,
    false,
    '',
    '0',
    'false'
  ];

  return values.includes(value);
}

document.addEventListener('alpine:init', () => {
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const search = urlParams.get('search');
  const h5p = urlParams.get('h5p');
  console.log(h5p);
  Alpine.store('filters', {
    search: search ? search : '',
    h5p: ! falsy(h5p),
    toggle: function(key) {
      this[key] ^= true;
      //TODO: build query string from filters
      if(key === 'h5p') {
        window.location.href = window.location.href.split('?')[0] + '?search=' + this.search + '&h5p=' + this[key];
      }
    }
  })

  console.log(Alpine.store('filters'));
});

window.selectableFilters = ({open}) => {
  return {
    open,
    visibility() {
      if (this.open) {
        return '';
      }

      return 'hidden';
    },
    toggle() {
      this.open ^= true;
    }
  }
}

Alpine.start();

console.log('main.js - start');
