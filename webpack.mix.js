const mix = require('laravel-mix');

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


mix.js([
    'resources/js/app.js',
    // 'resources/assets/vendors/js/core/jquery-3.2.1.min.js',
    'public/assets/vendors/js/core/popper.min.js',
    // 'public/assets/vendors/js/core/bootstrap.min.js',
    'public/assets/vendors/js/perfect-scrollbar.jquery.min.js',
    'public/assets/vendors/js/prism.min.js',
    'public/assets/vendors/js/jquery.matchHeight-min.js',
    // 'public/assets/vendors/js/screenfull.min.js',
    // 'public/assets/vendors/js/pace/pace.min.js',
    // 'public/assets/vendors/js/chart.min.js',
    // 'public/assets/vendors/js/pickadate/picker.js',
    // 'public/assets/vendors/js/pickadate/picker.date.js',
    // 'public/assets/vendors/js/pickadate/picker.time.js',
    // 'public/assets/vendors/js/pickadate/legacy.js',
    // 'public/assets/vendors/js/switchery.min.js',
    // 'public/assets/vendors/js/sweetalert2.min.js',
    'public/assets/js/app-sidebar.js',
    'public/assets/js/notification-sidebar.js',
    'public/assets/js/customizer.js',
    // 'public/assets/js/switch.min.js',
    // 'public/assets/js/pick-a-datetime.js',
    'public/assets/js/tooltip.js',
    'public/assets/js/custom.js',
    // 'public/assets/js/sweet-alerts.js',
], 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

mix.styles([
    'public/assets/fonts/feather/style.min.css',
    'public/assets/fonts/simple-line-icons/style.css',
    'public/assets/fonts/font-awesome/css/font-awesome.min.css',
    'public/assets/vendors/css/perfect-scrollbar.min.css',
    'public/assets/vendors/css/prism.min.css',
    // 'public/assets/vendors/css/chartist.min.css',
    // 'public/assets/vendors/css/pickadate/pickadate.css',
    // 'public/assets/vendors/css/sweetalert2.min.css',
    // 'public/assets/vendors/css/switchery.min.css',
    'public/assets/css/app.css',
    'public/assets/css/custom.css',
], 'public/css/theme.css');

if( mix.inProduction() ){
    mix.version();
}

// mix.browserSync('stlv2.test');