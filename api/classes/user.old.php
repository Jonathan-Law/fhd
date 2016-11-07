<?php


// Require the Database

require_once(CLASSES."cbSQLRetrieveData.php");
require_once(CLASSES."cbSQLConnectVar.php");
require_once(CLASSES."cbSQLConnectConfig.php");
require_once('database_object.php');

class User extends Database_Object {

   protected static $table_name = "user";
   protected static $db_fields = array('id','username','email','password','first_name','last_name','rights','last_loggedin','status','valid', 'other_comments', 'gender', 'company_position');
   // Attributes in user table

   public $id;
   public $username;
   public $email;
   public $password;
   public $first_name;
   public $last_name;
   public $rights;
   public $last_loggedin;
   public $status;
   public $valid;
   public $other_comments;
   public $gender;
   public $company_position;

   
   // This funciton tests the connection to the database.
   public function test()
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
            // $data = $this->fetch_assoc->QuerySingle("SELECT * FROM `user`"); 
         $data = $database->QuerySingle("SELECT * FROM `user`");

            // $data = $this->fetch_lazy->QuerySingle("SELECT * FROM `user`");
         return $data;
      }
      else
         return "There wasn't a connection...";
   }

   public function full_name()
   { 
      if(isset($this->first_name) && isset($this->last_name))
      {
         return $this->first_name . " " . $this->last_name;
      } 
      else 
      {
         return "";
      }
   }

   public function gender()
   {
      if (isset($this->gender)) 
      {
         return $this->gender;
      } 
      else 
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $tempId = $this->id;
            $data = $database->Query("SELECT `gender` FROM :table WHERE `username` = :value LIMIT 1;",
              array(array(':value' => $tempId, ':table' => $name)));
            echo($data);
            $this->gender = $data['gender'];
         }

         return $this->gender;
      }
   }

   public static function authenticate($username="",$password="")
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $sql = "SELECT *  ";
         $sql .= "FROM  `user`  ";
         $sql .= "WHERE (";
         $sql .= "`username` =  :username ";
         $sql .= "OR  `email` =  :username";
         $sql .= ") ";
         $sql .= "AND  `password` =  :password ";
         $sql .= "AND  `valid` =  '1';";
         $params = array(':password' => $password, ':username' => $username);
         array_unshift($params, '');
         unset($params[0]);
         $results_array = $database->QueryForObject($sql, $params);
         return !empty($results_array) ? array_shift($results_array) : false;
      }
   }

   public static function current_user()
   {
      $session = mySession::getInstance();
      $user_id = $session->getVar('user_id');
      // return $user_id;
      $user = self::find_by_id($user_id, self::$table_name);
      return $user;
   }

   public static function get_all_users()
   {
      global $udb;
      $users = $udb->query_and_fetch("SELECT `id` FROM `user` WHERE `valid`='1' AND `status`='current' ORDER BY `id`");
      $ids = array();
      foreach($users as $user)
      {
         $ids[] = $user['id'];
      }
      return $ids;
   }

   public static function get_all_usernames()
   {
      global $udb;
      $users = $udb->query_and_fetch("SELECT `username` FROM `user` WHERE `valid`='1' AND `status`='current' ORDER BY `id`");
      $usernames = array();
      foreach($users as $user)
      {
         $usernames[] = strtolower($user['username']);
      }
      return $usernames;
   }

   public static function get_all_emails()
   {
      global $udb;
      $users = $udb->query_and_fetch("SELECT `email` FROM `user` WHERE `valid`='1' AND `status`='current' ORDER BY `id`");
      $emails = array();
      foreach($users as $user)
      {
         $emails[] = $user['email'];
      }
      return $emails;
   }

   public static function count_all_users($sql)
   {
      global $udb;
      $sql = "SELECT COUNT(*) FROM `users` WHERE ".$sql."";
      $sql .= " ORDER BY `last_name`";
      $result_set = $udb->query($sql);
      $row = $udb->fetch_array($result_set);
      return array_shift($row);
   }

   public static function count_alumni($sql)
   {
      global $udb;
      $sql = "SELECT COUNT(*) FROM `users` WHERE ".$sql."";
      $sql .= " AND `affiliation`='Alumni'";
      $result_set = $udb->query($sql);
      $row = $udb->fetch_array($result_set);
      return array_shift($row);
   }

   public static function count_community($sql)
   {
      global $udb;
      $sql = "SELECT COUNT(*) FROM `users` WHERE ".$sql."";
      $sql .= " AND `affiliation`='Community'";
      $result_set = $udb->query($sql);
      $row = $udb->fetch_array($result_set);
      return array_shift($row);
   }

   public static function count_current($sql)
   {
      global $udb;
      $sql = "SELECT COUNT(*) FROM `users` WHERE ".$sql."";
      $sql .= " AND `affiliation`='Current'";
      $result_set = $udb->query($sql);
      $row = $udb->fetch_array($result_set);
      return array_shift($row);
   }

   public static function find_by_pag($sql,$per_page,$offset)
   {
      $sql = "SELECT * FROM `users` WHERE ".$sql."";
      $sql .= " ORDER BY `last_name`";
      $sql .= " LIMIT {$per_page}";
      $sql .= " OFFSET {$offset}";
      return self::find_by_sql($sql);
   }

      public function save()
   {
      // return $this->id;
      return isset($this->id) ? $this->update() : $this->create();
   }

   // create the object if it doesn't already exits.
   protected function create()
   {
      global $database;
      $attributes = $this->prepared_attributes();
      $sql = "INSERT INTO ".$this->$table_name." (";
         $sql .= join(", ", array_keys($attributes));
         $sql .= ") VALUES ('";
         $sql .= join("', '", array_values($attributes));
         $sql .= "')";
      if($database->query($sql)) //send the query and see if it works
      {
         $this->id = $database->insert_id();
         return true;
      }
      else
      {
         return false;
      }
   }

   // update the object if it does already exist.
   protected function update()
   {
      global $database;
      // return self::prepared_attributes();
      $attributes = self::prepared_attributes();
      $attribute_pairs = array();
      foreach($attributes as $key => $value)
      {
         $attribute_pairs[] = "{$key}='{$value}'";
      }
      $table_name = strtolower($this->nameMe());
      $sql = "UPDATE ".$table_name." SET ";
      $sql .= join(", ", $attribute_pairs);
      $sql .= " WHERE `id` = ".$this->id;
      // return $sql;
      $database->query($sql);
      return ($database->affected_rows() == 1) ? true : false;
   }


   // Delete the object from the table.
   public function delete()
   {
      global $database;
      $sql = "DELETE FROM ".$this->$table_name." ";
      $sql .= "WHERE `id` = ".$database->database_prep($this->id);
      $sql .= " LIMIT 1";
      $database->query($sql);
      return ($database->affected_rows() == 1) ? true : false;
   }
}


?>