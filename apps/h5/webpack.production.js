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
  plugins: [
    new webpack.DefinePlugin({
      "process.env": {
        NODE_ENV: JSON.stringify("production")
      }
    }),
    new webpack.optimize.UglifyJsPlugin({
     compress: {
       warnings: false
     },
     mangle: {
        except: ['$super', '$', 'exports', 'require']
    }
   }),
  ],
  resolve: {
    extensions: ['', '.js', '.jsx'],
  }
};