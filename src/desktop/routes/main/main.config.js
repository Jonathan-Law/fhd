function mainConfig($stateProvider) {
  require('./main.css');

  $stateProvider.state('main', {
    url: '/home',
    template: require('./main.template.html'),
    controller: require('./main.ctrl.js'),
    controllerAs: 'main',
  });
}

mainConfig.$inject = ['$stateProvider'];

module.exports = mainConfig;
