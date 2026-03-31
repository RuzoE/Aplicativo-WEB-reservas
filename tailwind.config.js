/** @type {import('tailwindcss').Config} */
module.exports = {
    important: true,
    content: [
      './resources/views/**/*.blade.php',
      './resources/js/**/*.js',
      './resources/js/**/*.vue',
    ],
    theme: {
      extend: {
        colors: {
          'primary-start': '#4f46e5',
          'primary-end':   '#3b82f6',
        },
      },
    },
    plugins: [],
  }
