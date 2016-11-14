<?php


require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");

class User{

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

   public function isAdmin() {
      if($this->rights === 'super' || $this->rights === 'admin') {
         return true;
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
         $sql .= "`username` =  :username ";       //
         $sql .= "OR  `email` =  :username";       //
         $sql .= ") ";                             //
         $sql .= "AND  `password` =  :password ";  //
         $sql .= "AND  `valid` =  '1';";
         $params = array(':password' => $password, ':username' => $username);
         array_unshift($params, '');
         unset($params[0]);
         $results_array = $database->QueryForObject($sql, $params);
         $user = !empty($results_array) ? array_shift($results_array) : false;
         if ($user) {
            $user = recast('User', $user);
            $user->displayableName = $user->displayName();
         }
         return $user;
      }
   }

   public static function getByID($id = '') {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $name = self::$table_name;
         $user = $database->getObjectById($name, $id);
         if ($user) {
            $user = recast('User', $user);
            $user->displayableName = $user->displayName();
         }
         unset($user->password);
         return $user;
      }
   }

   public static function current_user()
   {
      $session = mySession::getInstance();
      $user_id = $session->getVar('user_id');

      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $name = self::$table_name;
         $user = $database->getObjectById($name, $user_id);
         if ($user) {
            $user = recast('User', $user);
            $user->displayableName = $user->displayName();
         }
         unset($user->password);
         return $user;
      }
   }

   public static function getAllUsers()
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $users = $database->QuerySingle("SELECT * FROM `user` ORDER BY `id`");
         if ($users)
         {
            $data = array();
            foreach ($users as $user)
            {
               $data[] = recast("User", $user);
            }
            return $data;
         }
         else
         {
            return false;
         }
      }
   }

   public static function getUserByCred($email = null, $username = null)
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $sql = "SELECT *  ";
         $sql .= "FROM  `user`  ";
         $sql .= "WHERE (";
         $sql .= "`username` =  :username ";       //
         $sql .= "OR  `email` =  :email";       //
         $sql .= ") ";                             //
$sql .= "AND  `valid` =  '1';";
$params = array(':username' => $username, ':email' => $email);
array_unshift($params, '');
unset($params[0]);
$results_array = $database->QueryForObject($sql, $params);
return !empty($results_array) ? array_shift($results_array) : false;
}
}

public static function getUserById($user_id = null)
{
   $database = cbSQLConnect::connect('object');
   if (isset($database) && $user_id)
   {
      $name = self::$table_name;
      return $database->getObjectById($name, $user_id);
   }
}


public static function get_all_something($thing)
{
   $database = cbSQLConnect::connect('object');
   if (isset($database))
   {
      $users = $database->QuerySingle("SELECT $thing FROM `user` WHERE `valid`='1' AND `status`='current' ORDER BY `id`");

      $data = array();
      foreach($users as $user)
      {
         $data[] = strtolower($user->{$thing});
      }
      return $data;
   }
}

public function displayName()
{
   $name = $this->first_name." ";
   $name .= $this->last_name;
   return $name;
}


public function save()
{
      // return $this->id;
   return isset($this->id) ? $this->update() : $this->create();
}

   // create the object if it doesn't already exits.
   // create the object if it doesn't already exits.
protected function create()
{
   $database = cbSQLConnect::connect('object');
   if (isset($database))
   {
      $fields = self::$db_fields;
      $data = array();
      foreach($fields as $key)
      {
         if ($this->{$key})
         {
            $data[$key] = $this->{$key};
         }
         else
            $data[$key] = NULL;

      }
         // array_unshift($params, '');
         // unset($params[0]);
         // return $data;
         $insert = $database->SQLInsert($data, "user"); // return true if sucess or false
         if ($insert)
         {
            return $insert;
         }
         else
         {
            return "Insert didn't compute";
         }
      }
   }

   // update the object if it does already exist.
   protected function update()
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $fields = self::$db_fields;
         foreach($fields as $key)
         {
            $flag = $database->SQLUpdate("user", $key, $this->{$key}, "id", $this->id);
            if ($flag == "fail")
            {
               break;
            }
         }
         if ($flag == "fail")
         {
            return false;
         }
         else
            return true;
      }
   }

   // Delete the object from the table.
   public function delete()
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         return ($database->SQLDelete(self::$table_name, 'id', $this->id));
      }
   }
}


?>
