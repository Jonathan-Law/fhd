module.exports = ngModule => {
  const component = require('./dropzone.component.js');
  component(ngModule);

  describe('component:dropzone', () => {
    let $componentController;

    beforeEach(window.module(ngModule.name));

    beforeEach(inject(_$componentController_ => {
      $componentController = _$componentController_;
    }));

    function createController(bindings = {}) {
      const $ctrl = $componentController('dropzone', { $scope: {} }, bindings);
      return $ctrl;
    }

    it('should instantiate', () => {
      const $ctrl = createController();
      expect($ctrl).to.not.equal(undefined);
    });

    // insert your tests here
  });
};
