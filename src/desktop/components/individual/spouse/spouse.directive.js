module.exports = ngModule => {
  ngModule.directive('spouse', /* @ngInject */ (business) => {
    require('./spouse.css');

    function linkFn(scope) {
      business.individual.getSpouses(scope.ngModel.id, scope.individual).then((result) => {
        if (result) {
          if (result.year && result.year !== '') {
            let dateString = '';
            dateString = dateString + ((result.month && result.month !== '') ? result.month : '1');
            dateString = dateString + '-' + ((result.day && result.day !== '') ? result.day : '1');
            dateString = dateString + '-' + ((result.year) ? result.year : '1700');
            scope.ngModel.marriageDate = new Date(dateString);
          }
          if (result.yearM === '1' || result.yearM === 'true') {
            scope.ngModel.exactMarriageDate = true;
          }

          business.individual.getPlace(result.place).then((place) => {
            scope.ngModel.marriagePlace = place;
          });
        }
      }, () => {
      });
    }
    return {
      template: require('./spouse.template.html'),
      scope: {
        individual: '=',
        ngModel: '=',
        callback: '&'
      },
      restrict: 'E',
      link: linkFn,
    };
  });
};
