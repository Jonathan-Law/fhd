function edit($stateProvider) {
  require('./edit.css');

  $stateProvider.state('admin.edit', {
    url: '/edit',
    template: require('./edit.template.html'),
    controller: require('./edit.ctrl.js'),
    controllerAs: 'edit',
  });
}

edit.$inject = ['$stateProvider'];

module.exports = edit;
