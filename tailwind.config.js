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
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#7C6BC4',
                'primary-dark': '#5A4DA0',
                'primary-light': '#F0EEFF',
                accent: '#9B8FD4',
                secondary: '#6B5BB8',
            },
        },
    },

    plugins: [forms],
};
