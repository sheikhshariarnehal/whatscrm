import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],
    darkMode: 'class',
    theme: {
        screens: {
            xs: '576px',
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1536px',
        },
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: 'var(--primary)',
                'primary-deep': 'var(--primary-deep)',
                'primary-mild': 'var(--primary-mild)',
                'primary-subtle': 'var(--primary-subtle)',
                error: 'var(--error)',
                'error-subtle': 'var(--error-subtle)',
                success: 'var(--success)',
                'success-subtle': 'var(--success-subtle)',
                info: 'var(--info)',
                'info-subtle': 'var(--info-subtle)',
                warning: 'var(--warning)',
                'warning-subtle': 'var(--warning-subtle)',
                neutral: 'var(--neutral)',
                'gray-50': 'var(--gray-50)',
                'gray-100': 'var(--gray-100)',
                'gray-200': 'var(--gray-200)',
                'gray-300': 'var(--gray-300)',
                'gray-400': 'var(--gray-400)',
                'gray-500': 'var(--gray-500)',
                'gray-600': 'var(--gray-600)',
                'gray-700': 'var(--gray-700)',
                'gray-800': 'var(--gray-800)',
                'gray-900': 'var(--gray-900)',
                'gray-950': 'var(--gray-950)',
            },
        },
    },
    plugins: [forms],
};
