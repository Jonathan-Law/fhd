// const $ = require('jquery');
// const jQuery = require('jquery');
module.exports = ngModule => {
  ngModule.controller('registerCtrl', /* @ngInject */ ($scope, $uibModalInstance, business) => {
    // $scope.items = items;
    // $scope.selected = {
    //   item: $scope.items[0]
    // };

    $scope.register = () => {
      $scope.error = false;
      business.user.register($scope.username, $scope.password, $scope.email, $scope.first, $scope.last, $scope.gender).then((response) => {
        if (response) {
          $uibModalInstance.close(true);
        } else {
          $scope.error = true;
        }
      });
    };

    $scope.login = () => {
      $uibModalInstance.close(false);
    };

    $scope.cancel = () => {
      $uibModalInstance.dismiss();
    };
  })
  .directive('register', /* @ngInject */ (/*business*/$uibModal) => {
    require('./register.css');
    const template = require('./register.template.html');
    function linkFn(scope) {
      scope.modalInstance = null;
      scope.open = (size) => {
        scope.modalInstance = $uibModal.open({
          animation: true,
          template,
          controller: 'registerCtrl',
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
