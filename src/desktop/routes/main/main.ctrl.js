/* ngInject */
function MainCtrl(configs) {
  const main = this;
  main.meaningOfLife = 42;

  main.list = [];

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
MainCtrl.$inject = ['configs'];

module.exports = MainCtrl;
