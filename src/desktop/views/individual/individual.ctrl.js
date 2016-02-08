/* ngInject */
function individualCtrl($scope, individual, $state) {
  const ind = this;
  ind.id = ($state.params.constructor === Object && angular.equals($state.params, {}) || !$state.params.id) ? null : $state.params.id;

  $scope.$watch(angular.bind(ind, () => {
    return ind.id;
  }), (newval) => {
    $scope.indId = newval;
  });
}

// inject dependencies here
// individualCtrl.$inject = [];

module.exports = individualCtrl;
