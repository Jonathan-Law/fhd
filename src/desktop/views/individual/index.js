module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.individual', [])
    .config(require('./individual.config.js'));

  return ngModule;
};
