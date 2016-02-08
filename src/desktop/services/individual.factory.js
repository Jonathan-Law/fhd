// const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function individual(configs, $http, $q) { /*jshint unused: false*/
    const service = {};

    // var minute = 60 * 1000;
    // var componentservice = {};

    // /***************************************************************
    // * This function is used to check the localCache for the existance of a result
    // * object that hasn't yet expired
    // * params: name -- The unique identifier for the entry in the local cache (usually a string)
    // * params: expire -- The ammount of time in ms that it will take for the object to expire
    // * returns: result -- The value of the object if it has not yet expired, and null for
    // *                    result objects that are no longer valid
    // ***************************************************************/
    // var checkExpire = function(name, expire) {
    //   var result = localCache.get(name, 'object');
    //   var cacheTime = null;
    //   if (result) {
    //     cacheTime = localCache.get(name+'-time', 'date');
    //     var timeDiff = new Date() - cacheTime;
    //     if (timeDiff < expire) {
    //       return result;
    //     } else {
    //       return null;
    //     }
    //   }
    //   return null;
    // };

    // /***************************************************************
    // * We use this function in conjunction with the checkExpire function.
    // * Use this function to save the value in the local cache (it will also save
    // * an expire time that it can use later to check validity of an entry)
    // * params: name -- The unique identifier for the entry in the local cache (usually a string)
    // * params: value -- The value of the data that you will be storing
    // ***************************************************************/
    // var save = function(name, value) {
    //   localCache.save(name, value);
    //   localCache.save(name+'-time', new Date());
    // };


    // var updateCache = function(name, value) {
    //   save(name, value);
    // };


    // var handleQueue = function(current) {
    //   var deferred = $q.defer();
    //   setTimeout(function(){
    //     if (current) {
    //       $http({
    //         method: current.method,
    //         url: current.url,
    //         params: current.params,
    //         data: current.data
    //       }).success(function(data, status, headers, config) {
    //         if (data !== "false" && !isMaxError(data)) {
    //           save(current.saveName, data);
    //           deferred.resolve(data);
    //         } else {
    //           if (isMaxError(data)) {
    //             deferred.resolve(handleQueue(current));
    //           } else {
    //             deferred.reject([]);
    //           }
    //         }
    //       }).error(function(data, status, headers, config){
    //         deferred.reject(data);
    //       });
    //     }
    //   }, 500);
    //   return deferred.promise;
    // }

    // var isMaxError = function (result) {
    //   return (typeof result === 'object')? (result.error)? (_.contains(result.error, 'SQLSTATE[42000] [1203]'))? true:false :false : false;
    // }

    // service.getIndData = function (id, override){
    //   var deferred = $q.defer();
    //   if (id)
    //   {
    //     var url = '/api/v1/individual/' + id;
    //     var value = null;
    //     value = checkExpire('indData'+id, minute * 2);
    //     if (value && !override) {
    //       deferred.resolve(value);
    //     } else {
    //       $http({
    //         method: 'GET',
    //         url: url,
    //       }).success(function(data, status, headers, config) {
    //         if (data !== "false" && !isMaxError(data)) {
    //           save('indData'+id, data);
    //           deferred.resolve(data);
    //         } else {
    //           if (isMaxError(data)) {
    //             var returnCall = angular.copy(utils.httpObj);
    //             returnCall.method = 'GET';
    //             returnCall.saveName = 'indData'+id;
    //             returnCall.url = url;
    //             handleQueue(returnCall).then(function(result){
    //               deferred.resolve(result);
    //             });
    //           } else {
    //             deferred.resolve([]);
    //           }
    //         }
    //       });
    //     }
    //   } else {
    //     deferred.reject([]);
    //   }
    //   return deferred.promise;
    // };

    // service.getPictures = function(id){
    //   var deferred = $q.defer();
    //   if (id) {
    //     $http({
    //       method: 'GET',
    //       url: '/api/v1/individual/pictures/' + id,
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
    // }

    // service.updateIndData = function (data){
    //   var deferred = $q.defer();
    //   if (data) {
    //     $http({
    //       method: 'POST',
    //       url: '/api/v1/individual/',
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

    // service.getProfilePic = function (picId, override){
    //   var deferred = $q.defer();

    //   if (picId) {
    //     var url = '/api/v1/profilePic/'+ picId;
    //     var value = null;
    //     value = checkExpire('indProfilePic'+picId, minute * 1440);
    //     if (value && !override) {
    //       deferred.resolve(value);
    //     } else {
    //       $http({
    //         method: 'GET',
    //         url: url,
    //       }).success(function(data, status, headers, config) {
    //         if (data !== "false" && !isMaxError(data)) {
    //           save('indProfilePic'+picId, data);
    //           deferred.resolve(data);
    //         } else {
    //           if (isMaxError(data)) {
    //             var returnCall = angular.copy(utils.httpObj);
    //             returnCall.method = 'GET';
    //             returnCall.saveName = 'indProfilePic'+picId;
    //             returnCall.url = url;
    //             handleQueue(returnCall).then(function(result){
    //               deferred.resolve(result);
    //             });
    //             // deferred.resolve(false);
    //           } else {
    //             deferred.resolve([]);
    //           }
    //         }
    //       });
    //     }
    //   } else {
    //     deferred.resolve(false);
    //   }
    //   return deferred.promise;
    // };

    // service.getProfilePicByPersonId = function (personId){
    //   var deferred = $q.defer();
    //   if (personId) {
    //     $http({
    //       method: 'GET',
    //       url: '/api/v1/profilePic/person/'+ personId,
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
    // service.getSpouses = function (spouseId, individualId){
    //   var deferred = $q.defer();
    //   if (spouseId && individualId) {
    //     $http({
    //       method: 'GET',
    //       url: '/api/v1/spouses/' + spouseId + '/' + individualId,
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
    // service.getPlace = function (placeId){
    //   var deferred = $q.defer();
    //   if (placeId) {
    //     $http({
    //       method: 'GET',
    //       url: '/api/v1/place/' + placeId,
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

    // service.deleteInd = function(id) {
    //   var deferred = $q.defer();
    //   if (id) {
    //     $http({
    //       method: 'DELETE',
    //       url: '/api/v1/individual/'+id
    //     }).success(function(data, status, headers, config){
    //       deferred.resolve(data);
    //     }).error (function(data, status, headers, config){
    //       deferred.reject('There was an error on the server.')
    //     })
    //   }
    //   return deferred.promise;
    // }

    // service.setProfilePic = function(id, pic){
    //   var deferred = $q.defer();
    //   if (id && pic)
    //   {
    //     var url = '/api/v1/profilePic/' + id + '/' + pic;
    //     $http({
    //       method: 'POST',
    //       url: url,
    //     }).success(function(data, status, headers, config) {
    //       if (data !== "false" && !isMaxError(data)) {
    //         deferred.resolve(data);
    //       } else {
    //         deferred.reject(false);
    //       }
    //     });
    //   } else {
    //     deferred.reject(false);
    //   }
    //   return deferred.promise;
    // }

    // service.getChildren = function(id, spouseid, override) {
    //   var deferred = $q.defer();
    //   if (id && spouseid)
    //   {
    //     var url = '/api/v1/individual/children/' + id + '/' + spouseid;
    //     var value = checkExpire('childrenOf_'+ id + '_' + spouseid, minute * 2);
    //     if (!value) {
    //       value = checkExpire('childrenOf_'+ spouseid + '_' + id, minute * 2);
    //     }
    //     if (value && !override) {
    //       deferred.resolve(value);
    //     } else {
    //       $http({
    //         method: 'GET',
    //         url: url,
    //       }).success(function(data, status, headers, config) {
    //         if (data !== "false" && !isMaxError(data)) {
    //           save('childrenOf_'+ id + '_' + spouseid, data);
    //           deferred.resolve(data);
    //         } else {
    //           if (isMaxError(data)) {
    //             var returnCall = angular.copy(utils.httpObj);
    //             returnCall.method = 'GET';
    //             returnCall.saveName = 'childrenOf_'+ id + '_' + spouseid;
    //             returnCall.url = url;
    //             handleQueue(returnCall).then(function(result){
    //               deferred.resolve(result);
    //             });
    //           } else {
    //             deferred.resolve([]);
    //           }
    //         }
    //       });
    //     }
    //   } else {
    //     deferred.reject([]);
    //   }
    //   return deferred.promise;
    // }

    service.getFamilies = (letter, all) => {
      // console.log('letter', letter);
      const deferred = $q.defer();
      if (letter) {
        let url = configs.baseURL + '/api/v1/individual/families/' + letter;
        if (all) {
          url = url + '/true';
        }
        $http({
          method: 'GET',
          url,
        }).success((data/* , status, headers, config */) => {
          deferred.resolve(data);
        }).error((/* data, status, headers, config */) => {
          deferred.reject('There was an error on the server.');
        });
      }
      return deferred.promise;
    };

    // service.getFamily = function(id) {

    //   var deferred = $q.defer();
    //   if (id) {
    //     $http({
    //       method: 'GET',
    //       url: '/api/v1/individual/family/'+id
    //     }).success(function(data, status, headers, config){
    //       deferred.resolve(data);
    //     }).error (function(data, status, headers, config){
    //       deferred.reject('There was an error on the server.')
    //     })
    //   }
    //   return deferred.promise;
    // }

    service.getFirstNames = (family, all) => {
      const deferred = $q.defer();
      if (family) {
        let url = configs.baseURL + '/api/v1/individual/familyNames/' + family;
        if (all) {
          url = url + '/true';
        }
        $http({
          method: 'GET',
          url,
        }).success((data/* , status, headers, config */) => {
          deferred.resolve(data);
        }).error((/* data, status, headers, config */) => {
          deferred.reject('There was an error on the server.');
        });
      }
      return deferred.promise;
    };

    // service.getDocuments = function(id, override) {
    //   var deferred = $q.defer();
    //   if (id)
    //   {
    //     var url = '/api/v1/individual/documents/' + id;
    //     var value = checkExpire('documentsOf_'+ id, minute * 2);
    //     if (value && !override) {
    //       deferred.resolve(value);
    //     } else {
    //       $http({
    //         method: 'GET',
    //         url: url,
    //       }).success(function(data, status, headers, config) {
    //         if (data !== "false" && !isMaxError(data)) {
    //           save('documentsOf_' + id, data);
    //           deferred.resolve(data);
    //         } else {
    //           if (isMaxError(data)) {
    //             var returnCall = angular.copy(utils.httpObj);
    //             returnCall.method = 'GET';
    //             returnCall.saveName = 'documentsOf_' + id;
    //             returnCall.url = url;
    //             handleQueue(returnCall).then(function(result){
    //               deferred.resolve(result);
    //             });
    //           } else {
    //             deferred.resolve([]);
    //           }
    //         }
    //       });
    //     }
    //   } else {
    //     deferred.reject([]);
    //   }
    //   return deferred.promise;
    // }

    // service.getAllSubmissions = function() {
    //   var deferred = $q.defer();
    //   var url = '/api/v1/individual/allSubmissions';
    //   $http({
    //     method: 'GET',
    //     url: url,
    //   }).success(function(data, status, headers, config) {
    //     if (data !== "false" && !isMaxError(data)) {
    //       deferred.resolve(data);
    //     } else {
    //       if (isMaxError(data)) {
    //         var returnCall = angular.copy(utils.httpObj);
    //         returnCall.method = 'GET';
    //         returnCall.url = url;
    //         handleQueue(returnCall).then(function(result){
    //           deferred.resolve(result);
    //         });
    //       } else {
    //         deferred.resolve([]);
    //       }
    //     }
    //   });
    //   return deferred.promise;
    // }

    // service.getMySubmissions = function() {
    //   var deferred = $q.defer();
    //   var url = '/api/v1/individual/submissions';
    //   $http({
    //     method: 'GET',
    //     url: url,
    //   }).success(function(data, status, headers, config) {
    //     if (data !== "false" && !isMaxError(data)) {
    //       deferred.resolve(data);
    //     } else {
    //       if (isMaxError(data)) {
    //         var returnCall = angular.copy(utils.httpObj);
    //         returnCall.method = 'GET';
    //         returnCall.url = url;
    //         handleQueue(returnCall).then(function(result){
    //           deferred.resolve(result);
    //         });
    //       } else {
    //         deferred.resolve([]);
    //       }
    //     }
    //   });
    //   return deferred.promise;
    // }
    // service.activateSubmission = function(id) {
    //   var deferred = $q.defer();
    //   var url = '/api/v1/activateIndividual/'+id;
    //   if (id){
    //     $http({
    //       method: 'POST',
    //       url: url,
    //     }).success(function(data, status, headers, config) {
    //       if (data !== "false" && !isMaxError(data)) {
    //         deferred.resolve(data);
    //       } else {
    //         if (isMaxError(data)) {
    //           var returnCall = angular.copy(utils.httpObj);
    //           returnCall.method = 'GET';
    //           returnCall.url = url;
    //           handleQueue(returnCall).then(function(result){
    //             deferred.resolve(result);
    //           });
    //         } else {
    //           deferred.resolve([]);
    //         }
    //       }
    //     });
    //   } else {
    //     deferred.resolve([]);
    //   }
    //   return deferred.promise;
    // }
    // service.deactivateSubmission = function(id) {
    //   var deferred = $q.defer();
    //   var url = '/api/v1/deactivateIndividual/'+id;
    //   $http({
    //     method: 'POST',
    //     url: url,
    //   }).success(function(data, status, headers, config) {
    //     if (data !== "false" && !isMaxError(data)) {
    //       deferred.resolve(data);
    //     } else {
    //       if (isMaxError(data)) {
    //         var returnCall = angular.copy(utils.httpObj);
    //         returnCall.method = 'GET';
    //         returnCall.url = url;
    //         handleQueue(returnCall).then(function(result){
    //           deferred.resolve(result);
    //         });
    //       } else {
    //         deferred.resolve([]);
    //       }
    //     }
    //   });
    //   return deferred.promise;
    // }

    return service;
  }

  ngModule.factory('individual', individual);

  return ngModule;
};
