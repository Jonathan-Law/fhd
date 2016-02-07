require('./desktop.css');
const angular = require('angular');

angular
  .module('da.desktop', [
    'ui.router',
    'ngAnimate',
    require('./services')(angular).name,
    require('./views')(angular).name,
    require('./components')(angular).name,
  ])
  .constant('$', require('jquery'))
  .constant('d3', require('d3'))
  .constant('_', require('lodash'))
  .config($urlRouterProvider => {
    $urlRouterProvider.otherwise('/');
  });
