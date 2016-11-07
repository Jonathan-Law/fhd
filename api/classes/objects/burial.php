<?php


require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");
require_once(OBJECTS."place.php");
// Require the Database

class Burial
{

   protected static $table_name = "burial";
   protected static $db_fields = array('id', 'year', 'month', 'day', 'place', 'personId', 'yearB');
   public static function get_db_fields()
   {
      $fields = array('id', 'year', 'month', 'day', 'place', 'personId', 'yearB');
      return $fields;
   }
   public static function nameMe()
   {
      return "Burial";
   }

   // Attributes in burial table
   public $id;
   public $year;
   public $month;
   public $day;
   public $place;
   public $personId;
   public $yearB;

   public static function dropByPerson($temp_id = NULL)
   {
      $database = cbSQLConnect::adminConnect('both');
      if (isset($database))
      {
         return $database->SQLDelete('burial', 'personId', $temp_id);
      }
   }


   public static function getSomething($thing, $person)
   {
      $database = cbSQLConnect::connect('array');
      if (isset($database))
      {
         $data = $database->QuerySingle("SELECT $thing FROM `burial` WHERE `personId`=$person ORDER BY `id` LIMIT 1");
         if (count($data) == 0)
         {
            return NULL;
         }
         else
         {
            return $data[0][$thing];
         }
      }
   }

   public static function getById($id = NULL)
   {
      if ($id)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            return $database->getOtherObjectById($name, $id);
         }
      }
      else
         return NULL;
   }

   public function save()
   {
      // return $this->id;
      return isset($this->id) ? $this->update() : $this->create();
   }

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
         $insert = $database->SQLInsert($data, "burial"); // return true if sucess or false
         if ($insert)
         {
            return $insert;
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
            $flag = $database->SQLUpdate("burial", $key, $this->{$key}, "id", $this->id);
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
         return ($database->SQLDelete(self::$table_name, 'id', $this->id));
      }
   }

   public static function createInstance($data = NULL, $person_id = NULL)
   {
      $init = new Burial();
      $init->id = NULL;
      // Add some test data
      if ($data['burial_date'])
      {
         $date = $data['burial_date'];
         $date = explode("/", $date);
         $init->year = $date[2];
         $init->month = $date[1];
         $init->day = $date[0];
         if (isset($data['burial_date_overide']))
         {
            $init->yearB = false;
         }
         else
         {
            $init->yearB = true;
         }
      }
      $init->personId = $person_id;

      $init->place = NULL;// $place_id;//$data['place_id'];

      return $init;
   }

}


?>
