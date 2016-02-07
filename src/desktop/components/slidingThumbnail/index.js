module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.slidingThumbnail', []);

  require('./slidingThumbnail.directive')(ngModule);
  return ngModule;
};
