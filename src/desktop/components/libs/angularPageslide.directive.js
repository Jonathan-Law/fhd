module.exports = angular => {
  const ngModule = angular.module('pageslide-directive', []);
  /* eslint-disable */
  ngModule.directive('pageslide', ['$timeout', '$compile',
    function($timeout, $compile) {
      var defaults = {};

      /* Return directive definition object */

      return {
        restrict: "EA",
        // replace: false,
        // transclude: false,
        scope: {
          psOpen: "=?",
          psOther: "=?"
        },
        link: function($scope, el, attrs) {


          /* Inspect */
          //console.log($scope);
          //console.log(el);
          //console.log(attrs);

          /* parameters */
          var param = {};
          param.side = attrs.pageslide || 'right';
          param.speed = attrs.psSpeed || '0.5';
          param.size = attrs.psSize ? 'calc(100% - ' + attrs.psSize + 'px)' : '300px';
          param.className = attrs.psClass || 'ng-pageslide';
          // console.log('attrs', attrs);


          /* DOM manipulation */
          var content = null;
          if (!attrs.href && el.children() && el.children().length) {
            content = el.children()[0];
          } else {
            content = (attrs.href) ? document.getElementById(attrs.href.substr(1)) : document.getElementById(attrs.psTarget.substr(1));
          }

          // Check for content
          if (!content)
            throw new Error('You have to elements inside the <pageslide> or you have not specified a target href');
          var slider = document.createElement('div');
          var close = document.createElement('button');
          var x = document.createElement('i');
          $(close).on('click', function() {
            $scope.psOpen = false;
            $scope.$apply();
            $(slider).focus();
          });
          close.className = 'btn btn-primary';
          slider.className = param.className;
          $(slider).attr('tabindex', 0);
          $(slider).css({
            '-webkit-appearance': 'none',
            'outline': 0
          });

          /* Style setup */
          slider.style.transitionDuration = param.speed + 's';
          slider.style.webkitTransitionDuration = param.speed + 's';
          slider.style.zIndex = 1112;
          slider.style.position = 'fixed';
          slider.style.width = 0;
          slider.style.height = 0;
          slider.style['overflow-y'] = 'auto';
          slider.style['overflow-x'] = 'hidden';
          slider.style.transitionProperty = 'width, height';
          close.style.zIndex = 1113;
          close.style.position = 'fixed';
          close.style.width = '25px';
          close.style.height = '50px';
          close.style.border = '1px solid #555';
          x.style.position = 'relative';

          switch (param.side) {
            case 'right':
              slider.style.height = attrs.psCustomHeight || '100%';
              slider.style.top = attrs.psCustomTop || '0px';
              slider.style.bottom = attrs.psCustomBottom || '0px';
              slider.style.right = attrs.psCustomRight || '0px';
              close.style.top = '50%';
              x.style.left = '-5px';
              x.className = 'fa fa-arrow-right';
              close.style.right = attrs.psSize ? 'calc(100% - ' + (parseInt(attrs.psSize) + 26) + 'px)' : '274px';
              close.style['border-radius'] = '0px 50px 50px 0px';
              content.style['padding-left'] = '30px';
              $(close).append(x);
              $(content).append(close);
              // el.on('mouseleave', function() {
              //   $scope.psOpen = false;
              //   $scope.$apply();
              //   $(slider).focus();
              // })
              break;
            case 'left':
              slider.style.height = attrs.psCustomHeight || '100%';
              slider.style.top = attrs.psCustomTop || '0px';
              slider.style.bottom = attrs.psCustomBottom || '0px';
              slider.style.left = attrs.psCustomLeft || '0px';
              // close.style.top = '50%';
              // x.className = 'fa fa-arrow-left';
              // x.style.left = '-7px';
              // close.style.left = attrs.psSize? 'calc(100% - '+(parseInt(attrs.psSize) - 24) + 'px)': '274px';
              // close.style['border-radius'] = '50px 0px 0px 50px';
              // content.style['padding-right'] = '30px';
              el.on('mouseleave', function() {
                $scope.psOpen = false;
                $scope.$apply();
                $(slider).focus();
              })
              break;
            case 'top':
              slider.style.width = attrs.psCustomWidth || '100%';
              slider.style.left = attrs.psCustomLeft || '0px';
              slider.style.top = attrs.psCustomTop || '0px';
              slider.style.right = attrs.psCustomRight || '0px';
              break;
            case 'bottom':
              slider.style.width = attrs.psCustomWidth || '100%';
              slider.style.bottom = attrs.psCustomBottom || '0px';
              slider.style.left = attrs.psCustomLeft || '0px';
              slider.style.right = attrs.psCustomRight || '0px';
              break;
          }


          /* Append */
          $(el).append(slider);
          // $(close).append(x);
          // $(content).append(close);
          $(slider).append(content);

          /* Closed */
          function psClose(slider, param) {
            if (slider.style.width !== 0 && slider.style.width !== 0) {
              if (!$scope.psOpen) {
                $(content).stop(true, true).fadeOut(200);
                setTimeout(function() {
                  switch (param.side) {
                    case 'right':
                      slider.style.width = '0px';
                      slider.style.border = '0px';
                      break;
                    case 'left':
                      slider.style.width = '0px';
                      slider.style.border = '0px';
                      break;
                    case 'top':
                      slider.style.height = '0px';
                      break;
                    case 'bottom':
                      slider.style.height = '0px';
                      break;
                  }
                }, 200);
              } else {
                $(content).stop(true, true).fadeIn(0);
              }
            }
            $scope.psOpen = false;
          }

          /* Open */
          function psOpen(slider, param) {
            if (slider.style.width !== 0 && slider.style.width !== 0) {
              switch (param.side) {
                case 'right':
                  slider.style.width = param.size;
                  slider.style.border = '1px solid #555';
                  break;
                case 'left':
                  slider.style.width = param.size;
                  slider.style.border = '1px solid #555';
                  break;
                case 'top':
                  slider.style.height = param.size;
                  break;
                case 'bottom':
                  slider.style.height = param.size;
                  break;
              }
              setTimeout(function() {
                if ($scope.psOpen) {
                  $(content).stop(true, true).fadeIn(200);
                } else {
                  $(content).stop(true, true).fadeOut(0);
                }
              }, (param.speed * 1000));

            }
          }

          /*
           * Watchers
           * */

          $scope.$watch("psOpen", function(value) {
            if (!!value) {
              // Open
              psOpen(slider, param);
            } else {
              // Close
              psClose(slider, param);
            }
          });

          // close panel on location change
          if (attrs.psAutoClose) {
            $scope.$on("$locationChangeStart", function() {
              psClose(slider, param);
            });
            $scope.$on("$stateChangeStart", function() {
              psClose(slider, param);
            });
          }


          /*
           * Events
           * */

          $scope.$on('$destroy', function() {
            el[0].removeChild(slider);
          });

          $(slider).on('keyup', function(e) {
            if (e.keyCode === 27) {
              $scope.psOpen = false;
              $scope.psOther = false;
              $scope.$apply();
            }
            /* else if (e.keyCode === 37){
            if (param.side === 'left') {
              $scope.psOpen = !$scope.psOpen;
              $scope.$apply();
            } else {
              $scope.psOther = !$scope.psOther;
              $scope.$apply();
            }
          } else if (e.keyCode === 39){
            if (param.side === 'right') {
              $scope.psOpen = !$scope.psOpen;
              $scope.$apply();
            } else {
              $scope.psOther = !$scope.psOther;
              $scope.$apply();
            }
          }*/
            /* else if (e.keyCode === 38){
            $scope.psOpen = true;
            $scope.psOther = true;
            $scope.$apply();
          } else if (e.keyCode === 40){
            $scope.psOpen = false;
            $scope.psOther = false;
            $scope.$apply();
          }*/
          });

          var close_handler = (attrs.href) ? document.getElementById(attrs.href.substr(1) + '-close') : null;
          if (el[0].addEventListener) {
            el[0].addEventListener('click', function(e) {
              e.preventDefault();
              psOpen(slider, param);
            });

            if (close_handler) {
              close_handler.addEventListener('click', function(e) {
                e.preventDefault();
                psClose(slider, param);
              });
            }
          } else {
            // IE8 Fallback code
            el[0].attachEvent('onclick', function(e) {
              e.returnValue = false;
              psOpen(slider, param);
            });

            if (close_handler) {
              close_handler.attachEvent('onclick', function(e) {
                e.returnValue = false;
                psClose(slider, param);
              });
            }
          }

        }
      };
    }
  ]);
  /* eslint-enable */
  return ngModule;
};
