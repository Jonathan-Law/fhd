module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.spouse', [
      require('../../utilities/index.js')(angular).name
    ]);

  require('./spouse.directive')(ngModule);
  return ngModule;
};
