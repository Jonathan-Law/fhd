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

  /***************************************************************
  * This directive is used to inject html into an element through binding
  * just like an 'ng-bind-html'. The only difference is that it will compile
  * the contents so that it works with it's parent scope.
  *
  * The scope variable must be sent through '$sce' before being compiled here:
  *
  * NOTE: This is unsafe to use with any type of user content.... (its just nice to have
  *       when you need to make dynamic content)
  *
  * Scope Controller:
  * ~~~~~
  * $scope.scopevariable = $sce.trustAsHtml("<button>Hello World</button>");
  *
  * HTML:
  * ~~~~~
  * <div dynamichtml='scopevariable'></div>
  ***************************************************************/
  ngModule.directive('dynamichtml', /* ngInject */ ($compile) => {
    return {
      restrict: 'A',
      replace: true,
      link: (scope, ele, attrs) => {
        scope.$watch(attrs.dynamichtml, (html) => {
          ele.html(html.toString());
          $compile(ele.contents())(scope);
        });
      }
    };
  });

  return ngModule;
};
