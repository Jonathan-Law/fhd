module.exports = ngModule => {
  /* @nginject */
  function placepickerFn($http) {
    const placepickerService = {};


    placepickerService.getLocation = (val) => {
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


    return placepickerService;
  }

  ngModule.factory('placepickerService', placepickerFn);

  return ngModule;
};
