const angular = require('angular');
require('angular-ui-bootstrap');
require('./desktop.css');
require('ngtouch');
angular
  .module('da.desktop', [
    'ui.router',
    'ngAnimate',
    'ngTouch',
    'ui.bootstrap',
    require('./services')(angular).name,
    require('./views')(angular).name,
    require('./components')(angular).name,
  ])
  .constant('$', require('jquery'))
  .constant('d3', require('d3'))
  .constant('_', require('lodash'))
  .config(($urlRouterProvider, $httpProvider) => {
    $httpProvider.defaults.withCredentials = true;
    $urlRouterProvider.otherwise('/');
  })
  .run(/* @ngInject */($rootScope, business) => {
    // $.ajax({
    //   type: 'POST',
    //   url: 'http://familyhistorydatabase.org/api/v1/user/login',
    //   data: {
    //     username: 'jonlaw88',
    //     password: 'Vivitronn'
    //   },
    //   success: (output) => {
    //     const date = moment(new Date()).add(1, 'days');
    //     document.cookie = 'MYPHPSESSID' + '=' + output.ssId + ';expires=' + date.format('YYYY-MM-DD HH:mm:ss') + ';domain=.familyhistorydatabase.com;path=/';
    //     document.cookie = 'MYPHPSESSID' + '=' + output.ssId + ';expires=' + date.format('YYYY-MM-DD HH:mm:ss') + ';domain=.localhost;path=/';
    //   },
    //   cache: false
    // });
    $rootScope.getTypeahead = (val) => {
      return business.getTypeahead(val);
    };
  });
