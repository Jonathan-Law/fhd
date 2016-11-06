module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.myfamily', [])
    .config(require('./myfamily.config.js'));

  return ngModule;
};
