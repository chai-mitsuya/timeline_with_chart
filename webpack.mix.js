const mix = require("laravel-mix");

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
// 出力元のファイルと、出力先のディレクトリを指定する。
mix.js("resources/assets/js/app.js", "public/js").sass("resources/sass/app.scss","public/css");
mix.sass("resources/sass/common.scss","public/css");

mix.js("resources/assets/js/twitter.js", "public/js");
mix.js("resources/assets/js/twitterLoader.js", "public/js");

// develop環境の時にソースマップを表示するための設定
if (!mix.inProduction()) {
    mix.webpackConfig({devtool: 'source-map'}).sourceMaps()
}

// キャッシュを更新
mix.version();

