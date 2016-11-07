<?php

/**
* Core Paths for Website
**/
// Core Paths - Server Root - For PHP
defined('DS')             ? null : define('DS', '/');
defined('ROOT')           ? null : define('ROOT', $_SERVER['DOCUMENT_ROOT'].DS);
defined('APIROOT')        ? null : define('APIROOT', ROOT.'api'.DS);
defined('URL')            ? null : define('URL', 'http://'.$_SERVER['SERVER_NAME'].DS);
defined('APIURL')         ? null : define('APIURL', URL.'api/v1'.DS);
defined('IMAGEPATH')      ? null : define('IMAGEPATH', $_SERVER['DOCUMENT_ROOT'].DS);
defined('IMAGEPATHURL')   ? null : define('IMAGEPATHURL', 'http://'.$_SERVER['DOCUMENT_ROOT'].DS);
// Core Relative Paths
defined('LIBRARY')        ? null : define('LIBRARY', APIROOT.'library'.DS);
defined('CONTROLLER')     ? null : define('CONTROLLER', APIROOT.'controller'.DS);
defined('CLASSES')        ? null : define('CLASSES', APIROOT.'classes'.DS);
defined('OBJECTS')        ? null : define('OBJECTS', CLASSES.'objects'.DS);
defined('TOOLS')          ? null : define('TOOLS', CLASSES.'tools'.DS);
// defined('UPLOAD')         ? null : define('UPLOAD', 'dev'.DS.'upload'.DS);
defined('UPLOAD')         ? null : define('UPLOAD', 'upload'.DS);

?>
