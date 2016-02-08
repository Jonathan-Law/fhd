module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.family', [])
    .config(require('./family.config.js'));

  return ngModule;
};
