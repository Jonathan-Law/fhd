// const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function authService() { /*jshint unused: false*/
    const auth = {};
    return auth;
  }

  ngModule.factory('authFactory', authService);

  return ngModule;
};
