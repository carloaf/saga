/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                'saga-green': {
                    50: '#f0fdf4',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                },
            },
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
