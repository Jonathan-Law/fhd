module.exports = angular => {
  const ngModule = angular.module('da.desktop.components.utilities.placepicker', [
    // inject route modules here
  ]);
  require('./placepicker.service.js')(ngModule);
  require('./placepicker.directive.js')(ngModule);
  // require content
  return ngModule;
};
