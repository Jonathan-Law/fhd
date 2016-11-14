<?php

/////////////////////////////////////////////////////////////////////////
//PHP Functions
/////////////////////////////////////////////////////////////////////////

function objectListContains($list, $field, $value) {
  foreach ($list as $key) {
    if (isset($key->$field)  && $key->$field === $value) {
      return true;
    }
  }
  return false;
}

function strip_zeros_from_date($marked_string="") {
  $no_zeros = str_replace('*0','',$marked_string);
  $cleaned_string = str_replace('*','',$no_zeros);
  return $cleaned_string;
}

function attribute_prep($string="") {
  $prepared = str_replace(array('(',')','&','.',',','[',']','/'),"",$string);
  $prepared = ucwords(strtolower($prepared));
  $prepared = str_replace(" ","",$prepared);
  $prepared = lcfirst($prepared);
  return $prepared;
}

function redirect_to($location = NULL) {
  if($location != null) {
    header("Location:".URL."{$location}");
    exit;
  }
}

function refresh_page($page="") {
  header("Location:".URL."{$page}");
  exit;
}

function redirect_home() {
  header("Location:".URL);
  exit;
}

function output_message($message="") {
  if(!empty($message)) {
    return "<p class=\'message\'>{$message}</p>";
  }
  else {
    return "";
  }
}

function resetUri() {
  $_GET['action'] = "";
  $_POST['action'] = "";
  $_REQUEST['action'] = "";
  $_GET['controller'] = "";
  $_POST['controller'] = "";
  $_REQUEST['controller'] = "";
}

function __autoload($class_name) {
  $path = TOOLS."{$class_name}.php";
  $class_name = strtolower($class_name);
  if(file_exists($path)) {
    require_once($path);
  }
  else {
    die("The file {$class_name}.php could not be found.");
  }
  $path = OBJECTS."{$class_name}.php";
  $class_name = strtolower($class_name);
  if(file_exists($path)) {
    require_once($path);
  }
  else {
    die("The file {$class_name}.php could not be found.");
  }
}

function include_root($page="") {
  include(ROOT.$page);
}

function include_module($module="") {
  include(MODULES.$module);
}

function include_page($page="") {
  include(PAGE.$page);
}

function include_window($window="") {
  include(ROOT.'windows'.DS.$window);
}

function include_window_once($window="") {
  include_once ROOT.'windows'.DS.$window;
}

function mysql_time_stamp() {
  $dt = time();
  $mysql_dt = strftime('%Y-%m-%d %H:%M:%S',$dt);
  return $mysql_dt;
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p",$unixdatetime);
}

function one_hour() {
  return 3600;
}

function one_day() {
  return 3600*24;
}

function one_week() {
  return 3600*24*7;
}

function one_month() {
  return 3600*24*30;
}

function one_year() {
  return 3600*24*365;
}
/**
* recast stdClass object to an object with type
*
* @param string $className
* @param stdClass $object
* @throws InvalidArgumentException
* @return mixed new, typed object
*/
function recast($className, stdClass &$object) {
  if (!class_exists($className))
    throw new InvalidArgumentException(sprintf('Inexistant class %s.', $className));

  $new = new $className();

  foreach($object as $property => &$value) {
    // if (property_exists($className, $property))
    // {
    $new->$property = &$value;
    unset($object->$property);
    // }
  }
  unset($value);
  $object = (unset) $object;
  return $new;
}

function log_action($action,$message="",$page) {
  $logfile = LOGS.$page;
  //$new = file_exists($logfile) ? false : true;
  if($handle = fopen($logfile,'a')) { // append
    $timestamp = strftime("%Y-%m-%d %H:%M:%S",time());
    $content = "{$timestamp} | {$action}: {$message}\n";
    fwrite($handle, $content);
    fclose($handle);
    //if($new) { chmod($logfile,0755); }
  }
  else {
    echo "Could not open log file for writing.";
  }
}

// Validate for Strings
function val_string($string) {
  $string = trim($string);
  $string = filter_var($string, FILTER_SANITIZE_STRING);
  $string = stripslashes($string);
  return $string;
}

// Validate for numbers
function val_number($number) {
  $number = trim($number);
  $number = filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $number = filter_var($number, FILTER_VALIDATE_FLOAT);
  return $number;
}

// Validate for emails
function val_email($email) {
  $email = trim($email);
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);
  $email = filter_var($email, FILTER_VALIDATE_EMAIL);
  return $email;
}

// See if page exists
function page_exists($page) {
  return (file_exists(ROOT.'pages'.DS.$page.".php")) ? true : false;
}

// Shorten the length of the string
function shorten_string($string,$max_length) {
  $too_long = (strlen($string) > $max_length) ? true : false;
  if($too_long) {
    $string = substr($string, 0, $max_length-3)."...";
  }
  return $string;
}

function validate_form($inputs) {
  if(array_key_exists('email', $inputs)) {
    if(!$inputs['email']) {
      $errors[] = 'Please Provide a Valid Email Address';
    }
  }
  $errors = array();
  foreach($inputs as $input) { // See if Any Fields are empty
    $empty = "";
    if(!$input) {
      $empty = true;
    }
    if($empty) {
      $errors[] = 'Highlighted Fields Are Required';
      break;
    }
  }
  if(array_key_exists('password', $inputs) && array_key_exists('password2', $inputs)) {
    if($inputs['password'] != $inputs['password2']) {
      $errors[] = 'Passwords Must Match';
    }
  }
  if($errors) {
    return $errors;
  }
  else {
    return false;
  }
}

function debug($var = false) {
  echo "\n<pre style=\"background: #FFFF99; font-size: 10px;\">\n";

  $var = print_r($var, true);
  echo $var . "\n</pre>\n";
}

function objectToArray($d) {
  if (is_object($d)) {
    // Gets the properties of the given object
    // with get_object_vars function
    $d = get_object_vars($d);
  }

  if (is_array($d)) {
    /*
    * Return array converted to object
    * Using __FUNCTION__ (Magic constant)
    * for recursive call
    */
    return array_map(__FUNCTION__, $d);
  }
  else {
    // Return array
    return $d;
  }
}

function arrayToObject($d) {
  if (is_array($d)) {
    /*
    * Return array converted to object
    * Using __FUNCTION__ (Magic constant)
    * for recursive call
    */
    return (object) array_map(__FUNCTION__, $d);
  }
  else {
    // Return object
    return $d;
  }
}

//save a tag for any item (useful for searches)
function saveTags($tag, $table, $id, $group, $personId, $type, $link) {
  $database = cbSQLConnect::connect('object');
  if (isset($database)) {
    $fields = array();
    $fields['name'] = $tag;
    $fields['ftable'] = $table;
    $fields['fid'] = $id;
    $fields['category'] = $group;
    $fields['personid'] = $personId > 0? $personId : -1;
    $fields['type'] = $type;
    // return data
    // return $fields;
    $insert = $database->SQLInsert($fields, "tags"); // return true if sucess or false
    if ($insert && $personId > 0) {
      $person = Person::getById($personId);
      $person = recast("Person", $person);
      $message = '
      <html>
      <head>
        <title>Recent upload to a person on your watch list.</title>
      </head>
      <body>
        <p>Something has been uploaded for '.$person->displayName().'</p>
        <p>Click <a href="'.$link.'">HERE</a> to view the new document, or go to their page to view new content <a href="/?controller=individual&action=homepage&id='.$person->id.'">HERE</a></p>
        <br/>
        <p>Thank you for your continued membership!</p>
        <br/>
        <p>Sincerely</p>
        <p>-The Familyhistorydatabase crew</p>
      </body>
      </html>
      ';
      Favorites::sendUpdate($personId, $message);
      return $insert;
    }
    else {
      return "Insert didn't compute";
    }
  }
}
//save a place for any item (useful for searches)
function savePlaces($place, $table, $id, $group) {
  $database = cbSQLConnect::connect('object');
  if (isset($database)) {
    $fields = array();
    $fields['name'] = $place;
    $fields['ftable'] = $table;
    $fields['fid'] = $id;
    $fields['category'] = $group;
    // return data
    $insert = $database->SQLInsert($fields, "places"); // return true if sucess or false
    if ($insert) {
      return $insert;
    }
    else {
      return "Insert didn't compute";
    }
  }
}
function getRequest($thing) {
  if (isset($_REQUEST[$thing])) {
    return $_REQUEST[$thing];
  }
  else
    return null;
}
function getStream() {
  return json_decode(file_get_contents("php://input"));
}

function sendOwnerUpdate($message, $subject) {
  $from = "noreply@familyhistorydatabase.org";
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $headers .= "From: " . $from;
  return mail('lawpioneer@gmail.com',$subject,$message,$headers);
}
function recursive_array_diff($a1, $a2) {
  $r = array();
  foreach ($a1 as $k => $v) {
    if (array_key_exists($k, $a2)) {
      if (is_array($v)) {
        $rad = recursive_array_diff($v, $a2[$k]);
        if (count($rad)) { $r[$k] = $rad; }
      } else {
        if ($v != $a2[$k]) {
          $r[$k] = $v;
        }
      }
    } else {
      $r[$k] = $v;
    }
  }
  return $r;
}
?>
