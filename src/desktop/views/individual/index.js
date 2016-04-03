module.exports = angular => {
  const ngModule = angular
    .module('da.desktop.individual', [
      require('../../components/libs/angularPageslide.directive.js')(angular).name
    ])
    .config(require('./individual.config.js'));

  require('./individual.css');
  require('./photoAlbum.css');

  return ngModule;
};
