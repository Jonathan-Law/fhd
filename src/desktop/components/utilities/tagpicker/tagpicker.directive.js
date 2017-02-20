// const _ = require('lodash');
module.exports = ngModule => {
  ngModule.directive('tagpicker', /* @ngInject */ (tagpickerService) => {
    require('./tagpicker.css');
    function preFn(scope) {
      scope.getTagTypeahead = tagpickerService.getTagTypeahead;
      scope.type = scope.type || 'person';
    }

    function compileFn() {
      return {
        pre: preFn,
        post: () => {},
      };
    }
    return {
      template: require('./tagpicker.template.html'),
      restrict: 'E',
      scope: {
        tag: '=?',
        type: '@?',
      },
      compile: compileFn,
    };
  });
};
