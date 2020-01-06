const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const FriendlyErrorsWebpackPlugin = require( 'friendly-errors-webpack-plugin' );
const Autoprefixer = require('autoprefixer');

const config = {
  mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
  stats: 'errors-only',
  entry: {
    main: [
      './src/PredicWCPhoto/assets/js/main.js',
    ]
  },
  output: {
    path: path.resolve(__dirname),
    filename: './dist/js/[name].js',
    libraryTarget: 'this',
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
              plugins: () => [Autoprefixer()]
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
        use: ['babel-loader', 'eslint-loader'],
      },
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      fallback: 'style-loader',
      filename: 'dist/css/[name].css',
    }),
    new StyleLintPlugin({
      files: './src/PredicWCPhoto/assets/scss/**/*.scss',
      configFile: './.stylelintrc',
    }),
    new FriendlyErrorsWebpackPlugin(),
  ]
};

module.exports = config;
