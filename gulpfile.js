var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

var paths = {
    'bootstrap': 'node_modules/bootstrap-sass/assets/',
    'font-awesome': 'node_modules/font-awesome/',
    'emojify': 'node_modules/emojify.js/'
};

elixir(function(mix) {
    mix.sass('app.scss')
        .browserify('app.js')
        .version(['css/app.css', 'js/bundle.js'])
        .copy(paths.bootstrap + 'fonts', 'public/build/fonts')
        .copy(paths['font-awesome'] + 'fonts', 'public/build/fonts')
        .copy(paths['emojify'] + 'dist/images/basic', 'public/build/images/emoji')
        .copy('resources/assets/images', 'public/build/images');
});

