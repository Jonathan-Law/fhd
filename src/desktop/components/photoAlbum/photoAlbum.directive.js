const $ = require('jquery');
const jQuery = require('jquery');
module.exports = ngModule => {
  ngModule.directive('photoalbum', /* @ngInject */ (business, $timeout, configs) => {
    require('./photoAlbum.css');

    function linkFn(scope, element) {
      const Business = business;

      scope.isUserAdmin = false;
      scope.configs = configs;

      scope.getIsAdmin = () => {
        Business.user.getIsAdmin().then((result) => {
          scope.isUserAdmin = result;
        });
      };

      scope.$on('$LOGGEDIN', () => {
        scope.getIsAdmin();
      });

      scope.setProfilePicture = () => {
        Business.individual.setProfilePic(scope.id, scope.focus.id).then((/*result*/) => {
          // console.log(result);
        });
      };

      $(window).on('keydown', (e) => {
        if (e.keyCode === 37 || e.keyCode === 38) { //left or up
          if (scope.start > 0 && scope.active === 0) {
            scope.stop--;
            scope.start--;
            scope.setActiveImage(scope.active, scope.pictures[scope.start]);
          } else if (scope.start > 0) {
            scope.setActiveImage(scope.active - 1, scope.pictures[(scope.start + scope.active) - 1]);
          } else if (scope.active > 0) {
            scope.setActiveImage(scope.active - 1, scope.pictures[(scope.start + scope.active) - 1]);
          }
          scope.$apply();
        }
        if (e.keyCode === 39 || e.keyCode === 40) { //right or down
          if (scope.stop < scope.pictures.length && scope.active === 4) {
            scope.setActiveImage(scope.active, scope.pictures[scope.stop]);
            scope.stop++;
            scope.start++;
          } else if (scope.stop < scope.pictures.length) {
            scope.setActiveImage(scope.active + 1, scope.pictures[(scope.start + scope.active) + 1]);
          } else if (scope.active < 4) {
            scope.setActiveImage(scope.active + 1, scope.pictures[(scope.start + scope.active) + 1]);
          }
          scope.$apply();
        }
      });

      scope.$on('$destroy', function handleDestroyEvent() {
        // console.log('we destroyed the photo-album');
        $(window).off('keydown', () => {

        });
      });

      scope.interval = 1;
      scope.size = 5;

      scope.start = 0;
      scope.stop = scope.size;

      scope.active = 0;

      scope.moreAfter = () => {
        return (scope.pictures.length - scope.start) > scope.size;
      };
      scope.moreBefore = () => {
        return scope.start > 0;
      };
      let timeout;
      $(window).resize(() => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          scope.setDimensions();
          scope.setActiveImage(scope.active, scope.focus);
        }, 500);
      });

      scope.setDimensions = () => {
        scope.tempWidth = element.find('#display').width();
        scope.tempHeight = 600;
      };


      const calculateAspectRatioFit = (srcWidth, srcHeight, maxWidth, maxHeight) => {
        let ratio = [maxWidth / srcWidth, maxHeight / srcHeight];
        ratio = Math.min(ratio[0], ratio[1]);
        scope.imgWidth = srcWidth * ratio;
        scope.imgHeight = (srcHeight * ratio) - 2;
        scope.$apply();
      };

      jQuery.fn.animateAuto = function animateRatio(prop, speed, callback) {
        let elem;
        let height;
        let width;
        return this.each((i, el1) => {
          const el = jQuery(el1);
          elem = el.clone().css({
            'height': 'auto',
            'width': 'auto'
          }).appendTo('body');
          height = elem.css('height');
          width = elem.css('width');
          elem.remove();

          if (prop === 'height') {
            el.animate({
              height,
            }, speed, callback);
          } else if (prop === 'width') {
            el.animate({
              width,
            }, speed, callback);
          } else if (prop === 'both') {
            el.animate({
              width,
              height,
            }, speed, callback);
          }
        });
      };

      // element.find('#display').on('mouseenter', ()=> {
      //   $('.photoAlbumData').stop(true, true).animate({
      //     backgroundColor: 'rgba(102,102,76,.95)',
      //   }, 150, ()=> {
      //     //animation complete
      //   }).animateAuto('height', 150);
      // })
      // element.find('#display').on('mouseleave', ()=> {
      //   $('.photoAlbumData').stop(true, true).animate({
      //     'height': '100%',
      //     backgroundColor: 'rgba(0,0,0,.6)',
      //   }, 150, ()=> {
      //         //animation complete
      //       });
      // })
      //
      scope.setActiveImage = (index, image) => {
        scope.active = index;
        scope.focus = image;
        const img = new Image();
        img.onload = function calculateAspectRatio() {
          calculateAspectRatioFit(this.width, this.height, scope.tempWidth, scope.tempHeight);
        };
        img.src = configs.baseURL + '/' + scope.focus.link;
      };

      scope.openInNewWindow = () => {
        window.open(configs.baseURL + '/' + scope.focus.link);
      };

      scope.print = () => {
        const content = element.find('#display').html();
        $('#printOnly').html(content);
        window.print();
      };

      scope.getDownload = () => {
        const url = configs.baseURL + '/' + scope.focus.link;
        const download = scope.focus.link.replace('upload/', '');
        const a = $('<a>').attr('href', url).attr('download', download).appendTo('body');
        a[0].click();
        a.remove();
      };

      scope.pictures = [];
      Business.individual.getPictures(scope.id).then((result) => {
        scope.pictures = result ? result : [];
        if (scope.pictures.length) {
          $timeout(() => {
            scope.setDimensions();
            scope.setActiveImage(0, scope.pictures[0]);
          }, 300);
        }
      }, () => {
        scope.pictures = [];
      });


      element.find('#thumbnails').on('mousewheel DOMMouseScroll', (e) => {
        if (e.originalEvent.wheelDelta / 120 > 0) {
          if (scope.moreBefore()) {
            scope.start = scope.start - scope.interval;
            scope.stop = scope.stop - scope.interval;
            scope.active++;
          }
        } else {
          if (scope.moreAfter()) {
            scope.start = scope.start + scope.interval;
            scope.stop = scope.stop + scope.interval;
            scope.active--;
          }
        }
        scope.$apply();
      });
    }


    return {
      template: require('./photoAlbum.template.html'),
      restrict: 'EA',
      scope: {
        id: '='
      },
      link: linkFn,
    };
  });
};
