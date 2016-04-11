module.exports = angular => {
  const ngModule = angular.module('da.desktop.services', []);
  // make sure to include the configs first
  require('./configs.factory.js')(ngModule);

  require('./business.factory.js')(ngModule);
  require('./daEvents.factory.js')(ngModule);
  require('./file.factory.js')(ngModule);
  require('./individual.factory.js')(ngModule);
  require('./localCache.factory.js')(ngModule);
  require('./navigation.factory.js')(ngModule);
  require('./user.factory.js')(ngModule);

  return ngModule;
};
