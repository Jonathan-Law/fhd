module.exports = ngModule => {
  ngModule.directive('fhDropzone', /* @ngInject */ () => {
    function linkFn(/*scope, element*/) {
    }

    require('./dropzone.css');
    return {
      restrict: 'E',
      scope: {},
      template: require('./dropzone.template.html'),
      link: linkFn,
    };
  });
};
