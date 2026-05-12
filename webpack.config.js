const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( process.cwd(), 'src', 'index.js' ),
		'blocks/experience-list/index': path.resolve( process.cwd(), 'src', 'blocks', 'experience-list', 'index.js' ),
	},
	output: {
		filename: '[name].js',
		path: path.resolve( process.cwd(), 'dist' ),
	},
	plugins: [
		...( defaultConfig.plugins || [] ),
		new CopyWebpackPlugin( {
			patterns: [
				{
					from: 'blocks/**/style.css',
					context: path.resolve( process.cwd(), 'src' ),
					noErrorOnMissing: true,
				},
				{
					from: 'blocks/**/editor.css',
					context: path.resolve( process.cwd(), 'src' ),
					noErrorOnMissing: true,
				},
				{
					from: 'blocks/**/render.php',
					context: path.resolve( process.cwd(), 'src' ),
					noErrorOnMissing: true,
				},
				{
					from: 'blocks/**/block.json',
					context: path.resolve( process.cwd(), 'src' ),
					noErrorOnMissing: true,
				},
			],
		} ),
	],
};
