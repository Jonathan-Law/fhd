const jQuery = require('jquery');// attach myLibrary as a property of window
const moment = require('moment');
const angular = require('angular');
/* eslint-disable */
var utils = {};

// BEGIN API

utils.httpObj = {
  method: '',
  url: '',
  data: {},
  params: {},
  saveName: ''
}

utils.formatDate = (date, format) => {
  return moment(date).format(format);
};

// converts a string into a Date object and then into a readable string.
utils.getDisplayDate = function(date, text) {
  if (date && !text) {
    var d = new Date(date);
    var currDate = d.getDate();
    var currMonth = d.getMonth();
    var currYear = d.getFullYear();
    return ((currMonth + 1) + '/' + currDate + '/' + currYear);
  } else if (date) {
    var d = new Date(date);
    var currDate = d.getDate();
    var currMonth = d.getMonth();
    var currYear = d.getFullYear();
    return (utils.MONTHS[currMonth] + ' ' + currDate + ', ' + currYear);
  }
  return null;
};

utils.date = () => angular.copy({
  day: 0,
  month: 0,
  yearh: 0,
  id: '',
  personId: '',
  yearB: false,
  date: false,
  set: function(rhs) {
    if (rhs) {
      if (rhs.birthPlace !== undefined) {
        this.place = rhs.birthPlace;
        this.birthplace = {};
      } else if (rhs.deathPlace !== undefined) {
        this.place = rhs.deathPlace;
        this.deathplace = {};
      } else if (rhs.burialPlace !== undefined) {
        this.place = rhs.burialPlace;
        this.burialplace = {};
      } else {
        this.place = null;
      }
      this.day = +rhs.day;
      this.month = +rhs.month;
      this.year = +rhs.year;
      this.yearB = rhs.yearB === '1' ? true : false;
      if (!isNaN(this.day) && !isNaN(this.month) && !isNaN(this.year) && this.year && this.month && this.day) {
        this.date = moment(new Date(this.year + '/' + this.month + '/' + this.day));
      } else  if (!isNaN(this.year)) {
        this.date = this.year;
      }
      this.personId = rhs.personId || this.personId;
    } else {
      return false;
    }
    return angular.copy(this);
  },
  toString: function(format) {
    if (typeof this.date === 'object') {
      if (format === 'input') {
        return this.date.format('MM/DD/YYYY');
      }
      return this.date.format('MMMM D, YYYY')
    } else if (!isNaN(this.year)) {
      if (this.yearB) {
        return this.year;
      } else {
        return 'About ' + this.year;
      }
    }
  },
  getPlace: function(format) {

    var result = '';
    if (this.place) {
      if (this.place.cemetary) {
        result += this.place.cemetary + ' (cemetary)';
      }
      if (this.place.town) {
        if (result.length) {
          result += ', '
        }
        result += this.place.town;
      }
      if (this.place.county) {
        if (result.length) {
          result += ', '
        }
        result += this.place.county;
      }
      if (this.place.state) {
        if (result.length) {
          result += ', '
        }
        result += this.place.state;
      }
      if (this.place.country) {
        if (result.length) {
          result += ', '
        }
        result += this.place.country;
      }
      if (!result.length) {
        return 'Unknown';
      }
      return result;
    } else {
      return 'Unknown';
    }
  }
});

// function to convert an object with parameters that translate to strings
utils.toParamString = function(obj) {
  var queryParams = "";
  for (var key in obj) {
    if (obj.hasOwnProperty(key)) {
      var val = obj[key];
      // if the value is a clean string and has a value, we know we want it.
      if (val !== null && (typeof val === 'string' || typeof val === 'number')) {
        if (!queryParams.length) {
          queryParams += key + '=' + encodeURIComponent(val);
        } else {
          queryParams += '&' + key + '=' + encodeURIComponent(val);
        }
      }
    }
  }
  return queryParams;
}

// END API
utils.RE = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

utils.EMAIL_REGEXP = /^[a-z0-9!#$%&'*+\/=?^_`{|}~.-]+@[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*$/i;

utils.MONTHS = new Array('January', 'February', 'March',
  'April', 'May', 'June', 'July', 'August', 'September',
  'October', 'November', 'December');

module.exports = utils;

;!(function($) {
  $.fn.classes = function(callback) {
    var classes = [];
    $.each(this, function(i, v) {
      var splitClassName = v.className.split(/\s+/);
      for (var j in splitClassName) {
        var className = splitClassName[j];
        if (-1 === classes.indexOf(className)) {
          classes.push(className);
        }
      }
    });
    if ('function' === typeof callback) {
      for (var i in classes) {
        callback(classes[i]);
      }
    }
    return classes;
  };
})(jQuery);
/* eslint-enable */
