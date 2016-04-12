const _ = require('lodash');
const $ = require('jquery');
module.exports = ngModule => {
  ngModule.directive('family', /* @ngInject */ (business, $timeout, $compile) => {
    require('./familyTree.css');

    function linkFn(scope, element) {
      const Business = business;
      scope.data = {};
      scope.data.init = false;
      scope.data.treeReady = false;
      scope.$broadcast('$LOAD', 'treeHolderLoader');
      scope.getTitle = () => {
        if (scope.family && scope.family.self && scope.family.self.displayableName) {
          return scope.family.self.displayableName + ' Family Chart';
        }
        return 'Family Chart';
      };
      function addParents(parents, person, size) {
        if (parents && parents.length) {
          let list = person.find('ul');
          if (list.length === 0) {
            list = person.append('<ul></ul>').find('ul');
          }
          _.each(parents, (parent) => {
            const tempsize = (size - 50) >= 25 ? (size - 50) : 25;
            const temp = list.append('<li id="person' + parent.id + '"><a class="zoomable"><individual classes="" person="' + parent.id + '" mode="picture" zoomable="true" initialsize="' + size + 'px"></individual></a></li>').find('#person' + parent.id);
            return addParents(parent.parents, temp, tempsize);
          });
        }
        return;
      }
      let treeRefreshTimer;
      function resizeTree() {
        scope.data.treeReady = true;
        clearTimeout(treeRefreshTimer);
        $('.tree').css('width', 6000);
        treeRefreshTimer = setTimeout(() => {
          $('.tree').css('width', $('.tree').find('ul').find('li').width() + 100);
          setTimeout(() => {
            const treeHolderWidth = $('#treeHolder').width();
            const zoomableIndWidth = $('.zoomableInd').width();
            if (treeHolderWidth < zoomableIndWidth) {
              $('#treeHolder').scrollLeft((zoomableIndWidth - treeHolderWidth) / 2);
            }
            scope.$broadcast('$UNLOAD', 'treeHolderLoader');
            scope.$apply();
          }, 100);
        }, 1000);
      }

      let resize;
      $(window).resize(() => {
        clearTimeout(resize);
        resize = setTimeout(() => {
          scope.$broadcast('$LOAD', 'treeHolderLoader');
          scope.data.treeReady = false;
          $timeout(() => {
            resizeTree();
          });
        }, 500);
      });

      scope.$on('$CHARTRESIZE', () => {
        scope.$broadcast('$LOAD', 'treeHolderLoader');
        scope.data.treeReady = false;
        if (!scope.data.init) {
          scope.doThisOnce();
        } else {
          $timeout(() => {
            resizeTree();
          });
        }
      });
      scope.doThisOnce = () => {
        scope.data.init = true;
        Business.individual.getFamily(scope.personId).then((family) => {
          if (family && family.parents && family.parents.length) {
            scope.data.treeReady = false;
            scope.$broadcast('$LOAD', 'treeHolderLoader');
            scope.family = family ? family : [];
            const base = element.find('#treeHolder');
            const list = base.find('.tree');
            const root = list.append('<ul></ul>').find('ul');
            const person = root.append('<li><a class="zoomable"><individual classes="" person="' + scope.personId + '" mode="picture" zoomable="true" initialsize="150px"></individual></a></li>').find('li');
            addParents(family.parents, person, 125);
            const e = angular.element(base);
            $compile(e.contents())(scope);


            element.replaceWith(e);
            const allImgs = document.getElementById('treeHolder').getElementsByTagName('img');
            let allImgsLength = allImgs.length;
            let i;
            const eventCallback = () => {
              if (!(--allImgsLength)) {
                resizeTree();
              }
            };

            for (i = 0; i < allImgsLength; i++) {
              if (allImgs[i].complete) {
                allImgsLength--;
              }
              if (allImgs[i].addEventListener) {
                allImgs[i].addEventListener('load', eventCallback);
              } else if (allImgs[i].attachEvent) {
                allImgs[i].attachEvent('onload', eventCallback);
              } else {
                allImgs[i].onload = eventCallback;
              }
            }
          } else {
            scope.family = [];
            const base = element.find('#treeHolder');
            const list = base.find('.tree');
            list.append('<div>This individual has not yet been paired with his or her parents.</div>');
            const e = angular.element(base);
            $compile(e.contents())(scope);
            element.replaceWith(e);
            scope.data.treeReady = true;

            scope.$broadcast('$UNLOAD', 'treeHolderLoader');
          }
        }, () => {
          scope.family = [];
          const base = element.find('#treeHolder');
          const list = base.find('.tree');
          list.append('<div>This individual has not yet been paired with his or her parents.</div>');
          const e = angular.element(base);
          $compile(e.contents())(scope);
          element.replaceWith(e);
          scope.data.treeReady = true;
          scope.$broadcast('$UNLOAD', 'treeHolderLoader');
        });
        //
      };
    }

    return {
      template: require('./familyTree.template.html'),
      restrict: 'EA',
      scope: {
        personId: '='
      },
      link: linkFn,
    };
  });
};
