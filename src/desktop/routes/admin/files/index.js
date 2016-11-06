module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin.files', [])
    .config(require('./files.config.js'));

  return ngModule;
};
