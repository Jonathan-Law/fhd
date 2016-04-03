module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.breadcrumbs', []);

  require('./breadcrumbs.directive')(ngModule);
  return ngModule;
};
