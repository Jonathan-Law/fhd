module.exports = ngModule => {
  require('./contact-route.component.css');

  ngModule.component('contactRoute', {
    template: require('./contact-route.component.html'),
    controller: contactRouteCtrl,
    bindings: {
      // Inputs should use < and @ bindings.
      // Outputs should use & bindings.
    }
  });

  function contactRouteCtrl(Business) {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.handleCallback = handleCallback;
    ctrl.formSent = Date.now();

    function $onInit() {
      // Called on each controller after all the controllers have been constructed and had their bindings initialized
      // Use this for initialization code.
      Business.user.getIsValidated().then(isValidated => {
        ctrl.isValidated = isValidated;
      });
    }

    function handleCallback(form, model) {
      if (form.$valid) {
        Business.user.sendAdminMessage(model).then((result) => {
          if (result) {
            ctrl.formSent = Date.now();
          }
        });
      }
    }
  }

  // inject dependencies here
  contactRouteCtrl.$inject = ['business'];
};
