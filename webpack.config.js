const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enablePostCssLoader()
    .enableSassLoader()
    .enableVersioning()
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    }, {
        useBuiltIns: 'usage',
        corejs: 3,
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
        pattern: /\.(jpe?g|png|gif|svg|webp)$/i,
    })
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction());

const defaultConfig = Encore.getWebpackConfig();
defaultConfig.name = '_default';

module.exports = Encore.getWebpackConfig();