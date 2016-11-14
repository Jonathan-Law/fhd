import { attachAll } from '../../../../other/boilerplate-utils.js';

const ngModule = angular.module('da.desktop.contact', []);

attachAll(require.context('./components', true, /\.(component|directive)\.js$/))(ngModule);
attachAll(require.context('./containers', true, /\.(component|directive)\.js$/))(ngModule);

ngModule.config(contactConfig);


function contactConfig($stateProvider) {
  $stateProvider.state('contact', {
    url: '/contact',
    template: '<contact-route></contact-route>'
  });
}

contactConfig.$inject = ['$stateProvider'];


export default ngModule;
