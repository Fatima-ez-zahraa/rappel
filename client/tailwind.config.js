/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // Logo-inspired refined color palette
        brand: {
          50: '#f4f6fb',
          100: '#e9edf7',
          200: '#c8d2eb',
          300: '#a7b7df',
          400: '#6581c7',
          500: '#234baeb', // Mid-navy but keep a primary role
          600: '#1e4093',
          700: '#19357b',
          800: '#142a62',
          900: '#0E1648', // Logo Navy
          950: '#0a1036',
        },
        accent: {
          50: '#f6fdf4',
          100: '#edf9e9',
          200: '#d1f1c7',
          300: '#b6e9a5',
          400: '#80d961',
          500: '#7CCB63', // Logo Green
          600: '#70b759',
          700: '#568c44',
          800: '#436d35',
          900: '#37592b',
        },
        navy: {
          50: '#f5f6f8',
          100: '#eceef2',
          200: '#cfd4de',
          300: '#b1bac9',
          400: '#7786a0',
          500: '#3d5277',
          600: '#374a6b',
          700: '#2e3d59',
          800: '#253147',
          900: '#0E1648', // Logo Navy exact
          950: '#0a1036',
        },
        surface: {
          glass: 'rgba(255, 255, 255, 0.7)',
          dark: 'rgba(15, 23, 42, 0.8)',
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['Outfit', 'Inter', 'sans-serif'],
      },
      boxShadow: {
        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
        'premium': '0 20px 50px -12px rgba(0, 0, 0, 0.08)',
        'glow': '0 0 20px rgba(16, 185, 129, 0.25)',
        'inner-light': 'inset 0 1px 0 0 rgba(255, 255, 255, 0.1)',
      },
      animation: {
        'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
        'fade-in': 'fadeIn 0.5s ease-out forwards',
        'scale-in': 'scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards',
        'float': 'float 6s ease-in-out infinite',
        'shimmer': 'shimmer 2.5s infinite linear',
      },
      keyframes: {
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(30px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        scaleIn: {
          '0%': { opacity: '0', transform: 'scale(0.95)' },
          '100%': { opacity: '1', transform: 'scale(1)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-20px)' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-1000px 0' },
          '100%': { backgroundPosition: '1000px 0' },
        },
      }
    },
  },
  plugins: [],
}
