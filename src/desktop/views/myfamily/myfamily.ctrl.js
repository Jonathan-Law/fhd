const _ = require('lodash');
/* ngInject */
function myfamily($timeout, Business, $location, scope, $uibModal) {
  $timeout(() => {
    scope.getStatus();
  }, 100);

  // scope.$on('$NOTLOGGEDIN', function () {
  //   $location.path('/');
  // });
  // scope.$on('$LOGOUT', function () {
  //   $location.path('/');
  // });
  scope.getMySubmissions = () => {
    Business.individual.getMySubmissions().then((result) => {
      if (result && result.length && result !== 'null') {
        _.each(result, (submission) => {
          Business.user.getUserInfoId(submission.submitter).then((data) => {
            if (data) {
              submission.displayableName = data.displayableName;
            }
          });
        });
      }
      scope.approveThese = result;
    }, () => {
      // console.log('something broke');
    });
  };
  scope.getMySubmissions();

  scope.getStatus = () => {
    Business.user.isLoggedIn().then((result) => {
      // console.log('result', result);
      if (!result) {
        $location.path('/');
      }
    });
  };

  scope.editIndividual = (id) => {
    scope.open(id);
  };

  scope.open = (id) => {
    scope.modalInstance = $uibModal.open({
      animation: true,
      template: `<div class="modal-header" style="display: flex; flex-direction: row;">
            <h3 class="modal-title" id="modal-title" style="flex: 1">Add/Edit individuals</h3>
            <i class="fa fa-times" ng-click="closeModal()" style="line-height:34px;"></i>
        </div>
        <div class="modal-body" id="modal-body">
            <edit-individual person-id="${id || ''}" callback="doCallback"></edit-individual>
        </div>`,
      controller: ($scope, $uibModalInstance) => {
        $scope.closeModal = () => {
          $uibModalInstance.dismiss();
        };
      },
      size: 'lg', // 'lg, sm'
      resolve: { // injecting data into the modal
        editId: () => {
          return id;
        },
        doCallback: () => {
          return scope.getMySubmissions;
        }
      }
    });
    scope.modalInstance.result.then(() => {
      scope.getMySubmissions();
    }, () => {
      scope.getMySubmissions();
    });
  };

  Business.user.subscribeToUserState(scope.getStatus);
  // const fn = this;
}

// inject dependencies here
myfamily.$inject = ['$timeout', 'business', '$location', '$scope', '$uibModal'];

module.exports = myfamily;
