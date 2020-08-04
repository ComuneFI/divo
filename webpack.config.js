var Encore = require('@symfony/webpack-encore');

Encore
        // directory where compiled assets will be stored
        .setOutputPath('public/build/')
        // public path used by the web server to access the output path
        .setPublicPath('/build')
        // only needed for CDN's or sub-directory deploy
        //.setManifestKeyPrefix('build/')

        .copyFiles([{
                from: './node_modules/bootstrap-italia/dist/fonts',
                to: 'fonts/[path][name].[ext]',
            }, {
                from: './node_modules/bootstrap-italia/dist/svg',
                to: 'svg/[path][name].[ext]'
            }, {
                from: './assets/images',
                pattern: /\.(png|jpg|jpeg)$/,
                // to path is relative to the build directory
                to: 'images/[path][name].[ext]'
            }
        ])
        /*
         * ENTRY CONFIG
         *
         * Add 1 entry for each "page" of your app
         * (including one that's included on every page - e.g. "app")
         *
         * Each entry will result in one JavaScript file (e.g. app.js)
         * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
         */
        .addEntry('app', './assets/js/app.js')
        .addEntry('uploadDataCSV', './assets/js/uploadDataCSV.js')
        .addEntry('configsave', './assets/js/configsave.js')
        .addEntry('tableDetailLog', './assets/js/tableDetailLog.js')
        .addEntry('timeline', './assets/js/timeline.js')
        .addEntry('checkenabled', './assets/js/checkEnabled.js')
        .addEntry('bootstrapitalia', './vendor/comunedifirenze/bicorebundle/assets/js/bootstrapitalia.js')
        .addEntry('bicore', './vendor/comunedifirenze/bicorebundle/assets/js/bicore.js')
        .addEntry('login', './vendor/comunedifirenze/bicorebundle/assets/js/login.js')
        .addEntry('bitabella', './vendor/comunedifirenze/bicorebundle/assets/js/bitabella.js')
        .addEntry('bidemo', './vendor/comunedifirenze/bicorebundle/assets/js/bidemo.js')
        .addEntry('adminpanel', './vendor/comunedifirenze/bicorebundle/assets/js/adminpanel.js')
        .addEntry('alert', './assets/js/alert.js')
        .addEntry('divoSlicer', './assets/js/divoSlicer.js')
        .addEntry('openCollapse', './assets/js/openCollapse.js')
        .addEntry('creautente', './assets/js/creautente.js')

        //.addEntry('page1', './assets/js/page1.js')
        //.addEntry('page2', './assets/js/page2.js')

        // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
        .splitEntryChunks()

        // will require an extra script tag for runtime.js
        // but, you probably want this, unless you're building a single-page app
        .enableSingleRuntimeChunk()

        /*
         * FEATURE CONFIG
         *
         * Enable & configure other features below. For a full
         * list of features, see:
         * https://symfony.com/doc/current/frontend.html#adding-more-features
         */
        .cleanupOutputBeforeBuild()
        .enableBuildNotifications()
        .enableSourceMaps(!Encore.isProduction())
        // enables hashed filenames (e.g. app.abc123.css)
        .enableVersioning(Encore.isProduction())

        // enables @babel/preset-env polyfills
        .configureBabel(() => {
        }, {
            useBuiltIns: 'usage',
            corejs: 3
        })

        // enables Sass/SCSS support
        //.enableSassLoader()

        // uncomment if you use TypeScript
        //.enableTypeScriptLoader()

        // uncomment to get integrity="..." attributes on your script & link tags
        // requires WebpackEncoreBundle 1.4 or higher
        //.enableIntegrityHashes()

        // uncomment if you're having problems with a jQuery plugin
        .autoProvidejQuery()

        // uncomment if you use API Platform Admin (composer req api-admin)
        //.enableReactPreset()
        //.addEntry('admin', './assets/js/admin.js')
        ;

module.exports = Encore.getWebpackConfig();
