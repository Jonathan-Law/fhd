module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.admin', [
      require('./add')(angular).name,
      require('./edit')(angular).name,
    ])
    .config(require('./admin.config.js'));

  return ngModule;
};
