<?php 

/////////////////////////////////////////////////////////////////////////
               //Database Object - Common Database Methods
/////////////////////////////////////////////////////////////////////////

// Require the Database
// require_once('mysqli_database.php');
require_once('database.php');

class Database_Object 
{
   // useful fields for later use
   protected static $db_fields;
   protected static $table_name;
   // how to get the table fields
   public static function get_db_fields() 
   {
      $fields = array();
      return $fields;
   }
   // how to get the table name
   public static function nameMe()
   {
      return "database_object";
   }

   // get EVERYTHING
   public static function find_all()
   {
      return $this->find_by_sql("SELECT * FROM ".$this->$table_name);
   }

   // get something by its id and table name
   public static function find_by_id($id=0, $table_name = null)
   {
      global $database;
      $sql = "SELECT * FROM `$table_name` WHERE `id`='".$database->database_prep($id)."' LIMIT 1";
      $results_array = self::find_by_sql($sql);
      return $results_array;
   }

   // return the number of items in this table
   public static function count_all()
   {
      global $database;
      //may need to switch out $this->table_name for nameMe() function...
      $sql = "SELECT COUNT(*) FROM `".$this->$table_name."`";
      $result_set = $database->query($sql);
      $row = $database->fetch_array($result_set);
      return array_shift($row);
   }

   // returns the return from the query in object form.
   public static function find_by_sql($sql="")
   {
      global $database;
      $object_array = $database->query_and_fetch_obj($sql);
      return $object_array;
   }



//==================== work on saving/deleting the object ======================

   // function to prepare the grabbed attributes 
   protected function prepared_attributes()
   {
      global $database;
      $clean_attributes = array();
      foreach($this->attributes() as $key => $value)
      {
         $clean_attributes[$key] = $database->database_prep($value);
      }
      // return $this->attributes();
      return $clean_attributes;
   }

   // function to grab the attributes 
   protected function attributes()
   {
      $array = $this->get_db_fields();
      $attributes = array();
      foreach($array as $field)
      {
         $attributes[$field] = $this->$field;
      }
      return $attributes;
   }

   // function to grab the attributes 
   private function has_attribute($attribute)
   {
      $object_vars = $this->prepared_attributes();
      return array_key_exists($attribute, $object_vars);
   }



   // CRUD Method for saving objects
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