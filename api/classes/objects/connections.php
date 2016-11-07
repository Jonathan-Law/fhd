<?php


require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");
// Require the Database

class Connections
{

   protected static $table_name = "connections";
   protected static $db_fields = array('id', 't1_name', 't2_name', 't1_id', 't2_id');
   public static function get_db_fields() 
   {
      $fields = array('id', 't1_name', 't2_name', 't1_id', 't2_id');
      return $fields;
   }
   public static function nameMe()
   {
      return "connections";
   }

   // Attributes in connections table
   public $id;
   public $t1_name;
   public $t2_name;
   public $t1_id;
   public $t2_id;


   public static function dropById($temp_id = NULL)
   {
      $database = cbSQLConnect::adminConnect('both');
      if (isset($database))
      {
         return $database->SQLDelete('connections', 'id', $temp_id);
      }
   }

   public static function getSomething($thing, $table, $lookup)
   {
      $database = cbSQLConnect::connect('array');
      if (isset($database))
      {
         $data = $database->QuerySingle("SELECT $thing FROM `connections` WHERE `fkey`=$lookup AND `ft_name`='{$table}' ORDER BY `id` LIMIT 1");
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
            return $database->getObjectById($name, $id);
         }
      }
      else
         return NULL;
   }

   public static function getByField($table = NULL, $id = NULL)
   {
      if ($table && $id)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM $name WHERE (`t1_name`=:table AND `t1_id`=:id) OR (`t2_name`=:table AND `t2_id`=:id)" ;
            $params = array(':table' => $table, ':id' => $id);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? $results_array : false;
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
         // return data
         $insert = $database->SQLInsert($data, "connections"); // return true if sucess or false
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
            $flag = $database->SQLUpdate("connections", $key, $this->{$key}, "id", $this->id);
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


   public static function createInstance($t1_name = NULL, $t2_name = NULL, $t1_id = NULL, $t2_id = NULL)
   {
      $temp = Connections::getByField($t1_name, $t1_id);
      // return $temp;
      if (!$temp)
      {
         $flag = true;
         foreach ($temp as $item)
         {
            if ($item->t2_name == $t2_name && $item->t2_id == $t2_id)
            {
               $flag = false;
               $found = $item;
            }
         }
         if ($flag)
         {
            $init = new Connections();
            $init->id = null;
            $init->t1_name = $t1_name;
            $init->t2_name = $t2_name;
            $init->t1_id = $t1_id;
            $init->t2_id = $t2_id;
            $init->save();
            return $init;
         }
      }
      $init = recast('Connections', $found);
      return $init;
   }


}


?>