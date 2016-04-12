module.exports = ngModule => {
  ngModule.directive('breadcrumbs', /* @ngInject */ ($state) => {
    require('./breadcrumbs.css');
    function linkFn(scope) {
      scope.show = true;
      scope.$watch('ngModel', () => {
        if (scope.ngModel && scope.ngModel.letter) {
          scope.show = true;
        }
      });

      scope.goToLetter = () => {
        if (scope.ngModel && scope.ngModel.letter) {
          $state.go('families', { letter: scope.ngModel.letter });
        }
      };

      scope.goToFamily = () => {
        if (scope.ngModel && scope.ngModel.family) {
          $state.go('family', { name: scope.ngModel.family });
        }
      };

      scope.goToIndividual = () => {
        if (scope.ngModel && scope.ngModel.individual) {
          $state.go('individual', {
            id: scope.ngModel.individual.id,
            tab: 'default',
          });
        }
      };
    }
    return {
      template: require('./breadcrumbs.template.html'),
      restrict: 'A',
      scope: {
        ngModel: '='
      },
      link: linkFn,
    };
  });
};
