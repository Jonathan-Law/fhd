module.exports = angular => {
  const ngModule = angular.module('da.desktop.components', [
    // inject route modules here
    require('./auth')(angular).name,
    require('./nav')(angular).name,
    require('./breadcrumbs')(angular).name,
    require('./documents')(angular).name,
    require('./familyTree')(angular).name,
    require('./photoAlbum')(angular).name,
    require('./slidingThumbnail')(angular).name,
  ]);

  return ngModule;
};
