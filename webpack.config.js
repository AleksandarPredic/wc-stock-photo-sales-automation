var path = require( 'path' ); // Path dependency
const UglifyJsPlugin = require( 'uglifyjs-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const OptimizeCSSAssetsPlugin = require( 'optimize-css-assets-webpack-plugin' );
const StyleLintPlugin = require( 'stylelint-webpack-plugin' );
const Autoprefixer = require( 'autoprefixer' );

// Get mode - https://webpack.js.org/concepts/mode/
module.exports = ( env, argv ) => {

	let inProduction = argv.mode === 'production';

	let config = {
		entry: {
			main: [
				'./src/themes/predic-storefront/assets/js/main.js',
				'./src/themes/predic-storefront/assets/scss/style.scss',
			],
		},
		output: {
			path: path.resolve( __dirname, './src/themes/predic-storefront/dist/' ),
			filename: 'assets/js/[name].js',
		},
		module: {
			rules: [
				{
					test: /\.s[ac]ss$/,
					use: [
						{
							loader: MiniCssExtractPlugin.loader,
						},
						{
							loader: "css-loader", options: {
								sourceMap: true,
							},
						},
						{
							loader: 'postcss-loader',
							options: {
								plugins: () => [Autoprefixer()],
								sourceMap: true,
							}
						},
						{
							loader: "sass-loader", options: {
								sourceMap: true,
							},
						},
					],
				},
				{
					test: /\.js$/,
					exclude: /node_modules/,
					use: ["babel-loader", "eslint-loader"],
				},
			],
		},
		optimization: {
			minimizer: [
				new UglifyJsPlugin( {
					cache: true,
					parallel: true,
					sourceMap: inProduction ? false : true,
				} ),
				new OptimizeCSSAssetsPlugin( { cssProcessorOptions: { map: { inline: false, annotation: true, } } } ),
			],
		},
		plugins: [
			new StyleLintPlugin( {
				files: './src/themes/predic-storefront/assets/scss/**/*.scss',
				configFile: './.stylelintrc',
			} ),
			new MiniCssExtractPlugin( {
				fallback: 'style-loader',
				filename: 'assets/css/[name].css',
			} ),
		],
		devtool: 'source-map',
	};

	return config;
};
