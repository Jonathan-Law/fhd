// const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function fileService($http, $q, configs) {
    const service = {};

    service.getAllFiles = () => {
      return $http({
        method: 'GET',
        url: `${configs.baseURL}api/v1/file/getAll`,
      }).then((data) => data && data.data ? data.data : [], () => []);
    };

    service.getByTag = (val = '', type = '') => {
      if (val) {
        return $http({
          method: 'GET',
          url: `${configs.baseURL}api/v1/file/getTypeahead/${val}/${type}/false`,
        }).then((data) => data && data.data ? data.data : [], () => []);
      }
      return Promise.reject();
    };

    service.getTags = (id) => {
      if (id) {
        return $http({
          method: 'GET',
          url: `${configs.baseURL}api/v1/file/getTags/${id}`,
        }).then((data) => data && data.data ? data.data : [], () => []);
      }
    };

    return service;
  }

  ngModule.factory('fileFactory', fileService);

  return ngModule;
};
