module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.editIndividual', [
      require('../../utilities/index.js')(angular).name
    ]);

  require('./editIndividual.directive')(ngModule);
  return ngModule;
};
