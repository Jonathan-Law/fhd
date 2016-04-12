require('ng-tags-input');
module.exports = angular => {
  const ngModule = angular.module('da.desktop.components.utilities.tagpicker', [
    // inject route modules here
    'ngTagsInput'
  ]);
  // require content
  require('./tagpicker.service.js')(ngModule);
  require('./tagpicker.directive.js')(ngModule);
  return ngModule;
};
