module.exports = ngModule => {
  require('./user.component.css');

  ngModule.component('user', {
    template: require('./user.component.html'),
    controller: userCtrl,
    bindings: {
      userInfo: '<',
      actionCallback: '&',
    }
  });

  function userCtrl() {
    const ctrl = this;

    ctrl.$onInit = $onInit;

    function $onInit() {
    }
  }

  // inject dependencies here
  userCtrl.$inject = [];
};
