/* ngInject */
function MainCtrl(configs, $state) {
  const main = this;
  main.meaningOfLife = 42;

  main.list = [];

  main.goTo = (letter) => {
    $state.go('family', {
      letter,
    });
  };

  for (let i = 1; i < 27; i++) {
    const current = String.fromCharCode(i + 96);
    main.list.push({
      overlay: configs.baseURL + 'images/ind/' + current + '.' + current + '.jpg',
      base: configs.baseURL + 'images/ind/' + current + '.jpg',
      letter: current
    });
  }
}

// inject dependencies here
// MainCtrl.$inject = [];

if (ON_TEST) {
  require('./Main.ctrl.spec.js')(MainCtrl);
}

module.exports = MainCtrl;
