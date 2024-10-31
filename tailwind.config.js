const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');
const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./templates/**/*.html.twig",
        "./assets/images/**/*.svg",
        // "./themes/**/*.html.twig",
        "./assets/js/**/*.js",
        "star': 'url(./assets/images/illustrations/star.svg)",
    ],
    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            'midnight': {
                50: '#F3F6FC',
                100: '#E6EEF8',
                200: '#C7DBF0',
                300: '#96BCE3',
                400: '#5E99D2',
                500: '#397DBE',
                600: '#2962A0',
                700: '#224F82',
                800: '#20436C',
                900: '#1b324e',
                950: '#15253C',
                DEFAULT: '#1B324E'
            },
            lagoon: {
                50: '#F1FCFA',
                100: '#D0F7F4',
                200: '#A1EEE9',
                300: '#6ADEDB',
                400: '#39C1C2',
                500: '#22A7AA',
                600: '#198388',
                700: '#18696D',
                800: '#185357',
                900: '#184549',
                950: '#08272B',
                DEFAULT: '#39C1C2'
            },
            mustard: {
                50: '#FDF9E9',
                100: '#FCF0C5',
                200: '#FBDE8D',
                300: '#F8C44C',
                400: '#F5B02E',
                500: '#E4910E',
                600: '#C56D09',
                700: '#9D4C0B',
                800: '#823D11',
                900: '#6F3214',
                950: '#401808',
                DEFAULT: '#F5B02E'
            },
            coral: {
                50: '#FEF2F3',
                100: '#FEE2E4',
                200: '#FECACD',
                300: '#FBA6AB',
                400: '#F6636B',
                500: '#EE454E',
                600: '#DA2832',
                700: '#B81D25',
                800: '#981C23',
                900: '#7E1E23',
                950: '#450A0D',
                DEFAULT: '#F6636B'
            },
            black: colors.black,
            white: colors.white,
        },
        screens: {
            'display': '3000px',
            'laptop': '1800px',
        },

        extend: {
            fontFamily: {
                'chonky': ['chonky', ...defaultTheme.fontFamily.sans],
                'sans': ['area-normal', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'hero': '5.2rem',
                'h2': ['2.4rem', {
                    fontWeight: defaultTheme.fontWeight.normal,
                }],
                'styleguide': ['35px', {
                    lineHeight: '46px',
                    fontWeight: defaultTheme.fontWeight.bold,
                }],
                'description': ['18px', {
                    lineHeight: '26px',
                    fontWeight: defaultTheme.fontWeight.normal,
                }],
            },

            dropShadow: {
                DEFAULT: '20px 12px 2px rgba(0,0,0,0.2)',
            },

            borderWidth: {
                DEFAULT: '1.5px',
            },

            keyframes: {
                firework: {
                    '0%': { 'background-image': 'url("/assets/images/firework1.svg")' },
                    '10%': { 'background-image': 'url("/assets/images/firework2.svg")' },
                    '20%': { 'background-image': 'url("/assets/images/firework3.svg")' },
                    '75%': { 'background-image': 'url("/assets/images/firework4.svg")' },
                },
                balloons: {
                    '0%, 100%': { top: '-4rem' },
                    '50%': { top: '-3.25rem' },
                }
            },

            animation: {
                firework: 'firework 3.5s linear infinite',
                balloons: 'balloons 2.5s ease-in-out infinite',
            }
        },
    },
    plugins: [
        plugin(function({ addBase, matchUtilities, addComponents, theme }) {
            addBase({
                'html': {
                    fontSize: '25px',
                    fontWeight: theme('fontWeight.bold'),
                    lineHeight: '1.5',
                },
                'h1': {
                    fontFamily: theme('fontFamily.chonky'),
                    fontSize: '3.6rem',
                    fontWeight: theme('fontWeight.normal'),
                },
                'h2': {
                    fontSize: '2.4rem',
                    fontWeight: theme('fontWeight.normal'),
                },
                'subheading': {
                    fontSize: '3.2rem',
                    fontWeight: theme('fontWeight.normal'),
                    fontFamily: theme('fontFamily.chonky'),
                },
            })
            matchUtilities(
                {
                    "animation-delay": (value) => {
                        return {
                            "animation-delay": value,
                        };
                    },
                },
                {
                    values: theme("transitionDelay"),
                }
            );
        }),
    ],
}

