module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin.add', [])
    .config(require('./add.config.js'));

  return ngModule;
};
