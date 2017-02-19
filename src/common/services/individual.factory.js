const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function individual(configs, $http, $q, localCache) { /*jshint unused: false*/
    const service = {};

    const minute = 60 * 1000;
    // const componentservice = {};

    /***************************************************************
    * This function is used to check the localCache for the existance of a result
    * object that hasn't yet expired
    * params: name -- The unique identifier for the entry in the local cache (usually a string)
    * params: expire -- The ammount of time in ms that it will take for the object to expire
    * returns: result -- The value of the object if it has not yet expired, and null for
    *                    result objects that are no longer valid
    ***************************************************************/
    function checkExpire(name, expire) {
      const result = localCache.get(name, 'object');
      let cacheTime = null;
      if (result) {
        cacheTime = localCache.get(name + '-time', 'date');
        const timeDiff = new Date() - cacheTime;
        if (timeDiff < expire) {
          return result;
        }
        return null;
      }
      return null;
    }

    /***************************************************************
    * We use this function in conjunction with the checkExpire function.
    * Use this function to save the value in the local cache (it will also save
    * an expire time that it can use later to check validity of an entry)
    * params: name -- The unique identifier for the entry in the local cache (usually a string)
    * params: value -- The value of the data that you will be storing
    ***************************************************************/
    function save(name, value) {
      localCache.save(name, value);
      localCache.save(name + '-time', new Date());
    }


    // function updateChache(name, value) {
    //   save(name, value);
    // }

    function isMaxError(result) {
      if (typeof result === 'object') {
        if (result && result.error) {
          return _.contains(result.error, 'SQLSTATE[42000] [1203]');
        } else if (result && result.type && result.message) {
          console.log('HTTP: ERROR', result);
        }
      }
      return false;
    }

    service.getIndData = (id, override) => {
      const deferred = $q.defer();
      if (id) {
        const url = configs.baseURL + 'api/v1/individuals/' + id;
        let value = null;
        value = checkExpire('indData' + id, minute * 2);
        if (value && !override) {
          deferred.resolve(value);
        } else {
          $http({
            method: 'GET',
            url,
          }).success((data) => {
            if (isMaxError(data)) {
              setTimeout(() => {
                service.getIndData(id, override).then((result) => {
                  deferred.resolve(result);
                }, () => {
                  deferred.reject();
                });
              }, 50);
            } else {
              save('indData' + id, data);
              deferred.resolve(data);
            }
          }).error((data) => {
            deferred.resolve(data || []);
          });
        }
      } else {
        deferred.reject([]);
      }
      return deferred.promise;
    };

    service.getPictures = (id) => {
      const deferred = $q.defer();
      if (id) {
        $http({
          method: 'GET',
          url: configs.baseURL + 'api/v1/individuals/pictures/' + id,
        }).success((data) => {
          if (isMaxError(data)) {
            setTimeout(() => {
              service.getPictures(id).then((result) => {
                deferred.resolve(result);
              }, () => {
                deferred.reject();
              });
            }, 50);
          } else {
            deferred.resolve(data);
          }
        }).error(() => {
          deferred.resolve([]);
        });
      } else {
        deferred.resolve(false);
      }
      return deferred.promise;
    };

    service.updateIndData = (data) => {
      const deferred = $q.defer();
      if (data) {
        $http({
          method: 'POST',
          url: configs.baseURL + 'api/v1/individuals/',
          data,
        }).success((result) => {
          deferred.resolve(result);
        }).error(() => {
          deferred.reject();
        });
      } else {
        deferred.reject(false);
      }
      return deferred.promise;
    };
    // service.getProfilePic = function (picId, override){
    //   var deferred = $q.defer();

    //   if (picId) {
    //     var url = '/api/v1/core/profilePic/'+ picId;
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
    //       url: '/api/v1/core/profilePic/person/'+ personId,
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

    service.getSpouses = (spouseId, individualId) => {
      const deferred = $q.defer();
      if (spouseId && individualId) {
        $http({
          method: 'GET',
          url: configs.baseURL + 'api/v1/core/spouses/' + spouseId + '/' + individualId,
        }).success((data) => {
          deferred.resolve(data);
        }).error(() => {
          deferred.reject();
        });
      } else {
        deferred.reject(false);
      }
      return deferred.promise;
    };


    service.getPlace = (placeId) => {
      const deferred = $q.defer();
      if (placeId) {
        $http({
          method: 'GET',
          url: configs.baseURL + 'api/v1/core/place/' + placeId,
        }).success((data) => {
          if (isMaxError(data)) {
            setTimeout(() => {
              service.getPlace(placeId).then((result) => {
                deferred.resolve(result);
              }, () => {
                deferred.reject();
              });
            }, 50);
          } else {
            deferred.resolve(data);
          }
        }).error(() => {
          deferred.reject();
        });
      } else {
        deferred.resolve(false);
      }
      return deferred.promise;
    };

    service.deleteInd = (id) => {
      const deferred = $q.defer();
      if (id) {
        $http({
          method: 'DELETE',
          url: configs.baseURL + '/api/v1/individuals/' + id
        }).success((data) => {
          deferred.resolve(data);
        }).error(() => {
          deferred.reject('There was an error on the server.');
        });
      }
      return deferred.promise;
    };

    // service.setProfilePic = function(id, pic){
    //   var deferred = $q.defer();
    //   if (id && pic)
    //   {
    //     var url = '/api/v1/core/profilePic/' + id + '/' + pic;
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

    service.getChildren = (id, spouseid, override) => {
      const deferred = $q.defer();
      if (id && spouseid) {
        const url = configs.baseURL + 'api/v1/individuals/children/' + id + '/' + spouseid;
        let value = checkExpire('childrenOf_' + id + '_' + spouseid, minute * 2);
        if (!value) {
          value = checkExpire('childrenOf_' + spouseid + '_' + id, minute * 2);
        }
        if (value && !override) {
          deferred.resolve(value);
        } else {
          $http({
            method: 'GET',
            url,
          }).success((data) => {
            if (isMaxError(data)) {
              setTimeout(() => {
                service.getChildren(id, spouseid, override).then((result) => {
                  save('childrenOf_' + id + '_' + spouseid, result);
                  deferred.resolve(result);
                }, () => {
                  deferred.reject();
                });
              }, 50);
            } else {
              save('childrenOf_' + id + '_' + spouseid, data);
              deferred.resolve(data);
            }
          }).error(() => {
            deferred.resolve([]);
          });
        }
      } else {
        deferred.reject([]);
      }
      return deferred.promise;
    };

    service.getFamilies = (letter, all) => {
      const deferred = $q.defer();
      if (letter) {
        let url = configs.baseURL + 'api/v1/individuals/families/' + letter;
        if (all) {
          url = url + '/true';
        }
        $http({
          method: 'GET',
          url,
        }).success((data/* , status, headers, config */) => {
          if (isMaxError(data)) {
            setTimeout(() => {
              service.getFamilies(letter, all).then((result) => {
                deferred.resolve(result);
              }, () => {
                deferred.reject();
              });
            }, 50);
          } else {
            deferred.resolve(data);
          }
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
    //       url: '/api/v1/individuals/family/'+id
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
        let url = configs.baseURL + 'api/v1/individuals/familyNames/' + family;
        if (all) {
          url = url + '/true';
        }
        $http({
          method: 'GET',
          url,
        }).success((data/* , status, headers, config */) => {
          if (isMaxError(data)) {
            setTimeout(() => {
              service.getFirstNames(family, all).then((result) => {
                deferred.resolve(result);
              }, () => {
                deferred.reject();
              });
            }, 50);
          } else {
            deferred.resolve(data);
          }
        }).error((/* data, status, headers, config */) => {
          deferred.reject('There was an error on the server.');
        });
      }
      return deferred.promise;
    };

    service.getDocuments = (id, override) => {
      const deferred = $q.defer();
      if (id) {
        const url = configs.baseURL + 'api/v1/individuals/documents/' + id;
        const value = checkExpire('documentsOf_' + id, minute * 2);
        if (value && !override) {
          deferred.resolve(value);
        } else {
          $http({
            method: 'GET',
            url,
          }).success((data/*, status, headers, config*/) => {
            if (isMaxError(data)) {
              setTimeout(() => {
                service.getDocuments(id, override).then((result) => {
                  save('documentsOf_' + id, result);
                  deferred.resolve(result);
                }, () => {
                  deferred.reject();
                });
              }, 50);
            } else {
              save('documentsOf_' + id, data);
              deferred.resolve(data);
            }
          });
        }
      } else {
        deferred.reject([]);
      }
      return deferred.promise;
    };

    service.getAllSubmissions = () => {
      const deferred = $q.defer();
      const url = configs.baseURL + '/api/v1/individuals/allSubmissions';
      $http({
        method: 'GET',
        url,
      }).success((data) => {
        if (isMaxError(data)) {
          setTimeout(() => {
            service.getAllSubmissions().then((result) => {
              deferred.resolve(result);
            }, () => {
              deferred.reject();
            });
          }, 50);
        } else {
          deferred.resolve(data);
        }
      }).error(() => {
        deferred.reject();
      });
      return deferred.promise;
    };

    service.getMySubmissions = () => {
      const deferred = $q.defer();
      const url = configs.baseURL + '/api/v1/individuals/submissions';
      $http({
        method: 'GET',
        url,
      }).success((data) => {
        if (isMaxError(data)) {
          setTimeout(() => {
            service.getMySubmissions().then((result) => {
              deferred.resolve(result);
            }, () => {
              deferred.reject();
            });
          }, 50);
        } else {
          deferred.resolve(data);
        }
      }).error(() => {
        deferred.reject();
      });
      return deferred.promise;
    };

    service.activateSubmission = (id) => {
      const deferred = $q.defer();
      const url = configs.baseURL + '/api/v1/individuals/activateIndividual/' + id;
      if (id) {
        $http({
          method: 'POST',
          url,
        }).success((data) => {
          deferred.resolve(data);
        }).error(() => {
          deferred.reject();
        });
      } else {
        deferred.reject();
      }
      return deferred.promise;
    };

    service.deactivateSubmission = (id) => {
      const deferred = $q.defer();
      const url = configs.baseURL + '/api/v1/individuals/deactivateIndividual/' + id;
      $http({
        method: 'POST',
        url,
      }).success((data) => {
        deferred.resolve(data);
      }).error(() => {
        deferred.reject();
      });
      return deferred.promise;
    };

    return service;
  }

  ngModule.factory('individual', individual);

  return ngModule;
};
