const $ = require('jquery');
const _ = require('lodash');
const utils = require('../../components/libs/utils');
/* ngInject */
function individualCtrl($scope, $state, $location, Business, $timeout, configs) {
  const ind = this;
  ind.id = ($state.params.constructor === Object && angular.equals($state.params, {}) || !$state.params.id) ? null : $state.params.id;
  $scope.$watch(angular.bind(ind, () => {
    return ind.id;
  }), (newval) => {
    $scope.indId = newval;
  });
  $scope.configs = configs;
  $scope.family = null;
  $scope.famLetter = null;
  $scope.letter = null;
  $scope.individual = null;
  $scope.setFocus = false;
  $scope.data = {};
  $scope.view = {};
  $scope.view.trigger = 'default';
  $scope.spouses = [];
  $scope.spouse = null;
  $scope.currentSpouse = 0;
  $scope.$broadcast('$UNLOAD', 'childLoader');

  $scope.$watch('view', (view) => {
    if (view && view.trigger) {
      // console.log('view', view.trigger);
    }
  }, true);

  $scope.cycleNext = () => {
    switch ($scope.view.trigger) {
      case 'photoAlbum':
        $scope.changeTrigger('documents');
        break;
      case 'documents':
        $scope.changeTrigger('default');
        break;
      case 'default':
        $scope.changeTrigger('photoAlbum');
        break;
      default:
        $scope.changeTrigger('default');
        break;
    }
  };

  $scope.getLoc = () => {
    switch ($scope.view.trigger) {
      case 'photoAlbum':
        return 'Photo Album';
      case 'documents':
        return 'Documents';
      default:
        return 'Home';
    }
  };

  $scope.changeTrigger = (val) => {
    $scope.view.trigger = val;
    const temp = $location.search();
    temp.tab = val;
    $location.search(temp);
  };

  $scope.triggerChartResize = () => {
    $scope.$emit('$TRIGGEREVENT', '$CHARTRESIZE');
  };

  $scope.addToSearch = (attribute, value) => {
    const search = $location.search();
    search[attribute] = value;
    $location.search(search);
  };

  function compareDisplayNames(a, b) {
    if (a.displayableName === b.displayableName) {
      return 0;
    } else if (a.displayableName > b.displayableName) {
      return 1;
    }
    return -1;
  }

  $scope.getIndData = (id, indObj) => {
    if (indObj) {
      Business.individual.getIndData(id).then((result) => {
        if (result) {
          $scope.data = angular.copy(result);
          $scope.data.birth = angular.copy(utils.date().set($scope.data.birth));
          $scope.data.death = angular.copy(utils.date().set($scope.data.death));
          $scope.data.burial = angular.copy(utils.date().set($scope.data.burial));
          $scope.pretty = JSON.stringify($scope.data, null, 4);
          $scope.links = {};

          $scope.links.letter = $scope.data.lastName.charAt(0);
          $scope.links.family = $scope.data.lastName;
          $scope.links.individual = $scope.data;

          $scope.getSpouses($scope.data.spouse);
        } else {
          $scope.noData = 'We could not grab the individual\'s data.';
        }
      }, () => {
        $scope.noData = 'We could not grab the individual\'s data.';
      });
    } else {
      return Business.individual.getIndData(id);
    }
  };

  $scope.getChildren = (father, mother) => {
    Business.individual.getChildren(father, mother).then(() => {
      // console.log('children', result);
    });
  };

  $scope.getSpouses = (spouses) => {
    $scope.$broadcast('$LOAD', 'spouseLoader');
    setTimeout(() => {
      let total = spouses.length;
      if (spouses.length) {
        _.each(spouses, (spouse) => {
          $scope.getIndData(spouse.personId, false).then((result) => {
            result.displayName = true;
            if (result) { $scope.spouses.push(result); }
            if (!(--total)) {
              $('#spouseHolder').css('width', 10000);
              setTimeout(() => {
                $('#spouseHolder').css('width', $('#spouseHolderInner').width() + 10);
                $scope.spouses = $scope.spouses.sort(compareDisplayNames);
                $scope.setKids($scope.spouses[0]);
                $scope.$broadcast('$UNLOAD', 'spouseLoader');
                $scope.$apply();
              }, 1000);
              //stop the loading.
            }
          }, () => {
            $scope.$broadcast('$UNLOAD', 'spouseLoader');
          });
        });
      } else {
        $scope.spouses = $scope.spouses.sort(compareDisplayNames);
        $scope.setKids(null);
        $scope.$broadcast('$UNLOAD', 'spouseLoader');
        $scope.$apply();
      }
    });
    return; //
  };

  $scope.setKids = (spouse) => {
    $scope.$broadcast('$LOAD', 'childLoader');
    if (spouse) {
      $scope.spouse = spouse;
      // console.log('spouse', $scope.spouse);
      // console.log('individual', $scope.data);
      Business.individual.getChildren($scope.data.id, $scope.spouse.id).then((tempResult) => {
        // console.log('result', result);
        const result = tempResult.sort(compareDisplayNames);
        _.each(result, (child) => {
          child.displayName = true;
        });
        $scope.children = result;
        $timeout(() => {
          $scope.$broadcast('$UNLOAD', 'childLoader');
        }, 500);
      }, () => {
        // console.log('Children call failed');
      });
    } else {
      $scope.$broadcast('$UNLOAD', 'childLoader');
    }
  };

  $scope.$watch('individual', () => {
    if ($scope.individual) {
      $scope.getIndData($scope.individual, true);
      Business.individual.getPictures($scope.individual).then((result) => {
        $scope.pictures = result ? result : [];
      }, () => {
        $scope.pictures = [];
      });
    }
  });

  $scope.$watch('openFam', () => {
    if (!$scope.openFam) {
      $scope.setFocus = true;
    }
  });

  $scope.$watch('openInd', () => {
    if (!$scope.openFam) {
      $scope.setFocus = true;
    }
  });

  $scope.goBackToLetter = (letter) => {
    $state.params({
      letter,
    });
    $state.go('families');
  };

  $scope.goBackToFamily = (familyName) => {
    $state.params({
      'name': familyName
    });
    $state.go('/family');
  };

  if ($state.params) {
    $scope.individual = $state.params.id ? $state.params.id : null;
    $scope.view.trigger = $location.search().tab ? $location.search().tab : 'default';
    if (!$location.search().tab) {
      const temp = $location.search();
      temp.tab = 'default';
      $location.search(temp);
    }
    if ($scope.individual === null) {
      $scope.goBackToLetter('a');
    }
  }
}

// inject dependencies here
individualCtrl.$inject = ['$scope', '$state', '$location', 'business', '$timeout', 'configs'];

module.exports = individualCtrl;
