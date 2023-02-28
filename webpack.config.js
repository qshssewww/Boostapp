// webpack.config.js v5.72.0
const path = require( 'path' );
// const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserWebpackPlugin = require('terser-webpack-plugin');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');

const isDev = process.env.NODE_ENV === 'development';
const isProd = !isDev;

const optimization = () => {
	const config = {
		splitChunks: {
			cacheGroups: {
				vendor: {
					test: /[\\/]node_modules[\\/](@fullcalendar|select2|jquery|moment|handlebars)[\\/]/, // matches /node_modules/
					name: 'vendor',
					chunks: 'all', // options: 'initial', 'async' , 'all'
				},
			},
		}
	}

	if (isProd) {
		config.minimizer = [
			new CssMinimizerPlugin(),
			new TerserWebpackPlugin()
		]
	}

	return config
}

const filename = ext => {
	// isDev ? `[name].${ext}` : `[name].[hash].${ext}`
	return `[name].bundle.${ext}`;
};

const cssLoaders = extra => {
	const loaders = [
		{
			loader: MiniCssExtractPlugin.loader,
			options: {},
		},
		'css-loader'
	]

	if (extra) {
		loaders.push(extra)
	}

	return loaders
}

const plugins = () => {
	const base = [
		// new HtmlWebpackPlugin({
		// 	template: 'templates/index.php',
		// 	filename: '../../../index.php',
		// 	publicPath: './assets/office/dist',
		// 	chunks: ['login']
		// }),
		// new HtmlWebpackPlugin({
		// 	template: 'templates/cart.php',
		// 	filename: '../../../office/cart.php',
		// 	publicPath: '../assets/office/dist',
		// 	chunks: ['cart']
		// }),
		// new HtmlWebpackPlugin({
		// 	template: 'templates/checkout.php',
		// 	filename: '../../../office/checkout.php',
		// 	publicPath: '../assets/office/dist',
		// 	chunks: ['checkout']
		// }),
		new CleanWebpackPlugin(),
		new MiniCssExtractPlugin({
			filename: filename('css'),
			ignoreOrder: true
		})
	]

	return base
}

module.exports = {
	context: path.resolve(__dirname, './assets/office/develop'),
	mode: 'development',
	entry: {
		'index': '/js/login.js',
		'cart': '/js/cart.js',
		'checkout': '/js/checkout.js',
		'refund': '/js/refund.js',
	},
	output: {
		filename: filename('js'),
		path: path.resolve( __dirname, './assets/office/dist'),
		publicPath: '/',
	},
	resolve: {
		extensions: ['.js', '.hbs', '.json'],
		alias: {
			'@partials': path.resolve(__dirname, './assets/office/develop/js/handlebars-partials'),
			'@modules': path.resolve(__dirname, './assets/office/develop/js/modules'),
			'@': path.resolve(__dirname, './assets/office/develop'),
		}
	},
	devServer: {
		open: true,
		hot: isDev,
		static: path.resolve(__dirname, './assets/office/dist'),
		port: 8001
	},
	optimization: optimization(),
	devtool: isDev ? 'source-map' : false,
	plugins: plugins(),
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: 'babel-loader',
			},
			{
				test: /\.hbs$/,
				use: [{
					loader: "handlebars-loader",
					options: {
						helperDirs: path.resolve(__dirname, "./assets/office/develop/js/handlebars-helpers")
					}
				}]
			},
			{
				test: /\.css$/,
				use: cssLoaders()
			},
			{
				test: /\.s[ac]ss$/,
				use: cssLoaders('sass-loader')
			},
			{
				// test: /\.(ttf|woff|woff2|eot)$/,
				test: /\.(png|jpg|jpeg|gif|svg|ttf|woff|woff2|eot)(\?.*$|$)/,
				use: ['file-loader']
			},
			{
				test: /\.html$/,
				loader: 'html-loader'
			}
		]
	}
};