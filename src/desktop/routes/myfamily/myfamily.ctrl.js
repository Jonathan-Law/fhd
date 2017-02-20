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
    scope.currentId = id;
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
            <button class="btn btn-primary" ng-click="editFile()">Add/Edit Files</button>
            <edit-individual person-id="${id || ''}" callback="doCallback"></edit-individual>
        </div>`,
      controller: ($scope, $uibModalInstance) => {
        $scope.closeModal = () => {
          $uibModalInstance.dismiss();
        };
        $scope.editFile = () => {
          $uibModalInstance.dismiss();
          scope.editfile(id);
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
  scope.editfile = (id) => {
    scope.modalInstance = $uibModal.open({
      animation: true,
      template: `<div class="modal-header" style="display: flex; flex-direction: row;">
            <h3 class="modal-title" id="modal-title" style="flex: 1">Add/Edit individuals</h3>
            <i class="fa fa-times" ng-click="closeModal()" style="line-height:34px;"></i>
        </div>
        <div class="modal-body" id="modal-body">
          <button class="btn btn-primary" ng-click="open(${id})">Back to Individual</button>
          <div class="fh-files">
            <file-list individual="${id}" callback="$ctrl.selectionMade(selection)" new-selection="$ctrl.selection"></file-list>
            <div class="fhdropzone" ng-hide="$ctrl.selection">
              <fhdropzone individual="${id}"><fhdropzone>
            </div>
            <div class="fhdropzone column" ng-hide="!$ctrl.selection">
              <button class="btn btn-primary" style="width: 150px; min-height: 34px; margin: 20px;" ng-click="$ctrl.selection = null;"><i class="fa fa-file-o padding"></i>Add New Files</button>
              <div style="width: 100%;">
                <edit-file callback="$ctrl.fileEdited(file)" style="width: 100%;" file="$ctrl.selection"></edit-file>
              </div>
            </div>
          </div>
        </div>`,
      controller: ($scope, $uibModalInstance) => {
        const ctrl = {};
        $scope.$ctrl = ctrl;
        ctrl.selectionMade = selectionMade;
        ctrl.fileEdited = fileEdited;

        function fileEdited(file) {
          if (file) {
            ctrl.selection = file;
          } else {
            ctrl.selection = null;
          }
        }

        function selectionMade(selection) {
          ctrl.selection = selection;
        }
        $scope.closeModal = () => {
          $uibModalInstance.dismiss();
        };
        $scope.open = () => {
          $uibModalInstance.dismiss();
          scope.open(id);
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
