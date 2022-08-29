import Alpine from 'alpinejs';
import '../css/app.css';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {

  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const search = urlParams.get('search');
  const h5p = urlParams.get('h5p');
  console.log(h5p);
  Alpine.store('filters', {
    search: search ? search : '',
    h5p: !(h5p === null || h5p === false),
    toggle: function(key) {
      const value = this[key];
      this[key] = this[key] ? 0 : 1;
      //TODO: build query string from filters
      if(key === 'h5p') {
        window.location.href = window.location.href.split('?')[0] + '?search=' + this.search + '&h5p=' + value;
      }
    }
  })

  console.log(Alpine.store('filters'));
})

Alpine.start();

console.log('main.js - start');
