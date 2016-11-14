/* ngInject */
function users($scope, Business) {
  const ctrl = {};
  $scope.$ctrl = ctrl;
  ctrl.getUsers = getUsers;
  ctrl.actionCallback = actionCallback;
  init();

  function getUsers() {
    Business.user.getUsers().then(usersList => {
      ctrl.users = usersList;
    });
  }

  function actionCallback(action, user) {
    console.log('action', action, user);
  }

  function init() {
    getUsers();
  }
}

// inject dependencies here
users.$inject = ['$scope', 'business'];

module.exports = users;
