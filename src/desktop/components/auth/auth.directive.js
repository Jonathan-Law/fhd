// const $ = require('jquery');
// const jQuery = require('jquery');
module.exports = ngModule => {
  ngModule.directive('auth', /* @ngInject */ ($timeout, business) => {
    require('./auth.css');

    function linkFn(scope) {
      scope.login = false;
      scope.register = false;
      scope.user = null;

      scope.callback = (/*result*/) => {
        scope.login = false;
        scope.register = false;
      };

      scope.switcher = () => {
        if (scope.login) {
          scope.triggerRegister();
        } else {
          scope.triggerLogin();
        }
      };

      scope.triggerLogin = () => {
        scope.login = true;
        scope.register = false;
      };

      scope.triggerRegister = () => {
        scope.register = true;
        scope.login = false;
      };

      scope.isUserLoggedIn = () => {
        business.user.isLoggedIn().then((result) => {
          if (typeof result === 'object' && !isNaN(result.id)) {
            scope.user = result;
          } else {
            scope.user = null;
          }
        }, () => {
          scope.user = null;
          // not logged in
        });
      };

      scope.setUser = (user) => {
        scope.user = user;
      };

      business.user.subscribeToUserState(scope.setUser);


      $timeout(() => {
        scope.isUserLoggedIn();
      });
    }
    return {
      template: require('./auth.template.html'),
      restrict: 'E',
      scope: {},
      link: linkFn,
    };
  });
};
