module.exports = angular => {
  const ngModule = angular.module('da.desktop.components.utilities.datepicker', [
    // inject route modules here
  ]);
  require('./datepicker.service.js')(ngModule);
  require('./datepicker.directive.js')(ngModule);
  // require content
  return ngModule;
};
