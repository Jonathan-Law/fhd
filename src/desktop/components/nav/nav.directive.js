const $ = require('jquery');

module.exports = ngModule => {
  ngModule.directive('nav', /* @ngInject */ (navigation, $timeout, $log, business, $state) => {
    function linkFn(scope) {
      // navigation.logThis();
      scope.getTypeahead = (val) => {
        return business.getTypeahead(val);
      };
      // trigger functions for slideout
      function closeMenu() {
        const menuItems = $('.side-wrapper-nav');
        const dropdowns = $('.sidebar-nav li .dropdown.open a[dropdowntoggle]');
        menuItems.each(function unDrop() {
          $(this).removeClass('active');
        });
        dropdowns.each(function dropDown() {
          const id = $(this).attr('dropdown-tog');
          $log.log(id);
        });
      }

      function openMenu() {
        const menuItems = $('.side-wrapper-nav');
        menuItems.each(function addClass() {
          $(this).addClass('active');
        });
      }

      scope.onSelect = (item/*, model, something*/) => {
        $state.go('individual', {
          'id': item.id,
          'tab': 'default',
        });
      };

      $timeout(() => {
        $('#sidebar-wrapper').on('mouseleave', () => {
          closeMenu();
        });
        $('#menu-toggle').on('click, mouseenter', () => {
          openMenu();
        });
        $(document).on('click', (e) => {
          const attr = $(e.target).attr('dropdowntoggle');
          if (($(e.target).attr('id') !== 'menu-toggle') && ($(e.target).attr('id') !== 'sideNavSearch') && ($(e.target).attr('id') !== 'sidebar-brand') && !(typeof attr !== typeof undefined && attr !== false)) {
            closeMenu();
          } else {
            e.stopPropagation();
          }
        });
      });
    }

    require('./nav.css');
    return {
      restrict: 'A',
      scope: {},
      template: require('./nav.html'),
      link: linkFn,
    };
  });
};
