const { fontFamily } = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './assets/js/*.js',
    './*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      maxWidth: {
        '8xl': '85rem',
      },
      fontFamily: {
        sans: ['Karla', ...fontFamily.sans],
      }
    },
  },
  plugins: [
  ]
};
