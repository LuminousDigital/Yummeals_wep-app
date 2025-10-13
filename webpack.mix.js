const mix = require('laravel-mix');
const webpack = require('webpack');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js').vue().postCss('resources/css/app.css', 'public/css', [require("tailwindcss")]).version().webpackConfig({
    plugins: [
        new webpack.DefinePlugin({
            'process.env.MIX_HOST': JSON.stringify(process.env.MIX_HOST),
            'process.env.MIX_API_KEY': JSON.stringify(process.env.MIX_API_KEY),
            'process.env.MIX_GOOGLE_MAP_KEY': JSON.stringify(process.env.MIX_GOOGLE_MAP_KEY),
            'process.env.MIX_DEMO': JSON.stringify(process.env.MIX_DEMO),
        }),
    ],
});
