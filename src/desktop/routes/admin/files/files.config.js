function files($stateProvider) {
  require('./files.css');

  $stateProvider.state('admin.files', {
    url: '/files',
    template: require('./files.template.html'),
    controller: require('./files.ctrl.js'),
    controllerAs: 'files',
  });
}

files.$inject = ['$stateProvider'];

module.exports = files;
