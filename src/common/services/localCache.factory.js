// const _ = require('lodash');
module.exports = ngModule => {
  /* @nginject */
  function localCache() {
    //Cross compatibility is provided by the sessionpolyfill.js file.
    const cache = window.sessionStorage;
    const undefinedStr = 'undefined';
    /**
     * Stores the value into the cache.  Will convert objects to strings.
     * params: key -- a string value
     * params: value -- string or object.  If object, it is converted to string.
     */
    const save = (key, val) => {
      let value = val;
      if (typeof key !== 'string') {
        throw new Error('Key must be a string');
      }
      if (typeof value === 'object') {
        if (value instanceof Date) {
          value = value.toString();
        } else {
          value = JSON.stringify(value);
        }
      }
      cache.setItem(key, value);
    };

    /**
     * Retrieves a value from the cache.
     * params: key -- string value
     * params: type -- optional parameter.  If it equals "object" a conversion to
     *         object will occur. "date" will convert it to a JS Date object.
     */
    const get = (key, type) => {
      if (typeof key !== 'string') {
        throw new Error('Key must be a string');
      }
      if ((typeof type !== 'undefined') && (type === 'object')) {
        const result = cache.getItem(key);
        if (result !== undefinedStr) {
          return JSON.parse(cache.getItem(key));
        }
        return undefined;
      }
      if ((typeof type !== 'undefined') && (type === 'date')) {
        return new Date(cache.getItem(key));
      }
      return cache.getItem(key);
    };

    /**
     * Clears out all key/value pairs currently stored.
     * TODO:: Make this a bit less destructive, so that it only wipes I-Learn
     *       specific items.
     */
    const clearAll = () => {
      cache.clear();
    };

    /**
     * Wipes out one element from the cache.
     * params: key -- The specified element.
     */
    const clear = (key) => {
      if (typeof key !== 'string') {
        throw new Error('Key must be a string');
      } else {
        cache.removeItem(key);
      }
    };

    return {
      save,
      get,
      clearAll,
      clear,
    };
  }

  ngModule.factory('localCache', localCache);

  return ngModule;
};
