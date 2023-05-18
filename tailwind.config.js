const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  darkMode: 'class',
  safelist: [
    { pattern: /^list-/ }
  ],
  theme: {
      extend: {
          fontFamily: {
              sans: ['Nunito', ...defaultTheme.fontFamily.sans],
          },
          colors: {
              danger: colors.red,
              primary: colors.blue,
              success: colors.green,
              warning: colors.orange,
          }
      },
  },

  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
