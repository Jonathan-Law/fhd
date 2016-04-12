// const _ = require('lodash');
module.exports = ngModule => {
  ngModule.directive('placepicker', /* @ngInject */ (placepickerService) => {
    let uniqueId = 0;
    require('./placepicker.css');

    function preFn(scope) {
      scope.placeId = 'place_' + uniqueId++;
      scope.$watch('place', () => {
        if (scope.place && typeof scope.place === 'object') {
          let town;
          let state;
          let country;
          let county;
          town = scope.place.town ? scope.place.town : '';
          state = scope.place.state ? scope.place.state : '';
          country = scope.place.country ? scope.place.country : '';
          county = scope.place.county ? scope.place.county : '';
          scope.tempplace = {
            'formatted_address': '' + town + ', ' + county + ', ' + state + ', ' + country,
            'address_components': [{
              'long_name': town
            }, {
              'long_name': county
            }, {
              'long_name': state
            }, {
              'long_name': country
            }]
          };
        } else if (scope.place !== false) {
          scope.tempplace = '';
        }
      });

      scope.getLocation = (val) => {
        return placepickerService.getLocation(val);
      };

      scope.$watch('tempplace', () => {
        if (scope.tempplace && typeof scope.tempplace === 'object') {
          if (scope.form === 'full') {
            scope.place = scope.tempplace;
          } else {
            scope.place = {};
            scope.place.town = scope.tempplace.address_components[0].long_name;
            scope.place.county = scope.tempplace.address_components[1].long_name;
            scope.place.state = scope.tempplace.address_components[2].long_name;
            scope.place.country = scope.tempplace.address_components[3].long_name;
          }
        } else if (scope.tempplace && scope.tempplace !== '') {
          const list = scope.tempplace.split(',');
          if (list.length < 4) {
            return false;
          }
          scope.place = {};
          scope.place.town = list[0].trim();
          scope.place.county = list[1].trim();
          scope.place.state = list[2].trim();
          scope.place.country = list[3].trim();
        } else {
          scope.place = false;
        }
      }, true);
    }

    function compileFn() {
      return {
        pre: preFn,
        post: () => {},
      };
    }
    return {
      template: require('./placepicker.template.html'),
      restrict: 'E',
      scope: {
        place: '=?',
      },
      compile: compileFn,
    };
  });
};
