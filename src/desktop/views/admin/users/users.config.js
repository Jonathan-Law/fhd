function users($stateProvider) {
  require('./users.css');

  $stateProvider.state('admin.users', {
    url: '/users',
    template: require('./users.template.html'),
    controller: require('./users.ctrl.js'),
    controllerAs: 'users',
  });
}

users.$inject = ['$stateProvider'];

module.exports = users;
