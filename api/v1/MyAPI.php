<?php

/* /////////////////////////////////////////////////////////////////////////
Essential Files to Include
///////////////////////////////////////////////////////////////////////// */
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once("../library/paths.php");

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
require_once 'api.php';
class MyAPI extends API
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

  protected function getThat() {
    $obj = new stdClass();
    $obj->method = $this->method;
    $obj->endpoint = $this->endpoint;
    $obj->request = $this->request;
    $obj->verb = $this->verb;
    $obj->args = $this->args;
    $obj->file = $this->file;
    return $obj;
  }

  /**
  * Example of an Endpoint (where we grab the verbs and arguments and then do
  * something with them...)
  */
  protected function example($args) {
    $session = mySession::getInstance();
    echo $this->verb;
    echo "\n";
    if ($this->method == 'GET') {
      return "Your name is " . $this->User->name . " and " . serialize($args);
    } else {
      return "Only accepts GET requests";
    }
  }

  /**
  * Example of an Endpoint
  */
  protected function process($args) {
    $session = mySession::getInstance();
    echo $this->verb;
    echo "\n";
    if ($this->method == 'GET') {
      return "Things have changed " . $this->User->name . " and " . serialize($args);
    } else {
      return "Only accepts GET requests";
    }
  }

  /**
  * Example of an Endpoint
  */
  protected function session($args) {
    $session = mySession::getInstance();
    if ($this->method == 'GET') {
      return $session->getSessionId();
    } else {
      return "Only accepts GET requests";
    }
  }

  /**
  * Example of an Endpoint
  */
  protected function cookies($args) {
    $session = mySession::getInstance();
    if ($this->method == 'GET') {
      return $_COOKIE;
    } else {
      return "Only accepts GET requests";
    }
  }


  /**
   * User Endpoint
   */
  protected function user($args) {
    require_once('./user.php');
    $that = $this->getThat();
    return handleUserAPI($args, $that);
  }

  protected function typeahead($args) {
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      // if ($session->isLoggedIn()) {
      $value = getRequest('typeahead');
      $limit = intval(getRequest('limit'));
      $list = Person::getSearchInd($value, $limit);
      return $list;
      // } else {
      // return false;
      // }
    }else {
      return "Only accepts GET requests";
    }
  }

  protected function tags($args) {
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      if ($this->verb === 'other') {
        $value = getRequest('typeahead');
        if (!empty($value)) {
          return Tag::getTags('other', $value);
        } else {
          return Tag::getTags('other');
        }
      } else if ($this->verb === 'person') {
        return Tag::getTags('person');
      } else if ($this->verb === 'place') {
        return Tag::getTags('place');
      } else {
        return Tag::getTags();
      }
    } else if ($this->method === 'POST' && $session->isLoggedIn()&& $session->isAdmin()) {
      $tag = recast('Tag', $this->file);
      if ($tag) {
        return $tag->save();
      }
    }else {
      return "Only accepts GET requests";
    }
  }

  protected function profilePic($args){
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      if ($this->verb == "") {
        $id = intval(array_shift($args));
        if ($id && is_numeric($id)) {
          $pic = File::getById($id);
          return $pic;
        }
      } else if ($this->verb === 'person') {
        $id = intval(array_shift($args));
        if ($id && is_numeric($id)) {
          $person = Person::getById($id);
          if (!empty($person->profile_pic)) {
            $pic = File::getById($person->profile_pic);
            return $pic;
          }
        }
      }
    } else if ($this->method === 'POST') {
      if ($session->isLoggedIn()&& $session->isAdmin()) {
        $id = intval(array_shift($args));
        $picId = intval(array_shift($args));
        if ($id && is_numeric($id) && $picId && is_numeric($picId)) {
          $person = Person::getById($id);
          $person->setProfilePic($picId);
          return true;
        }
      }
    }
    return false;
  }

  protected function spouses($args){
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      if ($this->verb == "") {
        $spouseId = intval(array_shift($args));
        $individualId = intval(array_shift($args));
        if (($spouseId && is_numeric($spouseId)) && ($individualId && is_numeric($individualId))) {
          $spouses = Spouse::getByPair($spouseId, $individualId);
          return $spouses;
        }
      }
    }
    return false;
  }

  protected function place($args){
    $session = mySession::getInstance();
    if ($this->method === 'GET') {
      if ($this->verb == "") {
        $placeId = intval(array_shift($args));
        if ($placeId && is_numeric($placeId)) {
          $place = Place::getById($placeId);
          return $place;
        }
      }
    }
    return false;
  }

  protected function file($args){
    require_once('./file.php');
    $that = $this->getThat();
    return handleFileAPI($args, $that);
  }

  protected function activateIndividual($args) {
    $session = mySession::getInstance();
    if (($this->method === 'POST' || $this->method === 'PUT') && $session->isLoggedIn() && $session->isAdmin()){
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $person = Person::getById($id);
        if (isset($person) && $person){
          $user = User::current_user();
          $person->activate($user);
          return $person;
        } else {
          return false;
        }
      } else {
        return false;
      }
    } else {
      return 'You may only POST/PUT to this endpoint and Admin is required';
    }
  }

  protected function deactivateIndividual($args) {
    $session = mySession::getInstance();
    if (($this->method === 'POST' || $this->method === 'PUT') && $session->isLoggedIn() && $session->isAdmin()){
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $person = Person::getById($id);
        if (isset($person) && $person){
          $user = User::current_user();
          $person->deactivate($user);
          return $person;
        } else {
          return false;
        }
      } else {
        return false;
      }
    } else {
      return 'You may only POST/PUT to this endpoint and Admin is required';
    }
  }

  protected function individual($args) {
    require_once('./individual.php');
    $that = $this->getThat();
    return handleIndividualAPI($args, $that);
  }
}
// End Class

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
  $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}


try {
  $API = new MyAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
  echo $API->processAPI();
} catch (Exception $e) {
  echo json_encode(Array('error' => $e->getMessage()));
}
?>
