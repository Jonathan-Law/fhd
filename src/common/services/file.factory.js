// const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function fileService($http, $q, configs) {
    const service = {
      deleteFile,
      getAllFiles,
      getByTag,
      getTags,
      updateFile,
    };

    const BASE = 'api/v1/files/';

    return service;

    function deleteFile(file) {
      if (file && file.id) {
        return $http({
          method: 'DELETE',
          url: `${configs.baseURL}${BASE}${file.id}`
        });
      }
    }

    function getAllFiles(individual) {
      return $http({
        method: 'GET',
        url: `${configs.baseURL}${BASE}getAll${individual ? '/' + individual : ''}`,
      }).then((data) => data && data.data ? data.data : [], () => []);
    }

    function getByTag(val = '', type = '', individual) {
      if (val) {
        return $http({
          method: 'GET',
          url: `${configs.baseURL}${BASE}getTypeahead/${val}/${type}/false${individual ? '/' + individual : ''}`,
        }).then((data) => data && data.data ? data.data : [], () => []);
      }
      return Promise.reject();
    }

    function getTags(id) {
      if (id) {
        return $http({
          method: 'GET',
          url: `${configs.baseURL}${BASE}getTags/${id}`,
        }).then((data) => data && data.data ? data.data : [], () => []);
      }
    }

    function updateFile(file) {
      // update file
      if (file && file.id) {
        return $http({
          method: 'POST',
          url: `${configs.baseURL}${BASE}update`,
          data: file,
        });
      }
    }
  }

  ngModule.factory('fileFactory', fileService);

  return ngModule;
};
