<?php

require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");


class Parents
{

  protected static $table_name = "parents";
  protected static $db_fields = array('id', 'parentId', 'gender', 'child');
  public static function get_db_fields()
  {
    $fields = array('id', 'parentId', 'gender', 'child');
    return $fields;
  }
  public static function nameMe()
  {
    return "Parents";
  }

  // Attributes in parents table
  public $id;
  public $parentId;
  public $gender;
  public $child;

  public static function dropByPerson($temp_id = NULL)
  {
    $database = cbSQLConnect::adminConnect('both');
    if (isset($database))
    {
      return $database->SQLDelete('parents', 'child', $temp_id);
    }
  }


  public static function getParentsOf($id)
  {
    $database = cbSQLConnect::connect('object');
    if (isset($database)) {
      $sql = "SELECT * FROM `parents` WHERE `child`= :id";
      $params = array(':id' => $id);
      $results_array = $database->QueryForObject($sql, $params);
      return !empty($results_array) ? $results_array : false;
    }
  }

  public static function getChildrenOf($id)
  {
    $database = cbSQLConnect::connect('object');
    if (isset($database)) {
      $sql = "SELECT * FROM `parents` WHERE `parentId`= :id";
      $params = array(':id' => $id);
      $results_array = $database->QueryForObject($sql, $params);
      return !empty($results_array) ? $results_array : false;
    }
  }

  public static function dropById($temp_id = NULL)
  {
    $database = cbSQLConnect::adminConnect('both');
    if (isset($database))
    {
      return $database->SQLDelete('parents', 'id', $temp_id);
    }
  }

  public static function getByField($parentId = NULL, $temp_id = NULL)
  {
    if ($temp_id && $parentId)
    {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
        $name = self::$table_name;
        $sql = "SELECT * FROM $name WHERE `parentId`=:parentId AND `child`= :id";
        $params = array( ':parentId' => $parentId, ':id' => $temp_id);
        array_unshift($params, '');
        unset($params[0]);
        $results_array = $database->QueryForObject($sql, $params);
        return !empty($results_array) ? array_shift($results_array) : false;
      }
    }
  }

  public static function getAllParentsById($temp_id = NULL)
  {
    if ($temp_id)
    {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
        $name = self::$table_name;
        $sql = "SELECT * FROM $name WHERE `child`= :id";
        $params = array(':id' => $temp_id);
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
      // return true if sucess or false
      $insert = $database->SQLInsert($data, "parents");
      if ($insert)
      {
        return $insert;
      }
      else
      {
        // return "Insert didn't compute";
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
        $flag = $database->SQLUpdate("parents", $key, $this->{$key}, "id", $this->id);
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

  public static function createInstance($parentId = NULL, $gender = NULL, $child = NULL)
  {
    $temp = Parents::getByField($parentId, $child);
    // return $temp;
    if (!$temp)
    {
      $init = new Parents();
      $init->id = null;
      $init->parentId = $parentId;
      if ($gender == 'male')
      {
        $init->gender = 'father';
      }
      else
      {
        $init->gender = 'mother';
      }
      $init->child = $child;
      return $init;
    }
    else
    {
      $temp->parentId = $parentId;
      if ($gender == 'male')
      {
        $temp->gender = 'father';
      }
      else
      {
        $temp->gender = 'mother';
      }
      $temp->child = $child;
    }
    $init = recast('Parents', $temp);
    return $init;
  }

}


?>
