/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#A89070',
        'primary-dark': '#8B6E54',
      }
    },
    fontFamily: {
    'serif': ['Monsterrat', 'serif'],
    'sans': ['DM Sans', 'sans-serif'],
    },
  },
  plugins: [],
}