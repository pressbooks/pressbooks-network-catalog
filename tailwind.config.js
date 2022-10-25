const { fontFamily } = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './assets/js/*.js',
    './*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Karla', ...fontFamily.sans],
      },
      maxWidth: {
        '8xl': '85rem',
      },
      screens: {
        'xl': '1440px',
      }
    },
  },
  plugins: [
  ]
};
