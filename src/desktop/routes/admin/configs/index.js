module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin.configs', [])
    .config(require('./configs.config.js'));

  return ngModule;
};
