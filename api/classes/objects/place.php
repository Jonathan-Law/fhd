<?php


require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");
// Require the Database

class Place
{

   protected static $table_name = "place";
   protected static $db_fields = array('id', 'town', 'county', 'state', 'country', 'cemetary');
   public static function get_db_fields()
   {
      $fields = array('id', 'town', 'county', 'state', 'country', 'cemetary');
      return $fields;
   }
   public static function nameMe()
   {
      return "Place";
   }

   // Attributes in place table
   public $id;
   public $town;
   public $county;
   public $state;
   public $country;
   public $cemetary;

   public static function dropById($temp_id = NULL)
   {
      $database = cbSQLConnect::adminConnect('both');
      if (isset($database))
      {
         return $database->SQLDelete('place', 'id', $temp_id);
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
            return $database->getObjectById($name, $id);
         }
      }
      else
         return NULL;
   }

   public static function getByTown($temp_town = NULL)
   {
      if ($temp_town)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM $name WHERE `town`=:town";
            $params = array(':town' => $temp_town);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? array_shift($results_array) : false;
         }
      }
   }

   public static function getByAll($place)
   {
      if (is_object($place) && isset($place->town))
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM $name WHERE `town` LIKE '%".$place->town."%'";
            if (isset($place->county) && !empty($place->county)) {
              $sql .= "OR `county` LIKE '%".$place->county."%'";
            }
            $sql .= "OR `state` LIKE '%".$place->state."%'";
            $sql .= "OR `country` LIKE '%".$place->country."%'";
            $params = array();
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? array_shift($results_array) : false;
         }
      }
   }

   public function getTypeaheadName(){
      $name = $this->town;
      $name .= $this->county? ", ".$this->county: '';
      $name .= $this->state? ", ".$this->state: '';
      $name .= $this->country? ", ".$this->country: '';
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
         // return data
         $insert = $database->SQLInsert($data, "place"); // return true if sucess or false
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
            $flag = $database->SQLUpdate("place", $key, $this->{$key}, "id", $this->id);
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


   public static function createInstance($data = NULL)
   {
      $init = new Place();

      // Add some test data
      $init->id = NULL;
      $init->town       = (isset($data['town']))?       $data['town'] : NULL;
      $init->county     = (isset($data['county']))?     $data['county'] : NULL;
      $init->state      = (isset($data['state']))?      $data['state'] : NULL;
      $init->country    = (isset($data['country']))?    $data['country'] : NULL;
      $init->cemetary   = (isset($data['cemetary']))?   $data['cemetary'] : NULL;

      return $init;
   }


}


?>
