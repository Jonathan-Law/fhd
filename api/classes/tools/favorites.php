<?php


// Require the Database

require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");

class Favorites{

   protected static $table_name = "favorites";
   protected static $db_fields = array('id', 'user_id', 'person_id');
   // Attributes in user table

   public $id;
   public $user_id;
   public $person_id;

   public static function sendUpdate($personId, $message)
   {
         //       error_reporting(E_ALL);
         // ini_set('display_errors', '1');
      $person = Person::getById($personId);
      $person = recast("Person", $person);
      $list = Favorites::getFavoritesByPerson($person->id);
      unset($to);
      foreach ($list as $individual) 
      {
         $user = User::getUserById($individual->user_id);
         if (!empty($user->email) && $user->rights == "medium")
         {
            if (isset($to))
            {
               $to .= ", ".$user->email;
            }   
            else
            {
               $to = $user->email;
            }
         }
         # code...
      }
      $subject = "Update message for ".$person->displayName();
      $from = "noreply@familyhistorydatabase.org";
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      $headers .= "From:" . $from;
      mail($to,$subject,$message,$headers);
   }

   public static function getFavorites($userId, $personId)
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $results = $database->QuerySingle("SELECT * FROM `favorites` WHERE `user_id`='".$userId."' AND `person_id`='".$personId."' ORDER BY `id`");

         $data = array();
         if (!empty($results))
         {
            foreach($results as $result)
            {
               $data[] = $result;
            }
            return $data;
         }
         else
         {
            return false;
         }
      }
   }

   public static function checkFavoriteById($personId = null, $userId = null)
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database) && $userId && $personId)
      {
         $results = $database->QuerySingle("SELECT * FROM `favorites` WHERE `user_id`='".$userId."' AND `person_id`='".$personId."' ORDER BY `id`");

         $data = array();
         if (!empty($results))
         {
            foreach($results as $result)
            {
               $data[] = $result;
            }
            return $data;
         }
         else
         {
            return false;
         }
      }
   }

   public static function getFavoritesByUser($userId)
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $results = $database->QuerySingle("SELECT * FROM `favorites` WHERE `user_id`='".$userId."'ORDER BY `id`");

         $data = array();
         if (!empty($results))
         {
            foreach($results as $result)
            {
               $data[] = $result;
            }
            return $data;
         }
         else
         {
            return false;
         }
      }
   }

   public static function getFavoritesByPerson($person)
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database) && $person)
      {
         $results = $database->QuerySingle("SELECT * FROM `favorites` WHERE `person_id`='".$person."'ORDER BY `id`");

         $data = array();
         if (!empty($results))
         {
            foreach($results as $result)
            {
               $data[] = $result;
            }
            return $data;
         }
         else
         {
            return false;
         }
      }
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

         // return $data;
         $insert = $database->SQLInsert($data, "favorites"); // return true if sucess or false
         if ($insert)
         {
            $person = Person::getById($this->person_id);
            $person = recast("Person", $person);
            $user = User::getUserById($this->user_id);
            $user = recast("Person", $user);
            $to = "lawpioneer@gmail.com";
            $subject = "Someone Added to favorites";
            $message = $person->displayName()." has been added to the favorite list of ".$user->displayName()."\n";
            $message .= "\nLove Jon";
            $from = "newFavorites@familyhistorydatabase.org";
            $headers = "From:" . $from;
            mail($to,$subject,$message,$headers);
            return "$insert";
         }
         else
         {
            return false;
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
            $flag = $database->SQLUpdate("favorites", $key, $this->{$key}, "id", $this->id);
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
            return $this->id;
      }
   }

   // Delete the object from the table.
   public function delete()
   {
      $database = cbSQLConnect::adminConnect('object');
      if (isset($database))
      {
         return ($database->SQLDelete('favorites', 'id', $this->id));
      }
   }
}


?>