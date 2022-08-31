const { fontFamily } = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './assets/js/*.js',
    './*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        'pb-blue': '#F3F9FA',
        'pb-dark-blue': '#00243a',
        'pb-red' : '#BB2026'
      },
      maxWidth: {
        '8xl': '90rem',
      }
    },
  },
  plugins: [
  ]
};
