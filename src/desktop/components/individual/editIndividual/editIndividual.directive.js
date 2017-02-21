const _ = require('lodash');
const moment = require('moment');
module.exports = ngModule => {
  ngModule.directive('editIndividual', /* @ngInject */ (business, configs, $timeout) => {
    require('./editIndividual.css');

    function linkFn(scope) {
      scope.getTypeahead = business.getTypeahead;
      scope.configs = configs;
      scope.personId = scope.personId || null;
      scope.$watch('personId', (newval) => {
        setupEdit(newval);
      });
      scope.setProfilePic = setProfilePic;
      scope.imgHeight = 40;

      business.user.getIsAdmin().then((result) => {
        scope.isAdmin = result;
      });

      scope.onSelectParent = () => {
        if (typeof scope.parents === 'object' && scope.parents) {
          business.individual.getIndData(scope.parents.id).then((result) => {
            scope.result.parentList.push(result);
            scope.parents = '';
          });
        }
      };
      scope.onSelectSpouse = () => {
        if (typeof scope.spouse === 'object' && scope.spouse) {
          business.individual.getIndData(scope.spouse.id).then((result) => {
            scope.result.spouseList.push(result);
            scope.spouse = '';
          });
        }
      };


      scope.removeParent = (id) => {
        const check = _.find(scope.result.parentList, {
          id
        });
        const index = _.indexOf(scope.result.parentList, check);
        scope.result.parentList.splice(index, 1);
      };

      scope.removeSpouse = (id) => {
        const check = _.find(scope.result.spouseList, {
          id
        });
        const index = _.indexOf(scope.result.spouseList, check);
        scope.result.spouseList.splice(index, 1);
      };


      function clearResult() {
        if (!scope.result || !angular.isObject(scope.result)) {
          scope.result = {};
          return;
        }
        scope.result.firstName = null;
        scope.result.middleName = null;
        scope.result.lastName = null;
        scope.result.birth = {};
        scope.result.birthDate = null;
        scope.result.birth.yearB = null;
        scope.result.birth.birthPlace = null;
        scope.result.death = {};
        scope.result.deathDate = null;
        scope.result.death.yearD = null;
        scope.result.death.deathPlace = null;
        scope.result.burial = {};
        scope.result.burialDate = null;
        scope.result.burial.yearB = null;
        scope.result.burial.burialPlace = null;
        scope.result.sex = null;
        scope.result.sex = null;
        scope.result.relationship = null;
        scope.result.parentList = [];
        scope.result.spouseList = [];
        scope.result.id = null;
        scope.result.profile_pic = null;
        scope.result.profilePicture = null;
      }

      function setProfilePic(pic) {
        scope.result.profile_pic = pic.id;
        scope.result.profilePicture = pic;
      }

      function setupEdit(id) {
        business.individual.getPictures(scope.personId).then((results) => {
          scope.images = results;
        });
        if (id) {
          clearResult();
          business.individual.getIndData(id, true).then((result) => {
            scope.result = angular.copy(result);
            scope.backup = angular.copy(result);
            if (scope.result && scope.result.birth && +scope.result.birth.yearB) {
              scope.result.birthDate = moment(result.birth.year + '-' + result.birth.month + '-' + result.birth.day, 'YYYY-MM-DD').toDate();
            } else if (result.birth.year) {
              scope.result.birthDate = moment(result.birth.year + '-01-01', 'YYYY-MM-DD').toDate();
            }

            if (scope.result && scope.result.death && +scope.result.death.yearD) {
              scope.result.deathDate = moment(result.death.year + '-' + result.death.month + '-' + result.death.day, 'YYYY-MM-DD').toDate();
            } else if (result.death.year) {
              scope.result.deathDate = moment(result.death.year + '-01-01', 'YYYY-MM-DD').toDate();
            }

            if (scope.result && scope.result.burial && +scope.result.burial.yearB) {
              scope.result.burialDate = moment(result.burial.year + '-' + result.burial.month + '-' + result.burial.day, 'YYYY-MM-DD').toDate();
            } else if (result.burial.year) {
              scope.result.burialDate = moment(result.burial.year + '-01-01', 'YYYY-MM-DD').toDate();
            }

            scope.result.parentList = [];
            if (scope.result && scope.result.parents) {
              scope.result.parents.forEach((parent) => {
                business.individual.getIndData(parent.parentId).then((parentResult) => {
                  if (parentResult) {
                    scope.result.parentList.push(parentResult);
                  }
                });
              });
            }

            scope.result.spouseList = [];
            if (scope.result && scope.result.spouse) {
              scope.result.spouse.forEach((spouse) => {
                business.individual.getIndData(spouse.personId).then((spouseResult) => {
                  if (spouseResult) {
                    scope.result.spouseList.push(spouseResult);
                  }
                });
              });
            }
          });
        } else {
          scope.result = {
            parentList: [],
            spouseList: [],
          };
          scope.backup = {};
        }
      }


      function getDateObj(date, confidence) {
        const dt = moment(date);
        const temp = {
          day: confidence ? dt.date() : 0,
          month: confidence ? dt.month() + 1 : 0,
          year: dt.year(),
        };
        return temp;
      }

      function convertInfo(obj) {
        const temp = angular.copy(obj);

        const birthDateObj = getDateObj(temp.birthDate, temp.birth.yearB);
        temp.birthPlace = temp.birth.birthPlace;
        temp.birth = _.merge({}, {
          yearB: temp.birth && temp.birth.yearB ? true : false,
          id: temp.birth && temp.birth.id ? temp.birth.id : null,
        }, birthDateObj);
        delete temp.birthDate;

        const deathDateObj = getDateObj(temp.deathDate, temp.death.yearD);
        temp.deathPlace = temp.death.deathPlace;
        temp.death = _.merge({}, {
          yearD: temp.death && temp.death.yearD ? true : false,
          id: temp.death && temp.death.id ? temp.death.id : null,
        }, deathDateObj);
        delete temp.deathDate;

        if (temp.burialDate && temp.burial.yearB) {
          const burialDateObj = getDateObj(temp.burialDate, temp.burial.yearB);
          temp.burialPlace = temp.burial.burialPlace;
          temp.burial = _.merge({}, {
            yearB: temp.burial && temp.burial.yearB ? true : false,
            id: temp.burial && temp.burial.id ? temp.burial.id : null,
          }, burialDateObj);
        } else {
          temp.burialPlace = null;
          temp.burial = null;
        }
        delete temp.burialDate;

        temp.parents = temp.parentList;
        delete temp.parentList;

        temp.spouse = temp.spouseList;
        delete temp.spouseList;

        temp.spouse.forEach((spouse) => {
          spouse.marriageDate = getDateObj(spouse.marriageDate, spouse.exactMarriageDate);
          spouse.marriageDate.yearM = spouse.exactMarriageDate;
        });

        temp.person = {
          yearB: temp.birth.yearB,
          yearD: temp.death.yearD,
          yearBorn: temp.birth.year,
          yearDead: temp.death.year,
          firstName: temp.firstName,
          middleName: temp.middleName || '',
          lastName: temp.lastName,
          sex: temp.sex || '',
          relationship: temp.relationship || '',
        };

        if (temp.id) {
          temp.person.id = temp.id || '';
          delete temp.id;
        }

        delete temp.firstName;
        delete temp.middleName;
        delete temp.lastName;
        delete temp.sex;
        delete temp.relationship;

        return temp;
      }

      scope.activateSubmission = (id) => {
        business.individual.activateSubmission(id).then(() => {
          $timeout(() => {
            setupEdit(id);
            scope.callback();
          });
        });
      };

      scope.deactivateSubmission = (id) => {
        business.individual.deactivateSubmission(id).then(() => {
          $timeout(() => {
            setupEdit(id);
            scope.callback();
          });
        });
      };

      scope.deleteIndividual = (id) => {
        const test = confirm('Do you really want to delete this individual?');
        if (test) {
          business.individual.deleteInd(id).then((result) => {
            if (result) {
              setupEdit(null);
              scope.callback();
            }
          });
        }
      };

      scope.savePerson = () => {
        const postObj = convertInfo(scope.result);
        business.individual.updateIndData(postObj).then((result) => {
          if (result && result.id) {
            // show success screen or something... IT WORKED
            setupEdit(result.id);
            scope.callback();
          }
        });
      };
    }

    return {
      template: require('./editIndividual.template.html'),
      restrict: 'E',
      scope: {
        personId: '=?',
        callback: '&?'
      },
      link: linkFn,
    };
  });
};
