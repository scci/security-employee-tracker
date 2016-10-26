var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.scriptsIn('resources/assets/js/custom','public/js/custom.js');
    mix.scripts([
        'jquery-1.11.3.min.js',
        'materialize.min.js',
        'jquery.typeahead.min.js',
        'jquery.dataTables.min.js',
        'trumbowyg.min.js'
    ],'public/js/lib.js');
    mix.sass('app.scss');
    mix.phpUnit();
});