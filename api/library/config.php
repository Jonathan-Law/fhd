<?php

   /* /////////////////////////////////////////////////////////////////////////
                       Database Connections
   ///////////////////////////////////////////////////////////////////////// */
   // Server Name
   defined('DB_SERVER') ? null : define("DB_SERVER","lawpioneer.fatcowmysql.com");
   // defined('DB_SERVER') ? null : define("DB_SERVER","localhost");
   // Standard User
   defined('DB_USER') ? null : define("DB_USER","fhd_user");
   defined('DB_USR_PASS') ? null : define("DB_USR_PASS","1195Jonathan!user");
   // Admin User
   defined('DB_ADMIN') ? null : define("DB_ADMIN","fhd_admin");
   defined('DB_ADMIN_PASS') ? null : define("DB_ADMIN_PASS","1195Jonathan!admin");
   // Session user
   defined('DB_SESSION') ? null : define("DB_SESSION","fhd_session");
   defined('DB_SES_PASS') ? null : define("DB_SES_PASS","1195Jonathan!");
   // Database Name
   defined('DB_NAME') ? null : define("DB_NAME","fhd");

?>