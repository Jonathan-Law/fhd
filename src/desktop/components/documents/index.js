module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.documents', []);

  require('./documents.directive')(ngModule);
  return ngModule;
};
