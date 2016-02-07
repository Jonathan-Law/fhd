function MainCtrl() {
  const main = this;
  main.meaningOfLife = 42;
  main.list = [{
    letter: 'a',
    base: 'http://www.planwallpaper.com/static/cache/18/ca/18ca8c4c0fe2f90c096ebfaa942965c2.jpg',
    overlay: 'http://www.planwallpaper.com/static/images/nasas-images-of-most-remarkable-events-you-cant-miss.jpg'
  }, {
    letter: 'a',
    base: 'http://www.planwallpaper.com/static/cache/18/ca/18ca8c4c0fe2f90c096ebfaa942965c2.jpg',
    overlay: 'http://www.planwallpaper.com/static/images/nasas-images-of-most-remarkable-events-you-cant-miss.jpg'
  }, {
    letter: 'a',
    base: 'http://www.planwallpaper.com/static/cache/18/ca/18ca8c4c0fe2f90c096ebfaa942965c2.jpg',
    overlay: 'http://www.planwallpaper.com/static/images/nasas-images-of-most-remarkable-events-you-cant-miss.jpg'
  }, {
    letter: 'a',
    base: 'http://www.planwallpaper.com/static/cache/18/ca/18ca8c4c0fe2f90c096ebfaa942965c2.jpg',
    overlay: 'http://www.planwallpaper.com/static/images/nasas-images-of-most-remarkable-events-you-cant-miss.jpg'
  }, {
    letter: 'a',
    base: 'http://www.planwallpaper.com/static/cache/18/ca/18ca8c4c0fe2f90c096ebfaa942965c2.jpg',
    overlay: 'http://www.planwallpaper.com/static/images/nasas-images-of-most-remarkable-events-you-cant-miss.jpg'
  }, {
    letter: 'a',
    base: 'http://www.planwallpaper.com/static/cache/18/ca/18ca8c4c0fe2f90c096ebfaa942965c2.jpg',
    overlay: 'http://www.planwallpaper.com/static/images/nasas-images-of-most-remarkable-events-you-cant-miss.jpg'
  }, {
    letter: 'a',
    base: 'http://www.planwallpaper.com/static/cache/18/ca/18ca8c4c0fe2f90c096ebfaa942965c2.jpg',
    overlay: 'http://www.planwallpaper.com/static/images/nasas-images-of-most-remarkable-events-you-cant-miss.jpg'
  }];
  main.clicked = function clicked() {
    console.log('stuff');
  };
}

// inject dependencies here
MainCtrl.$inject = [];

if (ON_TEST) {
  require('./Main.ctrl.spec.js')(MainCtrl);
}

module.exports = MainCtrl;
