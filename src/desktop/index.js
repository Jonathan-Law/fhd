import 'ng-redux';
import { attachAll, getNgModuleNames } from '../../other/boilerplate-utils.js';
const angular = require('angular');
require('angular-ui-bootstrap');
require('./desktop.css');
require('ngtouch');
const ngDependencies = [
  'ui.router',
  'ngAnimate',
  'ngTouch',
  'ngRedux',
  'ui.bootstrap',
  require('../common').name,
  require('./components/utilities.js')(angular).name,
  // Add additional external Angular dependencies here
];

const dependencies = getNgModuleNames(require.context('./routes', true, /index\.js$/)).filter(thing => thing);
ngDependencies.push.apply(ngDependencies, dependencies);

const ngModule = angular.module('da.desktop', ngDependencies)
.constant('$', require('jquery'))
.constant('d3', require('d3'))
.constant('_', require('lodash'));


attachAll(require.context('./components', true, /\.(component|directive)\.js$/))(ngModule);
attachAll(require.context('./containers', true, /\.(component|directive)\.js$/))(ngModule);

ngModule.config(require('./desktop.config.js'))
.run(require('./desktop.init.js'));
