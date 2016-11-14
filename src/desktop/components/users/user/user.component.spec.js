module.exports = ngModule => {
  const component = require('./user.component.js');
  component(ngModule);

  describe('component:user', () => {
    let $componentController;

    beforeEach(window.module(ngModule.name));

    beforeEach(inject(_$componentController_ => {
      $componentController = _$componentController_;
    }));

    function createController(bindings = {}) {
      const $ctrl = $componentController('user', { $scope: {} }, bindings);
      return $ctrl;
    }

    it('should instantiate', () => {
      const $ctrl = createController();
      expect($ctrl).to.not.equal(undefined);
    });

    // insert your tests here
  });
};
