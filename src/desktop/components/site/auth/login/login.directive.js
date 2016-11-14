// const $ = require('jquery');
// const jQuery = require('jquery');
module.exports = ngModule => {
  ngModule.controller('loginCtrl', /* @ngInject */ ($scope, $uibModalInstance, business) => {
    // $scope.items = items;
    // $scope.selected = {
    //   item: $scope.items[0]
    // };

    $scope.resetPassword = () => {
      if (confirm(`We are about to reset the password for: ${$scope.username}. Is this you, and would you like to continue?`)) {
        business.user.resetPassword($scope.username);
      }
    };

    $scope.login = () => {
      $scope.error = false;
      business.user.login($scope.username, $scope.password).then((response) => {
        if (response) {
          // business.user.isLoggedIn().then((result) => {
          //   if (result) {
          //     console.log('we kept the session!!!');
          //   }
          // });
          $uibModalInstance.close(true);
          // console.log('response', response);
        } else {
          $scope.error = true;
        }
      });
    };

    $scope.register = () => {
      $uibModalInstance.close(false);
    };

    $scope.cancel = () => {
      $uibModalInstance.dismiss();
    };
  })
  .directive('login', /* @ngInject */ (/*business*/$uibModal) => {
    require('./login.css');
    const template = require('./login.template.html');
    function linkFn(scope) {
      scope.modalInstance = null;
      scope.open = (size) => {
        scope.modalInstance = $uibModal.open({
          animation: true,
          template,
          controller: 'loginCtrl',
          size, // 'lg, sm'
          resolve: { // injecting data into the modal
            // items: () => {
            //   return $scope.items;
            // }
          }
        });
        scope.modalInstance.result.then((result) => {
          if (result) {
            scope.callback({
              result: true
            });
          } else {
            scope.switcher();
          }
        }, () => {
          scope.callback({
            result: false
          });
        });
      };

      scope.$watch('trigger', (newval) => {
        if (newval) {
          scope.open('lg');
        } else if (scope.modalInstance) {
          scope.modalInstance.close();
        }
      });
    }
    return {
      template: '<div></div>',
      restrict: 'E',
      scope: {
        trigger: '=?',
        switcher: '&',
        callback: '&',
      },
      link: linkFn,
    };
  });
};
