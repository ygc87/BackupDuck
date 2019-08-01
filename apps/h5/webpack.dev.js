var webpack = require('webpack');
module.exports = {
  entry: {
    index: './webapp/thinksns.jsx',
  },
  output: {
    path: './resources/js',
    filename: '[name].js',
  },
  module: {
    loaders: [
      {
        test: /\.jsx$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
      }
    ]
  },
  resolve: {
    extensions: ['', '.js', '.jsx'],
  }
};