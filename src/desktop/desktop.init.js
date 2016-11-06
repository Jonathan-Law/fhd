module.exports = init;

function init($rootScope, business) {
  $rootScope.getTypeahead = (val) => {
    return business.getTypeahead(val);
  };
	// Add any app initialization code here.
}

init.$inject = ['$rootScope', 'business'];
