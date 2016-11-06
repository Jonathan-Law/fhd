const ngModule = angular
  .module('da.desktop.individual', [])
  .config(require('./individual.config.js'));
require('../../components/libs/angularPageslide.directive.js')(ngModule);
require('./individual.css');
require('./photoAlbum.css');

export default ngModule;
