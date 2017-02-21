module.exports = ngModule => {
  require('./file-list.component.css');

  ngModule.component('fileList', {
    template: require('./file-list.component.html'),
    controller: fileListCtrl,
    bindings: {
      callback: '&',
      newSelection: '<',
      individual: '<',
    }
  });

  function fileListCtrl(Business, configs, $scope, $element, $filter) {
    const ctrl = this;
    ctrl.$onInit = $onInit;
    ctrl.$onChanges = $onChanges;
    ctrl.$postLink = $postLink;
    ctrl.$onDestroy = $onDestroy;
    ctrl.makeSelection = makeSelection;
    ctrl.getAllFiles = getAllFiles;
    ctrl.getSubmissionsList = getSubmissionsList;
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
      getIsAdmin();
      Business.user.subscribeToUserState(getIsAdmin);
    }

    function $onChanges() {
      if (ctrl.newSelection !== ctrl.selection) {
        ctrl.selection = ctrl.newSelection;
        getAllFiles();
      }
    }

    function $postLink() {
      angular.element('body').on('keydown', bodyKeydown);
    }

    function $onDestroy() {
      angular.element('body').off('keydown', bodyKeydown);
    }

    function getIsAdmin() {
      Business.user.getIsAdmin().then((boolIsAdmin) => {
        ctrl.isAdmin = boolIsAdmin;
      });
    }

    function makeSelection(thing) {
      ctrl.selection = thing;
      ctrl.callback({
        selection: thing
      });
    }

    function bodyKeydown(e) {
      if (e.keyCode === 40) {
        const foundIndex = $filter('orderBy')(ctrl.files, ctrl.sortBy, !ctrl.reverse).findIndex((thing) => thing === ctrl.selection);
        if (foundIndex >= 0 && foundIndex !== (ctrl.files.length - 1)) {
          $scope.$applyAsync(() => {
            ctrl.makeSelection($filter('orderBy')(ctrl.files, ctrl.sortBy, !ctrl.reverse)[foundIndex + 1]);
            $element.find('.file-list').scrollTop((foundIndex + 1) * 50);
          });
        }
      } else if (e.keyCode === 38) {
        const foundIndex = $filter('orderBy')(ctrl.files, ctrl.sortBy, !ctrl.reverse).findIndex((thing) => thing === ctrl.selection);
        if (foundIndex >= 1) {
          $scope.$applyAsync(() => {
            ctrl.makeSelection($filter('orderBy')(ctrl.files, ctrl.sortBy, !ctrl.reverse)[foundIndex - 1]);
            $element.find('.file-list').scrollTop((foundIndex - 1) * 50);
          });
        }
      }
    }

    function getAllFiles() {
      const tempFiles = new Map();
      const promises = [];
      if (ctrl.typeahead) {
        Object.keys(ctrl.types).filter(type => ctrl.types[type]).forEach(type => {
          promises.push(Business.file.getByTag(ctrl.typeahead, type, ctrl.individual));
        });
        Promise.all(promises).then(results => {
          results.forEach(file => {
            tempFiles.set(file.id, file);
          });
          $scope.$applyAsync(() => {
            ctrl.files = Array.from(tempFiles.values())[0];
            ctrl.files.forEach(file => {
              file.id = +file.id;
            });
            $element.find('.file-list').scrollTop(0);
          });
        });
      } else {
        Business.file.getAllFiles(ctrl.individual).then((results) => {
          $scope.$applyAsync(() => {
            ctrl.files = results;
            ctrl.files.forEach(file => {
              file.id = +file.id;
            });
            $element.find('.file-list').scrollTop(0);
          });
        });
      }
    }

    function getSubmissionsList() {
      Business.file.getAllInactiveFiles().then((results) => {
        $scope.$applyAsync(() => {
          ctrl.files = results;
          ctrl.files.forEach(file => {
            file.id = +file.id;
          });
          $element.find('.file-list').scrollTop(0);
        });
      });
    }
  }

  // inject dependencies here
  fileListCtrl.$inject = ['business', 'configs', '$scope', '$element', '$filter'];
};
