module.exports = angular => {
  const ngModule = angular.module('da.desktop.components', []);


  ngModule.directive('backImg', () => {
    return (scope, element, attrs) => {
      attrs.$observe('backImg', (value) => {
        element.css({
          'background-image': 'url("' + value + '")',
          'background-size': 'contain',
          '-moz-background-size': 'contain'
        });
      });
    };
  });

  ngModule.filter('slice', () => {
    return (arr, start, end) => {
      return (arr || []).slice(start, end);
    };
  });


  return ngModule;
};
