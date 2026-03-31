import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#6367FF',      // Picpic Primary
                'primary-dark': '#4B50E6',
                'primary-medium': '#8494FF',
                'primary-light': '#C9BEFF',
                secondary: '#FFFFFF',    // secondary cards (was F5F0FF/pink)
                accent: '#6367FF',       // Accent/highlight
                background: '#F8F7FF',   // Putih keunguan subtle
                'pink-light': '#FFDBFD', // Hanya untuk badge/chip kecil
                'text-primary': '#2D2B55',
                'text-secondary': '#6B6B8A',
            },
            boxShadow: {
                sm: '0 4px 15px rgba(99, 103, 255, 0.08)',
                md: '0 4px 15px rgba(99, 103, 255, 0.1)',
                lg: '0 8px 20px rgba(99, 103, 255, 0.15)',
                xl: '0 15px 35px rgba(99, 103, 255, 0.2)',
                'card-purple': '0 4px 15px rgba(99, 103, 255, 0.1)',
                'card-menu': '0 2px 12px rgba(99, 103, 255, 0.08)',
            },
        },
    },

    plugins: [forms],
};
