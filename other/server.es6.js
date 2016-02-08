/* eslint no-console: 0 */
// webpack
const WebpackDevServer = require('webpack-dev-server');
const webpack = require('webpack');
const webpackConfig = require('../webpack.config');
webpackConfig.output.path = '/';

const compiler = webpack(webpackConfig);

// proxy
const path = require('path');
const fs = require('fs-extra');
const glob = require('glob');
const request = require('request');
const portfinder = require('portfinder');
portfinder.basePort = 9000;

// webpack-dev-server
const server = new WebpackDevServer(compiler, {
  contentBase: 'dist/',
  hot: false,
  noInfo: true, // set to false if you want to see build info
  stats: {
    colors: true
  }
});

// start server

portfinder.getPort({
  host: '0.0.0.0'
}, (err, port) => {
  server.listen(port, '0.0.0.0', () => {
    console.log(`Listening on http://0.0.0.0:${port}/webpack-dev-server/index.html`);
  });
});