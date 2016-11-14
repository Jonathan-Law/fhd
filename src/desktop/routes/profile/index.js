import { attachAll } from '../../../../other/boilerplate-utils.js';

const ngModule = angular.module('da.desktop.profile', []);

attachAll(require.context('./components', true, /\.(component|directive)\.js$/))(ngModule);
attachAll(require.context('./containers', true, /\.(component|directive)\.js$/))(ngModule);

ngModule.config(profileConfig);


function profileConfig($stateProvider) {
  $stateProvider.state('profile', {
    url: '/profile',
    template: '<profile-route></profile-route>'
  });
}

profileConfig.$inject = ['$stateProvider'];


export default ngModule;
