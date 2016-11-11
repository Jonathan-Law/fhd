module.exports = ngModule => {
  require('./edit-file.component.css');

  ngModule.component('editFile', {
    template: require('./edit-file.component.html'),
    controller: editFileCtrl,
    bindings: {
      file: '<'
    }
  });

  function editFileCtrl() {
    const ctrl = this;

    ctrl.$onInit = $onInit;

    function $onInit() {
    }
  }

  // inject dependencies here
  editFileCtrl.$inject = [];
};
