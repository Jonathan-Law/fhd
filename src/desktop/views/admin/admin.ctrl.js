/* ngInject */
function admin($scope/*, individual, $state*/, business) {
  $scope.isAdmin = false;
  $scope.getIsAdmin = () => {
    business.user.getIsAdmin().then((boolIsAdmin) => {
      $scope.isAdmin = boolIsAdmin;
    });
  };
  $scope.getIsAdmin();

  business.user.subscribeToUserState($scope.getIsAdmin);
}

// inject dependencies here
// admin.$inject = [];

module.exports = admin;
