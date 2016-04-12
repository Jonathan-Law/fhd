const _ = require('lodash');
module.exports = ngModule => {
  ngModule.directive('datepicker', /* @ngInject */ ($timeout/*datepickerFactory*/) => {
    require('./datepicker.css');

    function preFn(scope) {
      const disabled = false;
      $timeout(() => {
        scope.confident = scope.confident === undefined ? true : +scope.confident;
      });
      scope.today = () => {
        scope.dt = new Date();
      };

      scope.clear = () => {
        scope.dt = null;
      };

      scope.updateOptions = () => {
        scope.dateOptions = _.merge({}, {
          dateDisabled: disabled,
          formatYear: 'yyyy',
          startingDay: 1,
          yearRows: 10,
          yearColumns: 5,
        }, scope.options || {});
        if (!scope.confident) {
          scope.dateOptions.minMode = 'year';
          scope.format = 'YYYY';
          scope.formatConfig = 'yyyy';
        } else {
          scope.dateOptions.minMode = 'day';
          scope.format = 'MM-DD-YYYY';
          scope.formatConfig = 'MM-dd-yyyy';
        }
      };

      scope.$watch('confident', (newval) => {
        if (!+newval) {
          scope.dateOptions.minMode = 'year';
          scope.format = 'YYYY';
          scope.formatConfig = 'yyyy';
        } else {
          scope.dateOptions.minMode = 'day';
          scope.format = 'MM-DD-YYYY';
          scope.formatConfig = 'MM-dd-yyyy';
        }
      });

      scope.updateOptions();

      scope.popup = {
        opened: false
      };

      scope.open = () => {
        scope.popup.opened = true;
      };

      scope.setDate = (year, month, day) => {
        scope.dt = new Date(year, month, day);
      };

      scope.$watch('options', (newval) => {
        if (newval) {
          scope.updateOptions();
        }
      }, true);

      // scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
      // scope.format = scope.formats[0];
      // scope.altInputFormats = ['M!/d!/yyyy'];
    }

    function compileFn() {
      return {
        pre: preFn,
        post: () => {},
      };
    }
    return {
      template: require('./datepicker.template.html'),
      restrict: 'E',
      scope: {
        dt: '=?date',
        options: '=?',
        confident: '=?',
        isRequired: '=?',
        placeholder: '@?',
      },
      compile: compileFn,
    };
  });
};
