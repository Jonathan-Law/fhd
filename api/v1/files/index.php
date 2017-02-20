<?php

/* /////////////////////////////////////////////////////////////////////////
Essential Files to Include
///////////////////////////////////////////////////////////////////////// */
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once("../../library/paths.php");

// Load the Config File
require_once(LIBRARY."config.php");

// Load the functions so that everything can use them
require_once(LIBRARY."functions.php");

// Load the core objects
// require_once(CLASSES."mysqli_database.php");

require_once(OBJECTS."birth.php");
require_once(OBJECTS."death.php");
require_once(OBJECTS."burial.php");
require_once(OBJECTS."parents.php");
require_once(OBJECTS."person.php");
require_once(OBJECTS."spouse.php");
require_once(OBJECTS."place.php");
require_once(OBJECTS."file.php");
require_once(OBJECTS."tag.php");
require_once(OBJECTS."dropzone.php");
require_once(OBJECTS."connections.php");


require_once(TOOLS."user.php");
require_once(TOOLS."favorites.php");
require_once(TOOLS."pagination.php");
require_once(TOOLS."url.php");
require_once(TOOLS."mySession.conf.php");
require_once(TOOLS."mySession.class.php");
require_once(TOOLS."cbSQLConnect.class.php");

$session = mySession::getInstance();
require_once '../api.php';
class FilesEndpoint extends API
{

  protected $User;

  public function __construct($request, $origin) {
    parent::__construct($request);

    // Abstracted out for example
    // $APIKey = new Models\APIKey();
    // $User = new Models\User();

    // if (!array_key_exists('apiKey', $this->request)) {
    //   throw new Exception('No API Key provided');
    // } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
    //   throw new Exception('Invalid API Key');
    // } else if (array_key_exists('token', $this->request) &&
    //  !$User->get('token', $this->request['token'])) {

    //   throw new Exception('Invalid User Token');
    // }

    $this->User->name = "Jonathan";
  }

  protected function doDefault($args) {
    $session = mySession::getInstance();
    if ($this->method === 'DELETE' && $session->isLoggedIn()&& $session->isAdmin()) {
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $file = File::getById($id);
        return $file->delete();
      }
      throw new NoEndpointException();
    } else if ($this->method === 'DELETE') {
      throw new ForbiddenException();
    } else if ($this->method === 'GET') {
      // Get all edit information required for file edits.
      $id = intval($args[0]);
      if (!empty($id) && isset($id) && is_numeric($id)){
        $file = File::getById($id);
        return $file;
      }
      return File::getAll();
    } else if ($this->method === "POST") {
      if (!empty($_POST)) {
        $info = json_decode($_POST['info']);
      } else {
        $info = null;
      }
      if ($info) {
        $ds = DIRECTORY_SEPARATOR;
        $storeFolder = 'uploads';
        if (!empty($_FILES)) {
          $file = recast('Dropzone', $info);
          $file->file = new stdClass();
          $file->file->error = $_FILES['uploadfile']['error'][0];
          $file->file->name = $_FILES['uploadfile']['name'][0];
          $file->file->size = $_FILES['uploadfile']['size'][0];
          $file->file->tmp_name = $_FILES['uploadfile']['tmp_name'][0];
          $file->file->type = $_FILES['uploadfile']['type'][0];
          $file->status = $session->isLoggedIn() && $session->isAdmin() ? 'A' : 'I';
          return $file->save();
        }
        throw new Exception('Failed to save file');
      }
      throw new Exception('Failed to retrieve file information');
    }
    throw new NoMethodException();
  }

  protected function update($args) {
    $session = mySession::getInstance();
    if ($this->method === 'POST') {
      $file = $this->file;
      $file->status = $session->isLoggedIn() && $session->isAdmin() ? (isset($file->status) ? $file->status : 'I') : 'I';
      return Dropzone::updateFile($file);
    } else if ($this->method === 'POST') {
      throw new ForbiddenException();
    }
    throw new NoMethodException();
  }

  protected function getAll($args) {
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      $id = intval($args[0]);
      if (isset($id) && !empty($id) && is_numeric($id)) {
        return File::getAll($id);
      }
      return File::getAll();
    }
    throw new NoMethodException();
  }

  protected function getTypeahead($args) {
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      $type = isset($args[0]) ? $args[0] : 'person';
      $limit = isset($args[1]) ? $args[1] : true;
      $id = intval($args[2]);
      $individual = isset($id) && !empty($id) && is_numeric($id) ? $id : null;
      $val = $this->verb;
      if ($val === 'object' && $type === 'place') {
        $val = json_decode($_GET['place']);
        if (isset($val) && !empty($val)) {
          $val = $val[0];
        }
      }
      return File::getByTagType($val, $type, $limit, $individual);
      return $type;
    }
    throw new NoMethodException();
  }

  protected function getTags($args) {
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      // Get all edit information required for file edits.
      if (isset($args) && !empty($args)) {
        $id = intval($args[0]);
        if (!empty($id) && isset($id) && is_numeric($id)){
          return Tag::getByFileId($id);
        }
      }
      throw new Exception('Id Required');
    }
    throw new NoMethodException();
  }
}
// End Class

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
  $API = new FilesEndpoint($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
  echo $API->processAPI();
} catch (Exception $e) {
  $body = stdClass();
  $body->message = $e.getMessage();
  $body->type = "Internal Server Error";
  header("HTTP/1.1 " . 500 . " " . $API->_requestStatus(500));
  echo json_encode($body);
}

?>
