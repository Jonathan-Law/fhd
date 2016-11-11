/* ngInject */
function files($scope) {
  const ctrl = {};
  $scope.$ctrl = ctrl;
  ctrl.selectionMade = selectionMade;

  function selectionMade(selection) {
    ctrl.selection = selection;
  }
}

// inject dependencies here
files.$inject = ['$scope'];

module.exports = files;
