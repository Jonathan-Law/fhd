module.exports = angular => {
  const ngModule = angular.module('da.desktop.views', [
    // inject route modules here
    require('./admin')(angular).name,
    require('./family')(angular).name,
    require('./individual')(angular).name,
    require('./main')(angular).name,
    require('./searchByFamily')(angular).name,
  ]);

  return ngModule;
};
