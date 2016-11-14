module.exports = ngModule => {
  require('./profile-route.component.css');

  ngModule.component('profileRoute', {
    template: require('./profile-route.component.html'),
    controller: profileRouteCtrl,
    bindings: {
      // Inputs should use < and @ bindings.
      // Outputs should use & bindings.
    }
  });

  function profileRouteCtrl() {
    const ctrl = this;

    ctrl.$onInit = $onInit;

    function $onInit() {
      // Called on each controller after all the controllers have been constructed and had their bindings initialized
      // Use this for initialization code.
    }
  }

  // inject dependencies here
  profileRouteCtrl.$inject = [];
};
