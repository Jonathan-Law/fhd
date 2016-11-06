/* ngInject */
function individuals($scope, business/*, individual, $state*/) {
  $scope.selection = null;
  $scope.getNewList = (override) => {
    business.getTypeahead($scope.searchKey || override, 0).then((result) => {
      $scope.collection = result.sort((person1, person2) => {
        if (person1.firstName.toLowerCase() < person2.firstName.toLowerCase()) {
          return -1;
        } else if (person1.firstName.toLowerCase() > person2.firstName.toLowerCase()) {
          return 1;
        }
        return 0;
      });
    });
  };

  $scope.getSubmissionsList = () => {
    business.individual.getAllSubmissions().then((result) => {
      $scope.collection = result.sort((person1, person2) => {
        if (person1.firstName.toLowerCase() < person2.firstName.toLowerCase()) {
          return -1;
        } else if (person1.firstName.toLowerCase() > person2.firstName.toLowerCase()) {
          return 1;
        }
        return 0;
      });
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
