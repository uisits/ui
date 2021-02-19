let mix = require('laravel-mix');

let mix_app_name = '/' + process.env.MIX_APP_NAME + '/';
mix.setResourceRoot(mix_app_name);

require('laravel-mix-tailwind');

const tailwindcss = require('tailwindcss');

/*
|--------------------------------------------------------------------------
| Mix Asset Management
|--------------------------------------------------------------------------
|
| Mix provides a clean, fluent API for defining some Webpack build steps
| for your Laravel application. By default, we are compiling the Sass
| file for the application as well as bundling up all the JS files.
|
*/

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps()
    .version()
    .options({
        processCssUrls: false,
        postCss: [tailwindcss('./tailwind.config.js')],
    });
