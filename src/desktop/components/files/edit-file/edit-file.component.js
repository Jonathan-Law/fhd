module.exports = ngModule => {
  require('./edit-file.component.css');

  ngModule.component('editFile', {
    template: require('./edit-file.component.html'),
    controller: editFileCtrl,
    bindings: {
      file: '<'
    }
  });

  function editFileCtrl(Business) {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.$onChanges = $onChanges;

    function $onInit() {
      handleTags();
    }
    function $onChanges() {
      handleTags();
    }

    function handleTags() {
      if (ctrl.file) {
        Business.file.getTags(ctrl.file.id).then(tags => {
          ctrl.file.tags = tags;
        });
      }
    }
  }

  // inject dependencies here
  editFileCtrl.$inject = ['business'];
};
