import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography, daisyui],

    daisyui: {
        themes: [
            "light",
            "dark",
            "cupcake",
            "corporate",
            "business",
        ],
        darkTheme: "dark", // tema padrão para dark mode
        base: true, // aplica estilos base do DaisyUI
        styled: true, // inclui estilos dos componentes
        utils: true, // adiciona classes utilitárias
    },
};
