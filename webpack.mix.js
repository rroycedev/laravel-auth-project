let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .copy('resources/assets/css/users.css', 'public/css')
   .copy('resources/assets/css/assetmgr.css', 'public/css')
   .copy('resources/assets/css/modal.css', 'public/css')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .copy('node_modules/mdbootstrap/js', 'public/mdbootstrap/js')
   .copy('node_modules/mdbootstrap/css', 'public/mdbootstrap/css')
   .copy('node_modules/mdbootstrap/font', 'public/mdbootstrap/font')
   .copy('node_modules/mdbootstrap/img', 'public/mdbootstrap/img')
   .copy('resources/assets/bootstrap-template/docs/js/bootstrap-template.js', 'public/bootstrap-template/js')
   .copy('resources/assets/font-awesome', 'public/font-awesome')

   //  Copy over theme template stylesheets

   .copy('resources/assets/bootstrap-template/docs/css/bootstrap-template.css', 'public/bootstrap-template/css')
   .copy('resources/assets/bootstrap-template/docs/css/default-theme.css', 'public/bootstrap-template/css')
   
;

