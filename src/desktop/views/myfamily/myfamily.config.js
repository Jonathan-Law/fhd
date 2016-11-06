function myfamilyConfig($stateProvider) {
  require('./myfamily.css');

  $stateProvider.state('myfamily', {
    url: '/myfamily',
    template: require('./myfamily.template.html'),
    controller: require('./myfamily.ctrl.js'),
    controllerAs: 'myfamily',
  });
}

myfamilyConfig.$inject = ['$stateProvider'];

module.exports = myfamilyConfig;
