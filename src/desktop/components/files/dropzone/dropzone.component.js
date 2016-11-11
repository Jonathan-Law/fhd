const Dropzone = require('dropzone');
module.exports = ngModule => {
  require('./dropzone.component.css');

  ngModule.component('fhdropzone', {
    template: require('./dropzone.component.html'),
    controller: dropzoneCtrl,
    bindings: {
      // Inputs should use < and @ bindings.
      // Outputs should use & bindings.
    }
  });

  let uniqueId = 0;

  function dropzoneCtrl($element, $compile, $scope) {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.total = 0;

    const config = {
      url: '/api/v1/file',
      // maxFilesize: 100,
      // 'createImageThumbnails': true,
      // 'thumbnailWidth': 70,
      previewsContainer: 'div.dz-default',
      paramName: 'uploadfile',
      maxThumbnailFilesize: 10,
      parallelUploads: 1,
      autoProcessQueue: false,
      uploadMultiple: true,
      previewTemplate: require('./dropzoneTemplate.html'),
    };

    const eventHandlers = {
      addedfile: function addedFile(file) {
        file.eleHash = `hash${uniqueId}`;
        uniqueId++;
        if (uniqueId > 100000) {
          uniqueId = 0;
        }
        $scope[file.eleHash] = {
          tags: {},
          docType: '',
        };
        angular.element(file.previewElement).find('[data-hashkey]').each(function handleAttrs() {
          const ele = angular.element(this);
          if (ele.attr('ng-class')) {
            ele.attr('ng-class', ele.attr('ng-class').replace('HASH_KEY', file.eleHash));
          }
          if (ele.attr('ng-click')) {
            ele.attr('ng-click', ele.attr('ng-click').replace('HASH_KEY', file.eleHash));
          }
          if (ele.attr('tag')) {
            ele.attr('tag', ele.attr('tag').replace('HASH_KEY', file.eleHash));
          }
        });
        $compile(file.previewElement)($scope);
        const dropzone = this;
        angular.element(file.previewElement).find('.processMe').on('click', () => {
          dropzone.processFile(file);
        });
        angular.element(file.previewElement).find('input').keypress(function enter(e) {
          if (e.which === 13) {
            angular.element(this).next().focus(); //Use whatever selector necessary to focus the 'next' input
            return false;
          }
        });
        angular.element(file.previewElement).find('.tags').keypress(function enter(e) {
          if (e.which === 13) {
            angular.element(this).next().focus(); //Use whatever selector necessary to focus the 'next' input
            return false;
          }
        });
        $scope.$applyAsync(() => {
          ctrl.total = dropzone.files.length;
        });
      },
      removedfile: function removedfile() {
        const dropzone = this;
        $scope.$applyAsync(() => {
          ctrl.total = dropzone.files.length;
        });
      },
      success: (/*file, response*/) => {
        const dropzone = this;
        $scope.$applyAsync(() => {
          ctrl.total = dropzone.files.length;
        });
        // const dropzone = this;
        // dropzone.processQueue.bind(dropzone);
      },
      sending: function sending(file, xhr, formData) {
        const dropzone = this;
        const element = angular.element(file.previewElement);
        const models = element.find('[data-ngModel]');
        const form = {};
        let index = 0;
        while (models[index]) {
          const el = angular.element(models[index]);
          form[el.attr('data-ngModel')] = models[index].value;
          index++;
        }
        const upFile = {};
        if (!form.newName || form.newName === '') {
          dropzone.cancelUpload(file);
        }
        upFile.height = file.height;
        upFile.width = file.width;
        upFile.size = file.size;
        upFile.type = file.type;
        upFile.name = file.name;
        form.tags = $scope[file.eleHash].tags;
        form.fileInfo = upFile;
        form.docType = $scope[file.eleHash].docType || 'image';
        form.new = true;
        const blob = dataURItoBlob(element.find('[data-ngId="image"]').attr('src'));
        formData.append('thumbnail', blob);
        formData.append('info', JSON.stringify(form));
      },
      complete: function complete(file) {
        const dropzone = this;
        if (file.status === Dropzone.CANCELED) {
          file.status = Dropzone.QUEUED;
        } else if (file.status !== Dropzone.ERROR) {
          $scope.tags = {};
          this.removeFile(file);
          if (dropzone.autoProcessQueue) {
            return dropzone.processQueue();
          }
          $scope.$applyAsync(() => {
            ctrl.total = dropzone.files.length;
          });
        } else if (file.status === Dropzone.ERROR) {
          file.status = Dropzone.QUEUED;
        }
      },
      cancled: () => {},
    };

    // here we set up the actual dropzone.
    // if they've given us the location of a template go grab it
    // create a Dropzone for the element with the given options
    ctrl.saveAll = () => {
      ctrl.dropzone.autoProcessQueue = true;
      ctrl.dropzone.processQueue();
    };


    function $onInit() {
      // Called on each controller after all the controllers have been constructed and had their bindings initialized
      // Use this for initialization code.
      init();
    }

    function dataURItoBlob(dataURI) {
      // convert base64/URLEncoded data component to raw binary data held in a string
      let byteString;
      if (dataURI.split(',')[0].indexOf('base64') >= 0) {
        byteString = atob(dataURI.split(',')[1]);
      } else {
        byteString = unescape(dataURI.split(',')[1]);
      }

      // separate out the mime component
      const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

      // write the bytes of the string to a typed array
      const ia = new Uint8Array(byteString.length);
      for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }
      return new Blob([ia], {
        type: mimeString
      });
    }

    function init() {
      // config.options.previewTemplate = require('./dropzoneTemplate.html');
      ctrl.dropzone = new Dropzone($element[0], config);

      angular.forEach(eventHandlers, (handler, event) => {
        ctrl.dropzone.on(event, handler);
      });

      ctrl.processDropzone = () => {
        ctrl.dropzone.processQueue();
      };

      ctrl.resetDropzone = () => {
        ctrl.dropzone.removeAllFiles();
      };
    }
  }

  // inject dependencies here
  dropzoneCtrl.$inject = ['$element', '$compile', '$scope'];
};
