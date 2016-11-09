module.exports = ngModule => {
  require('./file-list.component.css');

  ngModule.component('fileList', {
    template: require('./file-list.component.html'),
    controller: fileListCtrl,
    bindings: {
      // Inputs should use < and @ bindings.
      // Outputs should use & bindings.
    }
  });

  function fileListCtrl(Business) {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.files = [];
    ctrl.typeahead = 'Salt Lake';
    ctrl.types = [
      'person',
      'place',
      'other',
    ];

    function $onInit() {
      // Called on each controller after all the controllers have been constructed and had their bindings initialized
      // Use this for initialization code.
      getAllFiles();
    }

    function getAllFiles() {
      const tempFiles = new Map();
      const promises = [];
      if (ctrl.typeahead) {
        ctrl.types.forEach(type => {
          promises.push(Business.file.getByTag(ctrl.typeahead, type));
          Promise.all(promises).then(results => {
            results.forEach(file => {
              tempFiles.set(file.id, file);
            });
            ctrl.files = Array.from(tempFiles.values())[0];
          });
        });
      } else {
        Business.file.getAllFiles().then((results) => {
          ctrl.files = results;
        });
      }
    }
  }

  // inject dependencies here
  fileListCtrl.$inject = ['business'];
};
