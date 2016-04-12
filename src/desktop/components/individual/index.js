module.exports = angular => {
  const ngModule = angular.module('da.desktop.components.individual', [
    // inject route modules here
    require('./documents')(angular).name,
    require('./editIndividual')(angular).name,
    require('./spouse')(angular).name,
    require('./familyTree')(angular).name,
    require('./photoAlbum')(angular).name,
  ]);

  return ngModule;
};
