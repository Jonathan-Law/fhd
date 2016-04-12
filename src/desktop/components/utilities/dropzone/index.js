require('angular-dropzone');
module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.dropzone', [
      'ngDropzone'
    ]);

  require('./dropzone.directive')(ngModule);
  return ngModule;
};
