<?php

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


abstract class API
{
  /**
  * Property: method
  * The HTTP method this request was made in, either GET, POST, PUT or DELETE
  */
  protected $method = '';
  /**
  * Property: endpoint
  * The Model requested in the URI. eg: /files
  */
  protected $endpoint = '';
  /**
  * Property: verb
  * An optional additional descriptor about the endpoint, used for things that can
  * not be handled by the basic methods. eg: /files/process
  */
  protected $verb = '';
  /**
  * Property: args
  * Any additional URI components after the endpoint and verb have been removed, in our
  * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
  * or /<endpoint>/<arg0>
  */
  protected $args = Array();
  /**
  * Property: file
  * Stores the input of the PUT request
  */
  protected $file = Null;

  /**
  * Constructor: __construct
  * Allow for CORS, assemble and pre-process the data
  */
  public function __construct($request) {
/*    header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");*/
    header("Content-Type: application/json");

    $this->args = explode('/', rtrim($request, '/'));
    $this->endpoint = array_shift($this->args);
    if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
      $this->verb = array_shift($this->args);
    }

    $this->method = $_SERVER['REQUEST_METHOD'];
    if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
      if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
        $this->method = 'DELETE';
      } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
        $this->method = 'PUT';
      } else {
        throw new Exception("Unexpected Header");
      }
    }

    switch($this->method) {
      case 'DELETE':
      case 'POST':
      $this->request = $this->_cleanInputs($_POST);
      $this->file = getStream();
      break;
      case 'GET':
      $this->request = $this->_cleanInputs($_GET);
      break;
      case 'PUT':
      $this->request = $this->_cleanInputs($_GET);
      $this->file = getStream();
      break;
      default:
      $this->_response('Invalid Method', 405);
      break;
    }
  }

  public function processAPI() {
    if ((int)method_exists($this, $this->endpoint) > 0) {
      return $this->_response($this->{$this->endpoint}($this->args));
    }
    return $this->_response("No Endpoint: $this->endpoint", 404);
  }

  private function _response($data, $status = 200) {
    header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
    return json_encode($data);
  }

  private function _cleanInputs($data) {
    $clean_input = Array();
    if (is_array($data)) {
      foreach ($data as $k => $v) {
        $clean_input[$k] = $this->_cleanInputs($v);
      }
    } else {
      $clean_input = trim(strip_tags($data));
    }
    return $clean_input;
  }

  private function _requestStatus($code) {
    $status = array(
      200 => 'OK',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      500 => 'Internal Server Error',
      );
    return ($status[$code])?$status[$code]:$status[500];
  }
}

?>
