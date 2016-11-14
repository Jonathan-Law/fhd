module.exports = ngModule => {
  const component = require('./contact-route.component.js');
  component(ngModule);

  describe('component:contactRoute', () => {
    let $componentController;

    beforeEach(() => {
      window.module('ui.router');
      window.module(ngModule.name);
    });

    beforeEach(inject(_$componentController_ => {
      $componentController = _$componentController_;
    }));

    function createController(bindings = {}) {
      const $ctrl = $componentController('contactRoute', { $scope: {} }, bindings);
      return $ctrl;
    }

    it('should instantiate', () => {
      const $ctrl = createController();
      expect($ctrl).to.not.equal(undefined);
    });

    // insert your tests here
  });
};
