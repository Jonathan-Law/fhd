module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin.edit', [])
    .config(require('./edit.config.js'));

  return ngModule;
};
