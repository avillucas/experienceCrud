const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( process.cwd(), 'src', 'index.js' ),
		'blocks/experience-list/index': path.resolve( process.cwd(), 'src', 'blocks', 'experience-list', 'index.js' ),
		'blocks/experience-header-slider/index': path.resolve( process.cwd(), 'src', 'blocks', 'experience-header-slider', 'index.js' ),
	},
	output: {
		filename: '[name].js',
		path: path.resolve( process.cwd(), 'dist' ),
	},
};
