// const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function fileService() {
    const service = {};

    // service.getFileData = function (id){
    //   const deferred = $q.defer();
    //   if (id) {
    //     $http({
    //       method: 'GET',
    //       url: '/api/v1/file/' + id,
    //     }).success(function(data, status, headers, config) {
    //       if (data !== "false") {
    //         deferred.resolve(data);
    //       } else {
    //         deferred.resolve([]);
    //       }
    //     });
    //   } else {
    //     deferred.resolve([]);
    //   }
    //   return deferred.promise;
    // };

    // service.updateFile = function (data){
    //   const deferred = $q.defer();
    //   if (data) {
    //     $http({
    //       method: 'POST',
    //       url: '/api/v1/file/update',
    //       data: data
    //     }).success(function(data, status, headers, config) {
    //       if (data !== "false") {
    //         deferred.resolve(data);
    //       } else {
    //         deferred.resolve(false);
    //       }
    //     });
    //   } else {
    //     deferred.resolve(false);
    //   }
    //   return deferred.promise;
    // };

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
