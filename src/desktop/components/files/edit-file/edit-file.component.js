module.exports = ngModule => {
  require('./edit-file.component.css');

  ngModule.component('editFile', {
    template: require('./edit-file.component.html'),
    controller: editFileCtrl,
    bindings: {
      file: '<',
      callback: '&',
    }
  });

  function editFileCtrl(Business) {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.$onChanges = $onChanges;
    ctrl.saveChanges = saveChanges;
    ctrl.deleteFile = deleteFile;

    function $onInit() {
      handleTags();
    }
    function $onChanges() {
      handleTags();
    }

    function saveChanges(file) {
      Business.file.updateFile(file).then(() => {
        ctrl.callback({ file });
      });
    }

    function deleteFile(file) {
      if (confirm('Are you sure you want to delete this file?')) {
        Business.file.deleteFile(file).then(() => {
          ctrl.callback();
        });
      }
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
