const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function businessFn($http, configs, individual) {
    // 60 seconds until expiration
    // const expireTime = 60 * 1000;
    const business = {};
    // business.user = UserService;
    // business.auth = AuthService;

    business.individual = individual;
    // business.file = FileService;

    business.getTypeahead = (val) => {
      return $http.get(configs.baseURL + 'api/v1/typeahead/', {
        params: {
          typeahead: val,
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

    business.getLocation = (val) => {
      return $http.get('http://maps.googleapis.com/maps/api/geocode/json', {
        params: {
          address: val,
          sensor: false
        }
      }).then((res) => {
        const addresses = [];
        angular.forEach(res.data.results, (item) => {
          addresses.push(item);
        });
        return addresses;
      });
    };

    business.getOtherTypeahead = (val) => {
      return $http.get(configs.baseURL + 'api/v1/tags/other', {
        params: {
          typeahead: val,
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
