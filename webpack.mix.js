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

// These fixes are made by me for immediate browser update whenever I make any changes in vue file
 // Enable HMR
// if (process.env.MIX_ENV === 'development') {
//     mix.browserSync('localhost:8000'); // Adjust the URL if needed
//   }

// mix.js('resources/js/app.js', 'public/js')
//     .vue()
//     .sass('resources/sass/app.scss', 'public/css')
//     .options({  // // These fixes are made by me for immediate browser update whenever I make any changes in vue file
//         processCssUrls: false
//     })
//     .browserSync({
//         proxy: 'localhost:8000',
//         port: 3000 // Change this to your preferred port
//     });



// const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

// mix.js('resources/js/app.js', 'public/js')
//    .vue() // Ensure Vue support is enabled
//    .sass('resources/sass/app.scss', 'public/css')
//    .options({
//        processCssUrls: false
//    })
//    .browserSync({
//        proxy: 'localhost:8000', // The URL of your Laravel server
//        port: 3000, // Use a different port for BrowserSync
//        files: [
//            'resources/views/**/*.php',
//            'resources/js/**/*.js',
//            'resources/sass/**/*.scss'
//        ]
//    });



   mix.js('resources/js/app.js', 'public/js')
       .vue()
       .sass('resources/sass/app.scss', 'public/css')
       .options({  // // These fixes are made by me for immediate browser update whenever I make any changes in vue file
           processCssUrls: false
       })
       .sourceMaps();