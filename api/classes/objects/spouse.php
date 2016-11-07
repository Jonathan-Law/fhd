<?php

require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");
require_once(OBJECTS."place.php");
// Require the Database

class Spouse
{

   protected static $table_name = "spouse";
   protected static $db_fields = array('id', 'spouse', 'personId', 'year', 'month', 'day', 'place', 'yearM');
   public static function get_db_fields()
   {
      $fields = array('id', 'spouse', 'personId', 'year', 'month', 'day', 'place', 'yearM');
      return $fields;
   }
   public static function nameMe()
   {
      return "Spouse";
   }

   // Attributes in spouse table
   public $id;
   public $spouse;
   public $personId;
   public $year;
   public $month;
   public $day;
   public $place;
   public $yearM;


   public static function dropById($temp_id = NULL)
   {
      $database = cbSQLConnect::adminConnect('both');
      if (isset($database))
      {
         return $database->SQLDelete('spouse', 'id', $temp_id);
      }
   }

   public static function dropByPerson($temp_id = NULL)
   {
      $database = cbSQLConnect::adminConnect('both');
      if (isset($database))
      {
         $database->SQLDelete('spouse', 'personId', $temp_id);
         return $database->SQLDelete('spouse', 'spouse', $temp_id);
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
            $sql = "SELECT * FROM $name WHERE `spouse`=:id";
            // $sql = "SELECT * FROM $name WHERE `spouse`=:id OR `personId`= :id";
            $params = array( ':id' => $id);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? $results_array : false;
         }
      }
   }

   public static function getByField($spouse = NULL, $individual = NULL)
   {
      if ($spouse && $individual)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM $name WHERE `spouse`=:spouse AND `personId`= :individual";
            $params = array( ':spouse' => $spouse, ':individual' => $individual);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? array_shift($results_array) : false;
         }
      }
   }

   public static function getAllSpousesById($temp_id = NULL)
   {
      if ($temp_id)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM $name WHERE `personId`= :id";
            $params = array(':id' => $temp_id);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? $results_array : false;
         }
      }
   }

   public static function getByPair($spouseId = NULL, $individualId = NULL)
   {
      if ($spouseId && $individualId)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM $name WHERE `personId`= :individual AND `spouse`= :spouse";
            $params = array(':individual' => $individualId, ':spouse' => $spouseId);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? array_shift($results_array) : false;
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
         $insert = $database->SQLInsert($data, "spouse"); // return true if sucess or false
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



  public static function addSpouse($spouse, $individualId, $spouseId) {
    $newSpouse = new Spouse();
    $newSpouse->personId = $individualId;
    $newSpouse->spouse = $spouseId;
    $newSpouse->day = $spouse->marriageDate->day;
    $newSpouse->month = $spouse->marriageDate->month;
    $newSpouse->year = $spouse->marriageDate->year;
    $newSpouse->yearM = $spouse->marriageDate->yearM;
    $spouseId = $newSpouse->save();
    if ($spouseId) {
      $newSpouse->id = $spouseId;
      $marriagePlace = new Place();
      if ($spouse->marriagePlace && isset($spouse->marriagePlace->town)) {
        $marriagePlace->town = $spouse->marriagePlace->town;
      }
      if ($spouse->marriagePlace && isset($spouse->marriagePlace->county)) {
        $marriagePlace->county = $spouse->marriagePlace->county;
      }
      if ($spouse->marriagePlace && isset($spouse->marriagePlace->state)) {
        $marriagePlace->state = $spouse->marriagePlace->state;
      }
      if ($spouse->marriagePlace && isset($spouse->marriagePlace->country)) {
        $marriagePlace->country = $spouse->marriagePlace->country;
      }
      if ($spouse->marriagePlace && isset($spouse->marriagePlace->cemetary)) {
        $marriagePlace->cemetary = $spouse->marriagePlace->cemetary;
      }
      $marriagePlace->ft_name = 'spouse';
      $marriagePlace->fkey = $spouseId;
      $marriageId = $marriagePlace->save();
      if ($marriageId) {
        $newSpouse->place = $marriageId;
        $newSpouse->save();
      }
    }
    return $newSpouse->id;
  }

  public static function updateSpouse($spouse, $spouseId, $individualId) {
    $newSpouse = Spouse::getByPair($spouseId, $individualId);
    if ($newSpouse){
      $newSpouse = recast('Spouse', $newSpouse);
      $newSpouse->day = $spouse->marriageDate->day;
      $newSpouse->month = $spouse->marriageDate->month;
      $newSpouse->year = $spouse->marriageDate->year;
      $newSpouse->yearM = $spouse->marriageDate->yearM;
      $newSpouse->save();
      $marriagePlace = Place::getById($newSpouse->place);
      if ($marriagePlace) {
        $marriagePlace = recast('Place', $marriagePlace);
        if ($spouse->marriagePlace && isset($spouse->marriagePlace->town)) {
          $marriagePlace->town = $spouse->marriagePlace->town;
        }
        if ($spouse->marriagePlace && isset($spouse->marriagePlace->county)) {
          $marriagePlace->county = $spouse->marriagePlace->county;
        }
        if ($spouse->marriagePlace && isset($spouse->marriagePlace->state)) {
          $marriagePlace->state = $spouse->marriagePlace->state;
        }
        if ($spouse->marriagePlace && isset($spouse->marriagePlace->country)) {
          $marriagePlace->country = $spouse->marriagePlace->country;
        }
        if ($spouse->marriagePlace && isset($spouse->marriagePlace->cemetary)) {
          $marriagePlace->cemetary = $spouse->marriagePlace->cemetary;
        }
        $marriagePlace->ft_name = 'spouse';
        $marriagePlace->fkey = $spouseId;
        $marriagePlace->save();
      }
      return $newSpouse->id;
    }
    return false;
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
            $flag = $database->SQLUpdate("spouse", $key, $this->{$key}, "id", $this->id);
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

   public static function createInstance($individual = NULL, $spouse = NULL, $date = NULL, $dateoveride = NULL)
   {
      $temp = Spouse::getByField($spouse, $individual);
      if (!$temp)
      {
         $init = new Spouse();
         $init->id = NULL;
         $init->place = NULL;
      }
      else
      {
         $init = $temp;
      }
      $init->spouse     = $spouse;
      $init->personId   = $individual;
      if ($date)
      {
         $date = explode("/", $date);
         $init->year = $date[2];
         $init->month = $date[1];
         $init->day = $date[0];
         if ($date_overide == 'true')
         {
            $init->yearM = false;
         }
         else
         {
            $init->yearM = true;
         }
      }
      if ($temp)
      {
         $response = recast('Spouse', $init);
      }
      else
         $response = $init;
      return $response;
   }


}


?>
