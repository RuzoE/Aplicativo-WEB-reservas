/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      './resources/views/**/*.blade.php',
      './resources/js/**/*.js',
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
