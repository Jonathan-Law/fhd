function individuals($stateProvider) {
  require('./individuals.css');

  $stateProvider.state('admin.individuals', {
    url: '/individuals',
    template: require('./individuals.template.html'),
    controller: require('./individuals.ctrl.js'),
    controllerAs: 'individuals',
  });
}

individuals.$inject = ['$stateProvider'];

module.exports = individuals;
