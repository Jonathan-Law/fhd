module.exports = angular => {
  const ngModule = angular.module('da.desktop.components.utilities', [
    // inject route modules here
    require('./dropzone')(angular).name,
    require('./datepicker')(angular).name,
    require('./placepicker')(angular).name,
    require('./tagpicker')(angular).name,
  ]);

  return ngModule;
};
