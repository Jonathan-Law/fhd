/* ngInject */
function files($scope) {
  const ctrl = {};
  $scope.$ctrl = ctrl;
  ctrl.selectionMade = selectionMade;
  ctrl.fileEdited = fileEdited;

  function fileEdited(file) {
    if (file) {
      ctrl.selection = file;
    } else {
      ctrl.selection = null;
    }
  }

  function selectionMade(selection) {
    ctrl.selection = selection;
  }
}

// inject dependencies here
files.$inject = ['$scope'];

module.exports = files;
