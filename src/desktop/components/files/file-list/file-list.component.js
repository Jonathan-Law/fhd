module.exports = ngModule => {
  require('./file-list.component.css');

  ngModule.component('fileList', {
    template: require('./file-list.component.html'),
    controller: fileListCtrl,
    bindings: {
      callback: '&',
      newSelection: '<',
    }
  });

  function fileListCtrl(Business, configs, $scope, $element) {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.$onChanges = $onChanges;
    ctrl.makeSelection = makeSelection;
    ctrl.getAllFiles = getAllFiles;
    ctrl.baseURL = configs.baseURL;
    ctrl.sortBy = '';
    ctrl.label = 'title';
    ctrl.files = [];
    ctrl.reverse = true;
    ctrl.typeahead = '';
    ctrl.types = {
      person: true,
      place: true,
      collection: true,
      other: true,
    };

    function $onInit() {
      getAllFiles();
    }

    function $onChanges() {
      if (ctrl.newSelection !== ctrl.selection) {
        ctrl.selection = ctrl.newSelection;
      }
    }

    function makeSelection(thing) {
      ctrl.selection = thing;
      ctrl.callback({ selection: thing });
    }

    function getAllFiles() {
      const tempFiles = new Map();
      const promises = [];
      if (ctrl.typeahead) {
        Object.keys(ctrl.types).filter(type => ctrl.types[type]).forEach(type => {
          promises.push(Business.file.getByTag(ctrl.typeahead, type));
        });
        Promise.all(promises).then(results => {
          results.forEach(file => {
            tempFiles.set(file.id, file);
          });
          $scope.$applyAsync(() => {
            ctrl.files = Array.from(tempFiles.values())[0];
            $element.find('.file-list').scrollTop(0);
          });
        });
      } else {
        Business.file.getAllFiles().then((results) => {
          $scope.$applyAsync(() => {
            ctrl.files = results;
            $element.find('.file-list').scrollTop(0);
          });
        });
      }
    }
  }

  // inject dependencies here
  fileListCtrl.$inject = ['business', 'configs', '$scope', '$element'];
};
