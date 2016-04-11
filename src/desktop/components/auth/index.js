module.exports = angular => {
  const ngModule = angular.module('da.desktop.auth', [
    require('../utilities.js')(angular).name
  ]);

  require('./login/login.directive.js')(ngModule);
  require('./register/register.directive.js')(ngModule);
  require('./auth.directive.js')(ngModule);

  return ngModule;
};
