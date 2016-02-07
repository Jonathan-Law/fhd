module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.nav', []);

  require('./nav.directive')(ngModule);
  return ngModule;
};
