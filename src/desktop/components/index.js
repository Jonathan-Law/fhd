module.exports = angular => {
  const ngModule = angular.module('da.desktop.components', [
    // inject route modules here
    require('./nav')(angular).name,
    require('./slidingThumbnail')(angular).name,
  ]);

  return ngModule;
};
