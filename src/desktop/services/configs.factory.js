// const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function configsFn() {
    const configs = {};
    configs.baseURL = 'http://familyhistorydatabase.org/';
    return configs;
  }

  ngModule.factory('configs', configsFn);

  return ngModule;
};
