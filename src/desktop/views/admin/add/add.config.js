function add($stateProvider) {
  require('./add.css');

  $stateProvider.state('admin.add', {
    url: '/add',
    template: require('./add.template.html'),
    controller: require('./add.ctrl.js'),
    controllerAs: 'add',
  });
}

add.$inject = ['$stateProvider'];

module.exports = add;
