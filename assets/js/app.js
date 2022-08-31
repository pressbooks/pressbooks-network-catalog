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
