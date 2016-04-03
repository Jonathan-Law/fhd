const utils = require('../libs/utils');
module.exports = ngModule => {
  ngModule.directive('documents', /* @ngInject */ (business, configs) => {
    require('./documents.css');

    function linkFn(scope) {
      const Business = business;
      scope.configs = configs;
      scope.$broadcast('$LOAD', 'documentsLoader');

      scope.getDate = (date) => {
        return utils.formatDate(date, 'D MMMM YYYY');
      };

      Business.individual.getDocuments(scope.id).then((result) => {
        if (result) {
          scope.$broadcast('$UNLOAD', 'documentsLoader');
          scope.documents = result;
        } else {
          scope.documents = [];
          scope.$broadcast('$UNLOAD', 'documentsLoader');
        }
      }, () => {
        scope.documents = [];
        scope.$broadcast('$UNLOAD', 'documentsLoader');
      });
    }
    return {
      template: require('./documents.template.html'),
      restrict: 'A',
      scope: {
        id: '='
      },
      link: linkFn,
    };
  });
};
