module.exports = ngModule => {
  require('./message-admin.component.css');

  ngModule.component('messageAdmin', {
    template: require('./message-admin.component.html'),
    controller: messageAdminCtrl,
    bindings: {
      callback: '&',
      formSent: '<',
    }
  });

  function messageAdminCtrl() {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.$onChanges = $onChanges;
    ctrl.reset = reset;
    ctrl.backup = ctrl.formSent;
    ctrl.doCallback = doCallback;

    function $onInit() {
      ctrl.message = {};
    }

    function $onChanges() {
      if (ctrl.formSent !== ctrl.backup) {
        ctrl.reset();
        ctrl.backup = ctrl.formSent;
      }
    }

    function reset() {
      ctrl.message = {};
    }

    function doCallback(fields) {
      ctrl.callback(fields);
    }
  }

  // inject dependencies here
  messageAdminCtrl.$inject = [];
};
