module.exports = angular => {
  const ngModule = angular.module('da.desktop.views', [
    // inject route modules here
    require('./main')(angular).name,
    require('./searchByFamily')(angular).name,
  ]);

  return ngModule;
};
