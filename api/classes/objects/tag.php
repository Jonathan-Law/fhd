<?php


require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");

class Tag
{
  protected static $table_name = "tag";
  protected static $db_fields = array('id', 'enum', 'fileid', 'foreignid', 'text');
  public static function get_db_fields()
  {
    $fields = array('id', 'enum', 'fileid', 'foreignid', 'text');
    return $fields;
  }
  public static function nameMe()
  {
    return "Tag";
  }

  public $id;
  public $enum;
  public $fileid;
  public $foreignid;
  public $text;

  public static function getSomething($thing, $type, $lookup)
  {
    $database = cbSQLConnect::connect('array');
    if (isset($database))
    {
      $data = $database->QuerySingle("SELECT $thing FROM `tag` WHERE `foreignid`=$lookup AND `enum`='$table' ORDER BY `id` LIMIT 1");
      if (count($data) == 0)
      {
        return false;
      }
      else
      {
        return $data[0][$thing];
      }
    }
  }

  public static function getTags($switch = NULL, $val = NULL)
  {
    $database = cbSQLConnect::connect('array');
    if (isset($database))
    { 
      if ($switch === 'other') {
        if ($val) {
          $sql = "SELECT * FROM `tag` WHERE `enum`='other' AND `text` LIKE '%".$val."%' GROUP BY 'text'";
        } else {
          $sql = "SELECT * FROM `tag` WHERE `enum`='other'";
        }
      } else if ($switch === 'person') {
        $sql = "SELECT * FROM `tag` WHERE `enum`='person'";
      } else if ($switch === 'place') {
        $sql = "SELECT * FROM `tag` WHERE `enum`='place'";
      } else {
        $sql = "SELECT * FROM `tag`";
      }
      $params = array();
      $results_array = $database->QueryForObject($sql, $params);
      $results = array();
      if (!empty($results_array)) {
        foreach ($results_array as $key) {
          $results[] = recast('Tag', $key);
        }
        return  !empty($results)? $results : false;
      } else {
        return false;
      }
    }
  }

  public static function getTagByData($data){
    if (isset($data)) {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      { 
        if ($data->text === NULL){
          $sql = "SELECT * FROM `tag` WHERE `enum`=:enum AND `fileid`=:fileid AND `foreignid`=:foreignid";
          $params = array(':enum'=>$data->enum, ':fileid'=>$data->fileid, ':foreignid'=>$data->foreignid);
        } else {
          $sql = "SELECT * FROM `tag` WHERE `enum`=':enum' AND `text`=':textInput'";
          $params = array(':enum'=>$data->enum, ':textInput'=>$data->text);
        }
        $results = $database->QueryForObject($sql, $params);
        return  !empty($results)? array_shift($results) : false;
      }
    }
    else {
      return NULL;
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
    else {
      return NULL;
    }
  }

  public static function getByIndId($id = NULL)
  {
    if ($id)
    {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
        $query = "SELECT * FROM `tag` WHERE `foreignid`=".$id." AND `enum`='person'";
        $result = $database->QuerySingle($query);
        return !empty($result)? $result : false;
      }
    }
    else {
      return NULL;
    }
  }

  public static function getByFileId($id = NULL)
  {
    if ($id)
    {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
        $query = "SELECT * FROM `tag` WHERE `fileid`=".$id;
        $result = $database->QuerySingle($query);
        $tags = array();
        $tags['person'] = array();
        $tags['place'] = array();
        $tags['other'] = array();
        foreach ($result as $tag) {
          if ($tag->enum === 'person') {
            $tempPerson = Person::getById($tag->foreignid);
            if ($tempPerson) {
              $tempPerson->text = $tempPerson->selectName()." (".$tempPerson->yearBorn.")";
              $tags['person'][] = $tempPerson;
            }
          } else if ($tag->enum === 'place') {
            $place = Place::getById($tag->foreignid);
            if ($place) {
              $place = recast('Place', $place);
              $place->text = $place->getTypeaheadName();
              $tags['place'][] = $place;
            }
          } else {
            $tags['other'][] = $tag;
          }
        }
        return $tags;
      }
    }
    else {
      return NULL;
    }
  }

  public function save()
  {
    return isset($this->id) ? $this->update() : $this->create();
  }

  protected function create()
  {
    $database = cbSQLConnect::connect('object');
    if (isset($database)) {
      $fields = self::$db_fields;
      $data = array();
      foreach($fields as $key) {
        if ($this->{$key}) {
          $data[$key] = $this->{$key};
        }
        else {
          $data[$key] = NULL;
        }
      }
      $insert = $database->SQLInsert($data, "tag"); 
      if ($insert) {
        return $insert;
      }
      else {
        return false;
      }
    }
  }
  protected function update()
  {
    $database = cbSQLConnect::connect('object');
    if (isset($database)) {
      $fields = self::$db_fields;
      foreach($fields as $key) {
        $flag = $database->SQLUpdate("tag", $key, $this->{$key}, "id", $this->id);
        if ($flag == "fail") {
          break;
        }
      }
      if ($flag == "fail") {
        return false;
      }
      else{
        return $this->id;
      }
    }
  }

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
    $init = new Tag();

    $init->id = NULL;
    $init->enum       = (isset($data['enum']))?       $data['enum'] : NULL;
    $init->fileid     = (isset($data['fileid']))?     $data['fileid'] : NULL;
    $init->foreignid  = (isset($data['foreignid']))?  $data['foreignid'] : NULL;
    $init->other      = (isset($data['other']))?      $data['other'] : NULL;
    return $init;
  }

}


?>
