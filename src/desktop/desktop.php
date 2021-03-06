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
<head lang="en">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='shortcut icon' type='image/x-icon' href='http://familyhistorydatabase.org/favicon.ico'/>
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
  <!-- <title>{%= o.htmlWebpackPlugin.options.title %}</title> -->
  <title>Family History Database</title>

  <!-- Disable tap highlight on IE -->
  <meta name="msapplication-tap-highlight" content="no">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap-theme.css">
  {% for (var dependency in o.htmlWebpackPlugin.options.pkg.cdnDependencies) { %}
    <script src="{%= o.htmlWebpackPlugin.options.pkg.cdnDependencies[dependency] %}"></script>
  {% } %}
</head>
<body ng-cloak ng-app="da.desktop">
  <div id="appContainer" class="appContainer">
    <div nav class="daNav"></div>
    <div ui-view class="ui-view-content"></div>
  </div>
  {% if (o.htmlWebpackPlugin.options.dev) { %}<script src="/webpack-dev-server.js"></script>{% } %}
  <!-- {%= o.htmlWebpackPlugin.options.pkg.name + ' v' + o.htmlWebpackPlugin.options.pkg.version + ' built on ' + new Date() %} -->
</body>
</html>
