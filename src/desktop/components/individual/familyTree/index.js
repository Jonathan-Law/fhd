module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.familyTree', []);

  require('./familyTree.directive')(ngModule);
  return ngModule;
};
