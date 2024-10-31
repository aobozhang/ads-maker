import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    safelist: [
        "bg-black",
        "bg-white",
        "!z-50",
        {
            pattern: /(leading)-(.+)/,
        },
        {
            pattern: /(bg|text)-(.+)-([1-9][0|5]0)/,
        },
        {
            pattern: /(bg|text)-(.+)-([1-9][0|5]0)(\/\d+)/,
        },
        {
            pattern: /(bg|text)-(black|white)(\/\d+)/,
        },
    ],
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                mbti: "linear-gradient(to right, #88619A, #4298B4 , #33A474, #E4AE3A);",
            },
        },
    },

    plugins: [forms],
};
