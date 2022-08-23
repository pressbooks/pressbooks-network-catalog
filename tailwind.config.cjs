const { fontFamily } = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './*.php',
    './resources/views/*.php',
    './resources/views/partials/*.php',
  ],
  theme: {
    extend: {
      fontFamily: {
      },
      colors: {
        'pb-blue': '#F3F9FA',
        'pb-dark-blue': '#00243a',
        'pb-red' : '#BB2026'
      }
    },
  },
  plugins: [
  ]
};
