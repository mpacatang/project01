let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | App Assets
 |--------------------------------------------------------------------------
 */

mix.js('resources/front/app.js', 'public/front/js/assets.js');
mix.sass('resources/front/assets/sass/main.scss', 'public/front/css/assets.css').options({
	processCssUrls: true
});

mix.copyDirectory('resources/front/assets/images', 'public/front/images');

if (mix.inProduction()) {
    mix.version();
}
