import barba from '@barba/core';

export default function fakeSpaTransition() {

  barba.init({
    preventRunning: true,
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

  document.getElementById('search').addEventListener('click', function () {
    extraFilters.search = document.querySelector('input[name="search"]').value || '';
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
      if (value !== 0) {
        params.push(`${key}=${value}`);
      }
    }

    if (params.length) {
      url += '?' + params.join('&');
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
