module.exports = angular => {
  const ngModule = angular.module('da.desktop.components', [
    // inject route modules here
    require('./individual')(angular).name,
    require('./site')(angular).name,
    require('./utilities/index.js')(angular).name,
  ]);

  return ngModule;
};
