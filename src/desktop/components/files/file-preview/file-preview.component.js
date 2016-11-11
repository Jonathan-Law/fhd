module.exports = ngModule => {
  require('./file-preview.component.css');

  ngModule.component('filePreview', {
    template: require('./file-preview.component.html'),
    controller: filePreviewCtrl,
    bindings: {
      file: '<',
      hideOther: '<',
      thumbnails: '<'
    }
  });

  function filePreviewCtrl(configs, $element, $timeout) {
    const ctrl = this;

    ctrl.$onInit = $onInit;
    ctrl.$onChanges = $onChanges;
    ctrl.configs = configs;
    function $onInit() {
      handleFile();
    }
    function $onChanges() {
      handleFile();
    }

    function handleFile() {
      if (!ctrl.file) return;
      if (isImage(ctrl.file.link) && ctrl.file.type) {
        ctrl.type = 'image';
      } else if (isVideo(ctrl.file.link) && ctrl.file.type) {
        ctrl.type = 'video';
      } else {
        ctrl.type = 'other';
        console.log('ctrl.file', ctrl.file);
        if (!ctrl.hideOther) {
          $timeout(() => {
            if (getExtension(ctrl.file.link) === 'html') {
              $element.find('iframe').attr('src', ctrl.configs.baseURL + ctrl.file.link);
            } else {
              $element.find('iframe').attr('src', 'http://docs.google.com/gview?url=' + ctrl.configs.baseURL + ctrl.file.link + '&embedded=true');
            }
          });
        }
      }
      // console.log('ctrl.file', ctrl.file);
    }

    function getExtension(filename) {
      if (filename) {
        const parts = filename.split('.');
        return parts[parts.length - 1];
      }
    }

    function isImage(filename) {
      const ext = getExtension(filename);
      switch (ext.toLowerCase()) {
        case 'jpg':
        case 'gif':
        case 'bmp':
        case 'png':
          //etc
          return true;
        default:
          return false;
      }
    }

    function isVideo(filename) {
      const ext = getExtension(filename);
      switch (ext.toLowerCase()) {
        case 'm4v':
        case 'avi':
        case 'mpg':
        case 'mp4':
          // etc
          return true;
        default:
          return false;
      }
    }
  }

  // inject dependencies here
  filePreviewCtrl.$inject = ['configs', '$element', '$timeout'];
};
