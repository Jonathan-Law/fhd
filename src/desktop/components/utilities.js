module.exports = angular => {
  const ngModule = angular.module('da.desktop.utilities', []);


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


  ngModule.directive('enterEvent', () => {
    return (scope, element, attrs) => {
      element.bind('keydown keypress', (event) => {
        if (event.which === 13) {
          scope.$apply(() => {
            scope.$eval(attrs.enterEvent, { event });
          });
          event.preventDefault();
        }
      });
    };
  });


  return ngModule;
};
