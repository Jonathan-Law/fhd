function searchByFamilyConfig($stateProvider) {
  require('./searchByFamily.css');

  $stateProvider.state('families', {
    url: '/families/:letter',
    template: require('./searchByFamily.template.html'),
    controller: require('./searchByFamily.ctrl.js'),
    controllerAs: 'sbf',
  });
}

searchByFamilyConfig.$inject = ['$stateProvider'];

module.exports = searchByFamilyConfig;
