/*
 * ***********************************************************
 * WARNING: DO NOT EDIT FILE BELOW THIS POINT!
 * ***********************************************************
 */
module.exports = function getConfig(configOptions) {
  // dependencies
  const fs = require('fs');
  const path = require('path');
  const webpack = require('webpack');
  const HtmlWebpackPlugin = require('html-webpack-plugin');
  const NgAnnotatePlugin = require('ng-annotate-webpack-plugin');

  // postcss plugins
  const precss = require('precss');
  const postcssImport = require('postcss-import');
  const reporter = require('postcss-reporter');
  const cssnano = require('cssnano');
  const messages = require('postcss-browser-reporter');
  const autoprefixer = require('autoprefixer');

  // Environment
  const ON_DEV = process.env.NODE_ENV === 'development' || !process.env.NODE_ENV;
  const ON_TEST = process.env.NODE_ENV === 'test';
  const ON_PROD = process.env.NODE_ENV === 'production';
  const pkg = require('../package.json');
  const bannerText = fs.readFileSync(path.resolve(__dirname, '../BANNER.txt')).toString();
  const date = new Date();
  const hash = date.getTime();
  const config = {
    context: path.resolve(__dirname, '../src'),

    // We will add entry points based on the platform configs.
    entry: {},

    // where 3rd-party modules can reside
    resolve: {
      modulesDirectories: ['node_modules', 'bower_components']
    },

    output: {
      // where to put standalone build file
      path: './dist',
      publicPath: '',
      filename: `/[name]/[name]${hash}.js`,
      sourceMapFilename: '[file].map',
      libraryTarget: 'umd'
    },

    // dependencies listed here will NOT be bundled into the app, even if you `require` them.
    externals: {
      'angular': {
        root: 'angular',
        commonjs: 'angular',
        commonjs2: 'angular',
        amd: 'angular'
      },
      'lodash': {
        root: '_',
        commonjs: 'lodash',
        commonjs2: 'lodash',
        amd: 'lodash'
      },
      'jquery': {
        root: '$',
        commonjs: 'jquery',
        commonjs2: 'jquery',
        amd: 'jQuery'
      },
      'd3': {
        root: 'd3',
        commonjs: 'd3',
        commonjs2: 'd3',
        amd: 'd3'
      }
    },

    // optimization plugins
    // we add more items to this array based on configs set at top of file.
    plugins: [
      new webpack.optimize.OccurenceOrderPlugin(),
      new webpack.ResolverPlugin(
        new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin('bower.json', ['main'])
      ),
      new webpack.optimize.DedupePlugin(),
      new NgAnnotatePlugin({
        add: false,
        remove: false
      }),
      new webpack.BannerPlugin(bannerText),
      new webpack.DefinePlugin({
        ON_DEV,
        ON_TEST,
        ON_PROD,
        'process.env': {
          'NODE_ENV': JSON.stringify(process.env.NODE_ENV)
        }
      })
    ],

    // what loaders to use based on file type.
    module: {
      preLoaders: [{
        test: /\.js$/,
        loader: 'eslint-loader',
        exclude: /(node_modules|bower_components)/,
      }],
      loaders: [{
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        loader: 'babel',
        query: {
          cacheDirectory: true,
          presets: ['es2015'],
          plugins: ['transform-runtime', 'add-module-exports']
        }
      }, {
        test: /\.css$/,
        loader: 'style!css?-minimize!postcss',
        exclude: /(node_modules|bower_components)/
      }, {
        test: /\.(png|jpeg|gif).*$/,
        loader: 'file?name=/[name].[ext]?[hash]'
      }, {
        test: /\.html$/,
        loader: 'raw'
      }, {
        test: /\.(woff|ttf|eot|svg).*$/,
        loader: 'file?name=/[name].[ext]?[hash]'
      }]
    },

    // postcss plugins settings
    postcss: function postcss(_webpack) {
      const postcssPlugins = [
        postcssImport({
          addDependencyTo: _webpack,
          onImport: function onImport(files) {
            files.forEach(this.addDependency);
          }.bind(this)
        }),
        precss(),
        autoprefixer({
          browsers: ['last 2 versions']
        }),
        reporter()
      ];
      // only minify when on production
      if (ON_PROD) {
        postcssPlugins.push(cssnano({
          mergeRules: false,
          zindex: false,
          reduceIdents: false,
          mergeIdents: false,
          safe: true
        }));
      } else {
        // use the message reported when on development
        postcssPlugins.push(messages());
      }

      return postcssPlugins;
    },

    eslint: {
      formatter: require('eslint-friendly-formatter'),
    },

    // devtool: ON_PROD ? 'source-map' : 'cheap-module-eval-source-map',
    devtool: 'source-map',

    devServer: {
      contentBase: 'dist/',
      noInfo: false,
      hot: false,
      inline: false
    }
  };

  /**
   * If on production then minify code else (on dev) and turn on hot module replacement.
   */
  if (ON_PROD) {
    config.plugins.push();
  } else {
    config.plugins.push(new webpack.HotModuleReplacementPlugin());
  }


  config.entry.desktop = './desktop/index.js';
  config.plugins.push(
    new HtmlWebpackPlugin({
      title: 'Desktop',
      dev: process.env.NODE_ENV === 'development' || !process.env.NODE_ENV,
      pkg,
      template: 'src/desktop/desktop.php', // Load a custom template
      inject: 'body', // Inject all scripts into the body
      filename: process.env.NODE_ENV === 'development' ? 'index.html' : 'index.php',
      chunks: ['desktop']
    })
  );

  /**
   * Add any extra externals
   */
  if (configOptions.hasOwnProperty('externals')) {
    const addedExternals = configOptions.externals;
    for (const key in addedExternals) {
      if (addedExternals.hasOwnProperty(key)) {
        config.externals[key] = addedExternals[key];
      }
    }
  }

  /**
   * Add any extra loaders
   */
  if (configOptions.hasOwnProperty('loaders') && configOptions.loaders.length > 0) {
    const addedLoaders = configOptions.loaders;
    config.module.loaders.push.apply(config.module.loaders, addedLoaders);
  }

  /**
   * Return the finished configuration back to the caller
   */
  return config;
};
