module.exports = config;

function config($ngReduxProvider, $urlRouterProvider, $httpProvider) {
  // $ngReduxProvider.createStoreWith(require('../common/store/reducers'));
  $urlRouterProvider.otherwise('/home');
  $httpProvider.defaults.withCredentials = true;
}

config.$inject = ['$ngReduxProvider', '$urlRouterProvider', '$httpProvider'];
