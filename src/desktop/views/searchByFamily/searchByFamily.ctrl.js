/* ngInject */
function searchByFamily($scope, individual, $state) {
  const sbf = this;
  sbf.meaningOfLife = 42;
  sbf.letter = ($state.params.constructor === Object && angular.equals($state.params, {}) || !$state.params.letter) ? 'a' : $state.params.letter;

  $scope.$watch(angular.bind(sbf, () => {
    return sbf.letter;
  }), (newval) => {
    individual.getFamilies(newval, true).then((result) => {
      $scope.$applyAsync(() => {
        // console.log('result', result);
        sbf.names = result;
      });
    }, () => {
      sbf.names = [];
    });
  });

  sbf.getNames = (index) => {
    if (index !== null && sbf.names && sbf.names.length > 0) {
      if (sbf.names.length > 30) {
        let offset;
        let plussone;
        if (sbf.names.length % 3 !== 0) {
          if (index === 0) {
            plussone = 1;
            offset = 0;
          } else if (index !== 2) {
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
        return sbf.names.slice(((sbf.names.length / 3) * index) + offset, ((sbf.names.length / 3) * (index + 1) + plussone));
      } else if (sbf.names.length > 15) {
        let offset;
        let plussone;
        if (sbf.names.length % 2 !== 0) {
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
        return sbf.names.slice(((sbf.names.length / 2) * index) + offset, ((sbf.names.length / 2) * (index + 1) + plussone));
      }
      return sbf.names;
    } else if (sbf.names) {
      if (sbf.names.length > 30) {
        return new Array(3);
      } else if (sbf.names.length > 15) {
        return new Array(2);
      }
      return new Array(1);
    }
  };
}

// inject dependencies here
// searchByFamily.$inject = [];

module.exports = searchByFamily;
