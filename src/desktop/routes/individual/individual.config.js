function familyNameConfig($stateProvider) {
  require('./individual.css');

  $stateProvider.state('individual', {
    url: '/individual/:id',
    template: require('./individual.template.html'),
    controller: require('./individual.ctrl.js'),
    controllerAs: 'ind',
  });
}

familyNameConfig.$inject = ['$stateProvider'];

module.exports = familyNameConfig;
