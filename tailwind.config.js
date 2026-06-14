/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./public/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'bg-main': '#f8fafc',
        'bg-card': '#ffffff',
        'bg-sidebar': '#0f172a',
        'bg-sidebar-hover': '#1e293b',
        primary: {
          DEFAULT: 'hsl(243, 75%, 59%)',
          light: 'hsl(243, 75%, 95%)',
          dark: 'hsl(243, 75%, 45%)',
        },
        secondary: 'hsl(217, 91%, 60%)',
        success: {
          DEFAULT: 'hsl(142, 71%, 45%)',
          light: 'hsl(142, 71%, 95%)',
        },
        danger: {
          DEFAULT: 'hsl(350, 89%, 60%)',
          light: 'hsl(350, 89%, 95%)',
        },
        warning: {
          DEFAULT: 'hsl(38, 92%, 50%)',
          light: 'hsl(38, 92%, 95%)',
        },
        'text-main': '#0f172a',
        'text-muted': '#64748b',
        'text-light': '#94a3b8',
        'border-color': '#e2e8f0',
      },
      fontFamily: {
        heading: ['Outfit', 'sans-serif'],
        body: ['Inter', 'sans-serif'],
      },
      borderRadius: {
        lg: '16px',
        md: '10px',
        sm: '6px',
      },
      boxShadow: {
        sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        md: '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05)',
        lg: '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05)',
      }
    },
  },
  plugins: [],
}
