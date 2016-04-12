/* ngInject */
function individuals($scope, business/*, individual, $state*/) {
  $scope.selection = null;
  $scope.getNewList = (override) => {
    business.getTypeahead($scope.searchKey || override, 0).then((result) => {
      $scope.collection = result;
    });
  };

  $scope.getSubmissionsList = () => {
    business.individual.getAllSubmissions().then((result) => {
      $scope.collection = result;
    });
  };

  $scope.getNewList();

  $scope.makeSelection = (thing) => {
    $scope.selection = thing;
  };
}

// inject dependencies here
// individuals.$inject = [];

module.exports = individuals;
