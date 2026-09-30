import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // System stacks — no external font CDN.
                sans: [
                    'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont',
                    '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', '"Noto Sans"',
                    'sans-serif', '"Apple Color Emoji"', '"Segoe UI Emoji"',
                ],
                serif: ['ui-serif', 'Georgia', 'Cambria', '"Times New Roman"', 'Times', 'serif'],
            },

            colors: {
                // Brass / thread accent — the brand colour.
                brand: {
                    50:  '#fdf8f1',
                    100: '#f9ecd8',
                    200: '#f1d5ab',
                    300: '#e6b774',
                    400: '#dd9b4a',
                    500: '#cf8230',
                    600: '#b56824',
                    700: '#974f20',
                    800: '#7c4020',
                    900: '#67361e',
                    950: '#3a1c0e',
                },
            },

            boxShadow: {
                card: '0 1px 2px 0 rgba(28,25,23,0.04), 0 12px 28px -12px rgba(28,25,23,0.14)',
                'card-hover': '0 2px 6px 0 rgba(28,25,23,0.06), 0 26px 50px -18px rgba(28,25,23,0.28)',
            },

            borderRadius: {
                '4xl': '2rem',
            },

            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'none' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },

            animation: {
                'fade-up': 'fade-up 0.6s ease both',
                'fade-in': 'fade-in 0.8s ease both',
                float: 'float 7s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
