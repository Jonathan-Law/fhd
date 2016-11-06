module.exports = ngModule => {
  /* @nginject */
  function navigation($state, $log) {
    const api = {
      logThis: () => {
        $log.log('TEST');
      }
    };
    return api;
  }

  ngModule.factory('navigation', navigation);

  return ngModule;
};
