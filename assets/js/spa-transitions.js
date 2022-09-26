import barba from '@barba/core';
import Nanobar from 'nanobar';

export default function fakeSpaTransition() {

  const nanobar = new Nanobar( {
    id: 'loading-bar',
  } );

  let loadingInterval;

  barba.init({
    preventRunning: true,
    timeout: 10000, // 10 seconds timeout should be enough specially in slow networks? before barba triggers the location reload (default is 5 seconds)
    prevent: ({ el }) => el.classList && el.closest('header').classList.contains('header'),
    transitions: [{
      name: 'spa-transition',
      leave() {
        let progress = 0;
        loadingInterval = setInterval(() => {
          progress += 2;
          nanobar.go(progress);
        }, 100); // 200ms
      },
      enter() {
        nanobar.go(100);
        clearInterval(loadingInterval);
        barba.history.clear();
      }
    }]
  });

  function debounce(func, timeout = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        func.apply(this, args);
      }, timeout);
    };
  }

  const filters = {
    'subjects': [],
    'licenses': [],
    'institutions': [],
    'publishers': [],
  }

  const extraFilters = {
    'search': document.querySelector('input[name="search"]').value || '',
    'h5p': 0,
    'from': document.getElementById('updated_from').value || '',
    'to': document.getElementById('updated_to').value || '',
  }

  document.querySelector('input[name="h5p"]').addEventListener('click', function (event) {
      extraFilters.h5p = event.target.checked ? 1 : 0;
      barba.go(buildUrl());
  });

  document.getElementById('updated_from').addEventListener('change', function (event) {
    console.log(event.target.value);
    extraFilters.from = event.target.value;
    barba.go(buildUrl());
  });

  document.getElementById('updated_to').addEventListener('change', function (event) {
    extraFilters.to = event.target.value;
    barba.go(buildUrl());
  });

  function buildUrl() {
    let url = window.location.href.split('?')[0];
    let params = [];

    for (const [key, values] of Object.entries(filters)) {
      if (values.length > 0) {
        values.forEach((item) => {
          params.push(`${key}[]=${item}`);
        });
      }
    }

    for (const [key, value] of Object.entries(extraFilters)) {
      if (value !== 0 && value !== '') {
        params.push(`${key}=${value}`);
      }
    }

    if (params.length) {
      url += '?' + params.join('&');
      const orderingAndPaginationParams = new URLSearchParams(window.location.search);
      if (orderingAndPaginationParams.has('sort_by')) {
        url += '&sort_by=' + orderingAndPaginationParams.get('sort_by');
      }
      if (orderingAndPaginationParams.has('per_page')) {
        url += '&per_page=' + orderingAndPaginationParams.get('per_page');
      }
    }

    return url;
  }

  Object.keys(filters).forEach(filter => {
    Array.from(document.querySelectorAll(`input[name="${filter}[]"]`)).map((input) => {
      if (input.checked) {
        filters[filter].push(input.value);
      }
    });
    document.querySelectorAll(`input[name="${filter}[]"]`)
      .forEach(input => input.addEventListener('click', debounce((event) => {
        onCheck(event);
      })));

    function onCheck(event) {
      if (event.target.checked) {
        filters[filter].push(event.target.value);
      } else {
        filters[filter].splice(filters[filter].indexOf(event.target.value), 1);
      }
      const url = buildUrl();
      barba.go(url);
    }
  })

}
