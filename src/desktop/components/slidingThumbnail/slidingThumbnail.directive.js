const $ = require('jquery');
require('../libs/hoverDir.js');
module.exports = ngModule => {
  ngModule.directive('slidingThumbnail', /* @ngInject */ () => {
    function linkFn(scope, element) {
      scope.direction = 'Initial';
      if (scope.id) {
        // Business.individual.getProfilePicByPersonId(scope.id).then(function(result) {
        // if (result) {
          // console.log('result', result);
        scope.profilePic = 'https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png'; //result.viewlink;
        // }
        // });
      }
      $(element).find('.dathumbsContainer').hoverdir();
    }

    require('./slidingThumbnail.css');
    return {
      restrict: 'E',
      scope: {
        params: '=',
        id: '=',
        callback: '=?'
      },
      template: require('./slidingThumbnail.html'),
      link: linkFn,
    };
  });
};
