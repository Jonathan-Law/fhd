module.exports = ngModule => {
  /* @nginject */
  function datepickerFn(/*$http, configs, individual, user*/) {
    const datepickerFactory = {};
    return datepickerFactory;
  }

  ngModule.factory('datepickerFactory', datepickerFn);

  return ngModule;
};
