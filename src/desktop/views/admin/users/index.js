module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin.users', [])
    .config(require('./users.config.js'));

  return ngModule;
};
