module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin.individuals', [])
    .config(require('./individuals.config.js'));

  return ngModule;
};
