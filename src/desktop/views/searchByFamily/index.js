module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.searchByFamily', [])
    .config(require('./searchByFamily.config.js'));

  return ngModule;
};
