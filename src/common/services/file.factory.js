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

    return service;

    function deleteFile(file) {
      if (file && file.id) {
        return $http({
          method: 'DELETE',
          url: `${configs.baseURL}api/v1/file/${file.id}`
        });
      }
    }

    function getAllFiles() {
      return $http({
        method: 'GET',
        url: `${configs.baseURL}api/v1/file/getAll`,
      }).then((data) => data && data.data ? data.data : [], () => []);
    }

    function getByTag(val = '', type = '') {
      if (val) {
        return $http({
          method: 'GET',
          url: `${configs.baseURL}api/v1/file/getTypeahead/${val}/${type}/false`,
        }).then((data) => data && data.data ? data.data : [], () => []);
      }
      return Promise.reject();
    }

    function getTags(id) {
      if (id) {
        return $http({
          method: 'GET',
          url: `${configs.baseURL}api/v1/file/getTags/${id}`,
        }).then((data) => data && data.data ? data.data : [], () => []);
      }
    }

    function updateFile(file) {
      // update file
      if (file && file.id) {
        return $http({
          method: 'POST',
          url: `${configs.baseURL}api/v1/file/update`,
          data: file,
        });
      }
    }
  }

  ngModule.factory('fileFactory', fileService);

  return ngModule;
};
