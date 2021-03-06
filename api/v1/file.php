<?php
  function handleFileAPI($args, $that) {
    $session = mySession::getInstance();
      // if ($that->method === 'POST') {
    if ($that->method === 'DELETE' && $session->isLoggedIn()&& $session->isAdmin()) {
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $file = File::getById($id);
        return $file->delete();
      }
    }else if ($that->method === 'POST' && $session->isLoggedIn()&& $session->isAdmin()) {
      if ($that->verb === 'update') {
        $file = $that->file;
        return Dropzone::updateFile($file);
      } else {
        if (!empty($_POST)) {
          $info = json_decode($_POST['info']);
        } else {
          $info = null;
        }
        if ($info) {
          $ds          = DIRECTORY_SEPARATOR;
          $storeFolder = 'uploads';
          if (!empty($_FILES)) {
            $file = recast('Dropzone', $info);
            $file->file = new stdClass();
            $file->file->error = $_FILES['uploadfile']['error'][0];
            $file->file->name = $_FILES['uploadfile']['name'][0];
            $file->file->size = $_FILES['uploadfile']['size'][0];
            $file->file->tmp_name = $_FILES['uploadfile']['tmp_name'][0];
            $file->file->type = $_FILES['uploadfile']['type'][0];
            return $file->save();
          } else {
            return false;
          }
        }
      }
      return false;
    } else if ($that->method === 'GET') {
      if ($that->verb === 'getAll' && $session->isAdmin()) {
        return File::getAll();
      } else if ($that->verb === 'getTypeahead'){
        $type = isset($args[1])? $args[1]: 'person';
        $limit = isset($args[2])? $args[2] : true;
        $val = $args[0];
        if ($val === 'object' && $type === 'place'){
          $val = json_decode($_GET['place']);
          if (isset($val) && !empty($val)) {
            $val = $val[0];
          }
        }
        return File::getByTagType($val, $type, $limit);
        return $type;
      } else if ($that->verb === "getTags") {
          // Get all edit information required for file edits.
        $id = intval($args[0]);
        if (isset($id) && is_numeric($id)){
          return Tag::getByFileId($id);
        }
        return false;
      } else if ($that->verb === "") {
          // Get all edit information required for file edits.
        $id = intval($args[0]);
        if (isset($id) && is_numeric($id)){
          $file = File::getById($id);
          return $file;
        }
        return false;
      }
    }
    return "Only accepts GET AND POSTS requests";
  }
?>
