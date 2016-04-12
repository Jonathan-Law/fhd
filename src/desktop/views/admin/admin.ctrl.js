/* ngInject */
function admin($scope/*, individual, $state*/, business, $state) {
  $scope.open = false;
  $scope.$watch(() => {
    return $state.current;
  }, (newval) => {
    if (newval && newval.name) {
      const check = newval.name.split('.');
      if (check.length > 1 && check[0] === 'admin') {
        $scope.open = true;
      } else {
        $scope.open = false;
      }
    }
  }, true);
  $scope.isAdmin = false;
  $scope.getIsAdmin = () => {
    business.user.getIsAdmin().then((boolIsAdmin) => {
      $scope.isAdmin = boolIsAdmin;
    });
  };
  $scope.getIsAdmin();

  $scope.addSelection = null;
  $scope.selections = [{
    name: 'Manage Individuals',
    route: 'admin.individuals',
    shown: () => {
      return true;
    }
  }, {
    name: 'Manage Files',
    route: 'admin.files',
    shown: () => {
      return true;
    }
  }, {
    name: 'Manage Users',
    route: 'admin.users',
    shown: () => {
      return true;
    }
  }, {
    name: 'Manage Site',
    route: 'admin.configs',
    shown: () => {
      return true;
    }
  }, {
    name: 'Back to Admin',
    route: 'admin',
    shown: () => {
      return $scope.open;
    }
  }];

  business.user.subscribeToUserState($scope.getIsAdmin);
}

// inject dependencies here
// admin.$inject = [];

module.exports = admin;
