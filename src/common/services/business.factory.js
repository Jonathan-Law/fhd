const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function businessFn($http, configs, individual, user, fileFactory) {
    // 60 seconds until expiration
    // const expireTime = 60 * 1000;
    const business = {};
    business.user = user;
    business.individual = individual;
    business.file = fileFactory;

    business.getTypeahead = (val, limit) => {
      const limNum = isNaN(limit) ? 10 : +limit;
      return $http.get(configs.baseURL + 'api/v1/core/typeahead/', {
        params: {
          typeahead: val,
          limit: limNum,
          sensor: false
        }
      }).then((res) => {
        if (res.data !== 'false') {
          const typeahead = [];
          _.each(res.data, (item) => {
            typeahead.push(item);
          });
          return typeahead;
        }
        return [];
      });
    };

    return business;
  }
  ngModule.factory('business', businessFn);

  return ngModule;
};
