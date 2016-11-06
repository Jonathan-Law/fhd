/* ngInject */
function files($scope/*individual, $state*/) {
  $scope.addSelection = null;
  $scope.selections = [{
    name: 'Add Individual',
    content: '<photoalbum></photoalbum>'
  }, {
    name: 'Add File',
    content: '<div></div>'
  }, {
    name: 'Add User',
    content: '<div></div>'
  }, {
    name: 'Add Site Note',
    content: '<div></div>'
  }];

  $scope.setAddSelection = (selection) => {
    $scope.addSelection = selection;
  };
}

// inject dependencies here
// files.$inject = [];

module.exports = files;
