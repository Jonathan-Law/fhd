const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function tagpickerFn($http, configs, $q) {
    const tagpickerService = {};


    function setupTagVal(result) {
      let list = [];
      if (result && result.length > 0 && result[0].address_components) {
        _.each(result, (response) => {
          if (response && response.address_components) {
            const temp = {};
            temp.town = response.address_components[0] ? response.address_components[0].long_name : '';
            temp.county = response.address_components[1] ? response.address_components[1].long_name : '';
            temp.state = response.address_components[2] ? response.address_components[2].long_name : '';
            temp.country = response.address_components[3] ? response.address_components[3].long_name : '';
            temp.text = response.formatted_address;
            list.push(temp);
          }
        });
      } else if (result && result.length > 0 && !result[0].text) {
        _.each(result, (response) => {
          response.text = response.typeahead;
          list.push(response);
        });
      } else {
        list = result;
      }
      return list;
    }

    tagpickerService.getTypeahead = (val) => {
      return $http.get(configs.baseURL + 'api/v1/core/typeahead/', {
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

    tagpickerService.getTypeaheadFile = (val, switchTrigger) => {
      const deferred = $q.defer();
      let newval = val;
      if (newval) {
        let body;
        if (typeof newval === 'object' && switchTrigger === 'place') {
          body = {
            method: 'GET',
            params: {
              'place': JSON.stringify(newval)
            }
          };
          newval = 'object';
          body.url = configs.baseURL + '/api/v1/files/getTypeahead/' + newval + '/' + switchTrigger;
        } else {
          body = {
            method: 'GET',
            url: configs.baseURL + '/api/v1/files/getTypeahead/' + newval + '/' + switchTrigger,
          };
        }
        $http(body).success((data) => {
          if (data) {
            // console.log('data', data)
            for (let i = data.length - 1; i >= 0; i--) {
              if (data[i].title) {
                data[i].typeahead = data[i].title;
              }
            }
            deferred.resolve(data);
          }
        }).error(() => {
          deferred.reject('The typeahead failed');
        });
      }
      return deferred.promise;
    };

    tagpickerService.getTypeaheadPlace = (val) => {
      return $http.get('http://maps.googleapis.com/maps/api/geocode/json', {
        withCredentials: false,
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

    tagpickerService.getTypeaheadOther = (val) => {
      return $http.get(configs.baseURL + 'api/v1/core/tags/other', {
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

    tagpickerService.getTagTypeaheadFiles = (val, switchTrigger) => {
      const deferred = $q.defer();
      let newval = val;
      if (newval) {
        if (switchTrigger && switchTrigger === 'place') {
          tagpickerService.getTypeaheadPlace(newval).then((result) => {
            newval = setupTagVal(result);
            tagpickerService.getTypeaheadFile(newval, switchTrigger).then((inner) => {
              if (inner && inner.length > 0) {
                deferred.resolve(inner);
              } else {
                deferred.reject(false);
              }
            });
          });
        } else {
          tagpickerService.getTypeaheadFile(newval, switchTrigger).then((inner) => {
            if (inner && inner.length > 0) {
              deferred.resolve(inner);
            } else {
              deferred.reject(false);
            }
          });
        }
      } else {
        deferred.reject('Your target switch trigger doesn\'t exist');
      }
      return deferred.promise;
    };

    tagpickerService.getTagTypeahead = (switchTrigger, val) => {
      const deferred = $q.defer();
      let target = tagpickerService.getTypeahead;
      if (switchTrigger) {
        if (switchTrigger === 'place') {
          target = tagpickerService.getTypeaheadPlace;
        } else if (switchTrigger === 'other') {
          target = tagpickerService.getTypeaheadOther;
        }
        if (target) {
          target(val).then((result) => {
            if (result && result.length > 0) {
              deferred.resolve(setupTagVal(result));
            } else {
              deferred.reject(false);
            }
          });
        } else {
          deferred.reject('Your target switch trigger doesn\'t exist');
        }
      } else {
        deferred.reject('You need to provide the switch trigger');
      }
      return deferred.promise;
    };

    return tagpickerService;
  }

  ngModule.factory('tagpickerService', tagpickerFn);

  return ngModule;
};
