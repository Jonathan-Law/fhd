function admin($stateProvider) {
  require('./admin.css');

  $stateProvider.state('admin', {
    url: '/admin',
    template: require('./admin.template.html'),
    controller: require('./admin.ctrl.js'),
    controllerAs: 'admin',
  });
}

admin.$inject = ['$stateProvider'];

module.exports = admin;
