// const moment = require('moment');
module.exports = ngModule => {
  /* @nginject */
  function userService(configs, $http, $q) { /*jshint unused: false*/
    const userStateWatches = [];

    const user = {};

    user.subscribeToUserState = (callback) => {
      userStateWatches.push(callback);
    };

    function handleWatches(state) {
      userStateWatches.forEach((watch) => {
        watch(state);
      });
    }

    // variables to cache data
    const results = {};
    const inProgress = {};

    // functions to debounce data calls
    // function checkComplete(key) {
    //   if (results[key] !== undefined) {
    //     return results[key];
    //   }
    //   return null;
    // }

    function addToInProgress(key, promise) {
      if (inProgress[key] && inProgress[key].constructor === Array) {
        inProgress[key].push(promise);
      } else {
        inProgress[key] = [promise];
      }
    }

    function resolveKey(key, result) {
      results[key] = result;
      while (inProgress[key].length) {
        inProgress[key].pop().resolve(result);
      }
    }

    function rejectKey(key, result) {
      results[key] = result;
      while (inProgress[key].length) {
        inProgress[key].pop().reject(result);
      }
    }

    user.getUserInfoId = (id) => {
      const deferred = $q.defer();
      $http({
        method: 'GET',
        url: configs.baseURL + '/api/v1/user/getUserInfo/' + id
      }).success((data) => {
        if (data) {
          deferred.resolve(data);
        } else {
          deferred.reject('Undefined user');
        }
      }).error(() => {
        deferred.resolve(false);
      });
      return deferred.promise;
    };

    user.getUsers = () => {
      return $http({
        method: 'GET',
        url: `${configs.baseURL}api/v1/user/getUserInfo`,
      }).then(data => data.data);
    };


    user.isLoggedIn = (/*override*/) => {
      // create a promise for if we don't have the data
      const promise = new Promise((resolve, reject) => {
        addToInProgress('isLoggedIn', {
          resolve, reject
        });
      });

      // and return it if we're already pulling for it
      if (inProgress.isLoggedIn.length > 1) {
        return promise;
      }

      return $http.get(configs.baseURL + 'api/v1/user/isLoggedIn')
        .success((data) => {
          if (data.data) {
            handleWatches(data.data);
            resolveKey('isLoggedIn', data.data);
            return promise;
          }
          handleWatches(data);
          resolveKey('isLoggedIn', data);
          return promise;
        }).error(() => {
          rejectKey('isLoggedIn', null);
          handleWatches(null);
          return promise;
        });
    };

    user.sendAdminMessage = (message) => {
      return $http({
        method: 'POST',
        url: `${configs.baseURL}api/v1/user/sendAdminMessage`,
        data: message,
      }).then(data => data.data);
    };

    user.getIsAdmin = (userInstance) => {
      if (userInstance && userInstance.rights && (userInstance.rights === 'super' || userInstance.rights === 'admin')) {
        return new Promise((resolve) => {
          resolve(true);
        });
      }
      return user.isLoggedIn().then((isLoggedIn) => {
        const result = isLoggedIn && isLoggedIn.data ? isLoggedIn.data : isLoggedIn;
        if (result && result.rights) {
          if (result.rights === 'super' || result.rights === 'admin') {
            return true;
          }
        }
        return false;
      }, () => {
        return false;
      });
    };

    user.getIsValidated = (userInstance) => {
      if (userInstance && userInstance.rights) {
        const promise = new Promise((resolve) => {
          switch (userInstance.rights) {
            case 'super':
            case 'admin':
            case 'high':
            case 'medium':
              resolve(true);
              break;
            default:
              resolve(false);
              break;
          }
        });
        return promise;
      }

      return user.isLoggedIn().then((isLoggedIn) => {
        const result = isLoggedIn && isLoggedIn.data ? isLoggedIn.data : isLoggedIn;
        if (result && result.rights) {
          switch (result.rights) {
            case 'super':
            case 'admin':
            case 'high':
            case 'medium':
              return true;
            default:
              return false;
          }
        }
        return false;
      }, () => {
        return false;
      });
    };

    setTimeout(() => {
      user.getIsValidated();
    }, 1000);

    user.resetPassword = (username) => {
      return $http({
        method: 'POST',
        url: configs.baseURL + 'api/v1/user/resetPassword',
        data: {
          username,
        },
      }).then(data => data.data);
    };

    user.login = (username, password) => {
      const deferred = $q.defer();
      if (!username || !password) {
        deferred.resolve(false);
        console.error('You must include a username and password to login');
      } else {
        $http({
          method: 'POST',
          url: configs.baseURL + 'api/v1/user/login',
          data: {
            username,
            password,
          }
        }).success((data) => {
          handleWatches(data);
          deferred.resolve(data);
        }).error((data) => {
          handleWatches(data);
          deferred.reject(data);
        });
      }
      return deferred.promise;
    };

    user.register = (username, password, email, first, last, gender) => {
      const deferred = $q.defer();
      if (!username || !password) {
        deferred.resolve(false);
        console.error('You must include a username and password to register');
      } else {
        $http({
          method: 'POST',
          url: configs.baseURL + '/api/v1/user/register',
          data: {
            username: username ? username : null,
            password: password ? password : null,
            email: email ? email : null,
            first: first ? first : null,
            last: last ? last : null,
            gender: gender ? gender : null
          }
        }).success((data) => {
          if (data !== 'false') {
            handleWatches(data);
            deferred.resolve(data);
          } else {
            handleWatches(null);
            deferred.resolve(false);
          }
        }).error(() => {
          handleWatches(null);
          deferred.reject(false);
        });
      }
      return deferred.promise;
    };

    user.logout = () => {
      return $http({
        method: 'POST',
        url: configs.baseURL + '/api/v1/user/logout',
      }).success(() => {
        return user.isLoggedIn();
      });
    };

    return user;
  }

  ngModule.factory('user', userService);

  return ngModule;
};
