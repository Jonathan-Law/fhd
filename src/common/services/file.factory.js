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

    service.getByTag = (val = '', type = 'person') => {
      if (val && type) {
        return $http({
          method: 'GET',
          url: `${configs.baseURL}/api/v1/file/getTypeahead/${val}/${type}/false`,
        }).then((data) => data && data.data ? data.data : [], () => []);
      }
      return Promise.reject();
    };

    // service.getTypeahead = function(val, switchTrigger){
    //   let deferred = $q.defer();
    //   if (val) {
    //     let body;
    //     if (typeof val === 'object' && switchTrigger === 'place'){
    //       body = {
    //         method: 'GET',
    //         params: {
    //           'place': JSON.stringify(val)
    //         }
    //       };
    //       val = 'object';
    //       body.url = '/api/v1/file/getTypeahead/'+val+'/'+switchTrigger;
    //     } else {
    //       body= {
    //         method: 'GET',
    //         url: '/api/v1/file/getTypeahead/'+val+'/'+switchTrigger,
    //       };
    //     }
    //     $http(body).success(function(data, status, headers, config){
    //       if (data) {
    //         // console.log('data', data)
    //         for (let i = data.length - 1; i >= 0; i--) {
    //           if (data[i].title) {
    //             data[i].typeahead = data[i].title;
    //           }
    //         };
    //         deferred.resolve(data);
    //       }
    //     }).error(function(data, staus, headers, config){
    //       deferred.reject('The typeahead failed');
    //     })
    //   }
    //   return deferred.promise;
    // }

    return service;
  }

  ngModule.factory('fileFactory', fileService);

  return ngModule;
};
