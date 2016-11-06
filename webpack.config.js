// Include dependencies
require('babel-register');
const getConfig = require('./other/webpack.config.es6');

/**
 * Configure your webpack setup here. These settings can be changed at any time.
 *
 * Read more on the wiki:
 * https://github.com/DomoApps/starter-kit/wiki/Webpack-Configuration
 */
module.exports = getConfig({
  includeDesktopView: false,
  includeResponsiveView: true,
  externals: {
    'angular-animate': {
      root: 'angular-animate',
      commonjs: 'angular-animate',
      commonjs2: 'angular-animate',
      amd: 'angular-animate'
    },
    'angular-ui-router': {
      root: 'angular-ui-router',
      commonjs: 'angular-ui-router',
      commonjs2: 'angular-ui-router',
      amd: 'angular-ui-router'
    },
    'bootstrap': {
      root: 'bootstrap',
      commonjs: 'bootstrap',
      commonjs2: 'bootstrap',
      amd: 'bootstrap'
    },
    'nodernizr': {
      root: 'modernizr',
      commonjs: 'nodernizr',
      commonjs2: 'nodernizr',
      amd: 'nodernizr'
    }
  },
  loaders: [
    // Include your app's extra loaders here
  ]
});
