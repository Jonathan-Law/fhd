function familyNameConfig($stateProvider) {
  require('./family.css');

  $stateProvider.state('family', {
    url: '/family/:name',
    template: require('./family.template.html'),
    controller: require('./family.ctrl.js'),
    controllerAs: 'fn',
  });
}

familyNameConfig.$inject = ['$stateProvider'];

module.exports = familyNameConfig;
