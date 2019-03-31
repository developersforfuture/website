var Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')

	.addEntry('app', './assets/js/app.js')
	.addEntry('base', './assets/css/base.scss')
	.disableSingleRuntimeChunk()

	.autoProvidejQuery()

	// allow sass/scss files to be processed
	.enableSassLoader()
	// allow legacy applications to use $/jQuery as a global variable
	.autoProvidejQuery()
	// empty the outputPath dir before each build
	.cleanupOutputBeforeBuild()
	// this is the default behavior...
	.enableSourceMaps(true)
	// show OS notifications when builds finish/fail
	.enableBuildNotifications()
;

module.exports = Encore.getWebpackConfig();
