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
require_once(LIBRARY."exceptions.php");

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
class UsersEndpoint extends API
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
    // if we don't have a verb, get current user
    if ($this->method === 'GET') {
      if (empty($args)) {
        $session = mySession::getInstance();
        $user_id = $session->getVar('user_id');
        if ($user_id) {
          $user = User::getById($user_id);
          unset($user->password);
          return $user;
        } else {
          return false;
        }
      } else if (!empty($args) && $session->isLoggedIn() && $session->isAdmin()) {
        // otherwise, if we have an id, get the id user
        $id = intval(array_shift($args));
        if ($id && is_numeric($id)) {
          $user = User::getById($id);
          unset($user->password);
          return $user;
        } else {
          return User::getAllUsers();
        }
      } else if ($this->method === 'GET') {
        throw new ForbiddenException();
      }
    }
    throw new NoEndpointException();
  }

  protected function login($args) {
    require_once(APIROOT.'controller/user.php');
    if ($this->method === 'POST' && !empty($this->file)) {
      $result = $this->file;
      $result = login(isset($result->username)? $result->username: null,
        isset($result->password)? $result->password: null, true);
      if (!$result) {
        throw new Exception('Invalid username and password');
      }
      return User::current_user();
    } else if ($this->method === 'POST') {
      throw new Exception('Invalid username and password');
    }
    throw new NoMethodException();
  }

  protected function logout($args) {
    $session = mySession::getInstance();
    if ($this->method === 'POST' || $this->method === 'GET') {
      return $session->logout();
    }
    throw new NoMethodException();
  }

  protected function register($args) {
    require_once(APIROOT.'controller/user.php');
    if ($this->method === 'POST') {
      $result = $this->file;
      $result->username = isset($result->username)? $result->username: null;
      $result->password = isset($result->password)? $result->password: null;
      $result->email = isset($result->email)? $result->email: null;
      $result->first = isset($result->first)? $result->first: null;
      $result->last = isset($result->last)? $result->last: null;
      $result->gender = isset($result->gender)? $result->gender: null;

      $result = register($result);
      return User::current_user();
    }
    throw new NoMethodException();
  }

  protected function sendAdminMessage($args) {
    $session = mySession::getInstance();
    if ($this->method === 'POST' && $session->isLoggedIn()) {
      $user = User::current_user();
      $this->file->name = $user->displayableName;
      $this->file->email = $user->email;
      return _sendAdminMessage($this->file);
    } else if ($this->method === 'POST') {
      throw new ForbiddenException();
    }
    throw new NoMethodException();
  }

  protected function resetPassword($args) {
    if ($this->method === 'POST') {
      $user = User::getByUsername($this->file->username);
      if ($user) {
        return $user->resetPassword();
      }
    }
    throw new NoMethodException();
  }

  protected function validate($args) {
    require_once(APIROOT.'controller/user.php');
    if ($this->method === 'GET') {
      $id = getRequest('id');
      $value = getRequest('validate');
      return validate($id, $value);
    }
    throw new NoMethodException();
  }

  protected function isLoggedIn($args) {
    if ($this->method === 'GET') {
      return User::current_user();
    }
    throw new NoMethodException();
  }

  protected function getUserInfo($args) {
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      if (!empty($args) && $session->isLoggedIn() && $session->isAdmin()) {
        $id = intval(array_shift($args));
        if ($id && is_numeric($id)) {
          $user = User::getById($id);
          unset($user->password);
          return $user;
        } else {
          return User::getAllUsers();
        }
      }
      throw new ForbiddenException();
    }
    throw new NoMethodException();
  }

  private function _sendAdminMessage($message) {
    $body = "<div>
      Message from User: ".$message->name." &mdash; ".$message->email."
      <br>
      <br>
      ".$message->message."
    </div>";
    $body;
    return sendOwnerUpdate($body, 'FHD: User Message from '.$message->name);
  }

}
// End Class

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}


try {
  $API = new UsersEndpoint($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
  echo $API->processAPI();
} catch (Exception $e) {
  $body = stdClass();
  $body->message = $e.getMessage();
  $body->type = "Internal Server Error";
  header("HTTP/1.1 " . 500 . " " . $API->_requestStatus(500));
  echo json_encode($body);
}
?>
