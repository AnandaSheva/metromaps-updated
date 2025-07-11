/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php"
  ],
  theme: {
    extend: {
      width: {
        '25': '100px',  // For metro logo
        '50': '200px',  // For diskominfo logo
      },
      maxWidth: {
        '2xl': '600px', // For content container
      },
      gap: {
        '10': '40px',   // For logos gap
      },
      margin: {
        '10': '40px',   // For bottom margin
      },
      height: {
        '20': '80px',   // For header height
        '18': '70px',   // For mobile header height
      },
      padding: {
        '45': '45px',   // For header padding
      },
      spacing: {
        '29': '29px',   // For nav menu height
      },
      fontSize: {
        '9': ['9px', '1.2'],     // For subtitle
      },
      colors: {
        'gray-custom': '#464646', // For text color
      },
      zIndex: {
        '1000': '1000',
        '2010': '2010',
        '2015': '2015',
      },
      screens: {
        'sm': '480px',
        'md': '768px', 
        'lg': '1024px',
        'xl': '1300px',
        '2xl': '1921px',
      }
    },
  },
  plugins: [],
}
