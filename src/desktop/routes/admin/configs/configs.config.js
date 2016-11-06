function configs($stateProvider) {
  require('./configs.css');

  $stateProvider.state('admin.configs', {
    url: '/configs',
    template: require('./configs.template.html'),
    controller: require('./configs.ctrl.js'),
    controllerAs: 'configs',
  });
}

configs.$inject = ['$stateProvider'];

module.exports = configs;
