module.exports = angular => {
  const ngModule = angular.module('da.desktop.components.site', [
    // inject route modules here
    require('./auth')(angular).name,
    require('./nav')(angular).name,
    require('./breadcrumbs')(angular).name,
    require('./slidingThumbnail')(angular).name,
  ]);

  return ngModule;
};
