<?php

   /////////////////////////////////////////////////////////////////////////
                          //Database Class
   /////////////////////////////////////////////////////////////////////////

   // Require the Config File
require_once(LIBRARY."config.php");

class MySQLDatabase 
{

   // my mysqli connection
   private $connection;
   function __construct() 
   {
      global $session;
      $this->open_connection();
   }

   // set up the connection
   public function open_connection()
   {
      $this->connection = new mysqli(DB_SERVER, DB_USER, DB_USR_PASS, DB_NAME);
      if (mysqli_connect_errno()) 
      {
         exit('Connect failed: '. mysqli_connect_error());
      }
   }

   // close the connection
   public function close_connection()
   {
      if(isset($this->connection))
      {
         mysqli_close($this->connection);
         unset($this->connection);
      }
   }

   // do general queries
   // could use some work to make it more secure 
   // (figure out a way to do prepared statements)
   public function query($sql)
   {
      // settype($sql, string);
      $result = $this->connection->query($sql);
      $this->confirm_query($result);
      return $result;
   }

   // prepare the values for use in the database
   // could use some work to make itmore secure
   // (add some type of sanitation)
   public function database_prep($value)
   {
      $value = $this->connection->escape_string($value);
      return $value;
   }

   // get the number of rows effected
   public function num_rows($result)
   {
      return mysqli_num_rows($result);
   }

   // returns the last auto generated id
   public function insert_id()
   {
      return mysqli_insert_id($this->connection);
   }

   // returns the number of effected rows on the last query
   public function affected_rows()
   {
      return mysqli_affected_rows($this->connection);
   }

   // returns the results in an array object
   public function fetch_array($results)
   {
      return mysqli_fetch_array($results);
   }

   // does the query and returns the top of the stack of returns
   public function query_and_fetch_one($sql="")
   {
      $rows = $this->query_and_fetch($sql);
      return ($rows) ? array_shift($rows) : false;
   }

   // does the query and returns all of the rows returned
   public function query_and_fetch($sql="") 
   {
      $results = $this->query($sql);
      $rows = array(); 
      while ($row = $this->fetch_array($results))
      { 
         $rows[] = $row; 
      }
      return ($rows) ? $rows : false;
   }
   
   // does the query and returns the rows returned in form of an std object 
   public function query_and_fetch_obj($sql="") 
   {
      $results = $this->query($sql);
      $rows = array(); 
      while ($object = $results->fetch_object())
      { 
         $rows[] = $object; 
      }
      return ($rows) ? $rows : false;
   }

   // returns true or false if the row exists or not
   public function query_and_count($sql) 
   {
      $exists = array_shift($this->query_and_fetch_one($sql));
      return ($exists > 0) ? true : false;
   }

   // kills the process and lets the admin/user know that there was an error with
   // the database comunication.
   private function confirm_query($result)
   {
      if(!$result)
      {
         // if (mysqli_connect_errno()) 
         // {
         //    echo 'Connect failed: '. mysqli_connect_error();
         // }
         die("Database Query Failed: " . mysqli_error($connection));
      }
   }


}

//the database object with its aliases
$database = new MySQLDatabase();
$udb =& $database;
$db =& $database;
$udatabase =& $database;

?>