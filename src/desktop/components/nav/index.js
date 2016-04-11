module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.nav', [
      require('../auth')(angular).name,
    ]);
  require('./nav.directive')(ngModule);
  return ngModule;
};
