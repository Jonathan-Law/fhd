/* ngInject */
function family($scope, individual, $state) {
  const fn = this;
  fn.name = ($state.params.constructor === Object && angular.equals($state.params, {}) || !$state.params.name) ? 'Law' : $state.params.name;

  $scope.$watch(angular.bind(fn, () => {
    return fn.name;
  }), (newval) => {
    // grab name info
    individual.getFirstNames(newval, true).then((result) => {
      $scope.$applyAsync(() => {
        fn.names = result;
      });
    }, () => {
      fn.names = [];
    });
  });

  fn.getNames = (index) => {
    if (index !== null && fn.names && fn.names.length > 0) {
      if (fn.names.length > 30) {
        let offset;
        let plussone;
        if (fn.names.length % 3 !== 0) {
          if (index === 0) {
            plussone = 1;
            offset = 0;
          } else if (index !== 2 && fn.names.length % 3 !== 1) {
            offset = 1;
            plussone = 1;
          } else {
            plussone = 0;
            offset = 1;
          }
        } else {
          offset = 0;
          plussone = 0;
        }
        return fn.names.slice(((fn.names.length / 3) * index) + offset, ((fn.names.length / 3) * (index + 1) + plussone));
      } else if (fn.names.length > 15) {
        let offset;
        let plussone;
        if (fn.names.length % 2 !== 0) {
          if (index === 0) {
            plussone = 1;
            offset = 0;
          } else {
            offset = 1;
            plussone = 0;
          }
        } else {
          offset = 0;
          plussone = 0;
        }
        return fn.names.slice(((fn.names.length / 2) * index) + offset, ((fn.names.length / 2) * (index + 1) + plussone));
      }
      return fn.names;
    } else if (fn.names) {
      if (fn.names.length > 30) {
        return new Array(3);
      } else if (fn.names.length > 15) {
        return new Array(2);
      }
      return new Array(1);
    }
  };
}

// inject dependencies here
// family.$inject = [];

module.exports = family;
