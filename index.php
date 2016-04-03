<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("api/library/paths.php");

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

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Switcher</title>
    
    
  </head>
  <body>
    
    <script src="/switcher/switcher.js"></script>
    
    <script src="/commons.js"></script>
    
  </body>
</html>
