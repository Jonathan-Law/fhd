module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin', [
      require('./files')(angular).name,
      require('./individuals')(angular).name,
      require('./users')(angular).name,
      require('./configs')(angular).name,
    ])
    .config(require('./admin.config.js'));

  return ngModule;
};
