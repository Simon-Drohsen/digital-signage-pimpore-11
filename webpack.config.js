const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enablePostCssLoader()
    .enableSassLoader()
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
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction());

const defaultConfig = Encore.getWebpackConfig();
defaultConfig.name = '_default';

module.exports = Encore.getWebpackConfig();
