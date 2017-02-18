<?php


require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");


class Person
{

  protected static $table_name = "person";
  protected static $db_fields = array('id', 'submitter', 'status', 'firstName', 'middleName', 'lastName', 'yearBorn', 'yearDead', 'yearB', 'yearD', 'relationship','profile_pic', 'sex');
  public static function get_db_fields()
  {
    $fields = array('id', 'submitter', 'status', 'firstName', 'middleName', 'lastName', 'yearBorn', 'yearDead', 'yearB', 'yearD', 'relationship','profile_pic', 'sex');
    return $fields;
  }
  public static function nameMe()
  {
    return "Person";
  }

  // Attributes in person table
  public $id;
  public $submitter;
  public $status;
  public $firstName;
  public $middleName;
  public $lastName;
  public $yearBorn;
  public $yearDead;
  public $yearB;
  public $yearD;
  public $relationship;
  public $profile_pic;
  public $sex;


  public static function dropByPerson($temp_id = NULL)
  {
    $database = cbSQLConnect::adminConnect('both');
    if (isset($database))
    {
      return $database->SQLDelete('person', 'id', $temp_id);
    }
  }




  public static function getIndividuals()
  {
    $database = cbSQLConnect::connect('object');
    $result = array();
    if (isset($database))
    {
      $people = $database->QuerySingle("SELECT * FROM `person` ORDER BY `lastName`");
      if ($people)
      {
        foreach($people as $aperson)
        {
          $temp = array();
          $aperson = recast("Person", $aperson);
          $aperson->displayName = $aperson->displayName();
          $aperson->selectName = $aperson->selectName();
          $temp[] = $aperson->firstName;
          $temp[] = $aperson->lastName;
          $temp[] = $aperson->yearBorn;
          $temp[] = $aperson->yearDead;
          $temp[] = $aperson->id;
          $temp[] = $aperson->submitter;
          $temp[] = $aperson->status;
          $temp[] = $aperson->middleName;
          $temp['data'] = $aperson;
          $result[] = $temp;
        }
        return $result;
      }
      else
      {
        return "none";
      }
    }
  }

  public static function getSearchInd($search = null, $limit = 10)
  {
    $database = cbSQLConnect::connect('object');
    $result = array();
    if (isset($database)) {
      $finalTarget = '';
      $target = explode(' ', $search);
      $target = '+'.implode(' +', $target).'*';

      // Here we have to prepare the statment using PDO 'quote'... Apparently the AGAINST
      // must have a constant string to work with or it breaks... so we can't prepare the statement...
      $target = $database->prepareQuote($target);
      $user = User::current_user();
      if (is_object($user)) {
        $searchCrit = "AND (`status`='A' OR `submitter`='".$user->id."')";
      } else {
        $searchCrit = "";
      }
      if (empty($search) || $search === null) {
        $people = $database->QuerySingle("SELECT DISTINCT * FROM `person` WHERE `status`='A' ORDER BY firstName ASC");
      } else {
        if ($limit && $limit !== 0) {
          $people = $database->QuerySingle("SELECT *, MATCH(firstName, middleName, lastName) AGAINST(".$target." IN BOOLEAN MODE) AS score FROM `person` WHERE MATCH(firstName, middleName, lastName) AGAINST(".$target." IN BOOLEAN MODE) ".$searchCrit." ORDER BY score DESC LIMIT 0, ".$limit);
        } else {
          $people = $database->QuerySingle("SELECT *, MATCH(firstName, middleName, lastName) AGAINST(".$target." IN BOOLEAN MODE) AS score FROM `person` WHERE MATCH(firstName, middleName, lastName) AGAINST(".$target." IN BOOLEAN MODE) ".$searchCrit." ORDER BY score DESC");
        }
      }
      if ($people) {
        foreach($people as $aperson) {
          $aperson = recast("Person", $aperson);
          $aperson->displayableName = $aperson->displayName();
          $aperson->selectableName = $aperson->selectName();
          $aperson->typeahead = $aperson->selectName()." (".$aperson->yearBorn.")";
          $result[] = $aperson;
        }
        return $result;
      } else {
        return false;
      }
    }
    return false;
  }

  public function getParentsGen($i){
    $parents = $this->getParents();
    $this->parents = array();
    if ($i === 0) {
      foreach ($parents as $parent) {
        $temp = Person::getById($parent->parentId);
        $this->parents[] = $temp;
      }
      return;
    } else {
      foreach ($parents as $parent) {
        $temp = Person::getById($parent->parentId);
        $temp->getParentsGen($i - 1);
        $this->parents[] = $temp;
      }
    }
  }

  public static function getNumPics()
  {
    $database = cbSQLConnect::connect('object');
    $result = array();
    if (isset($database))
    {
      $num_pics = $database->QuerySingle("SELECT * FROM person WHERE `profile_pic` IS NOT NULL;");
      if ($num_pics)
      {
        $result[0] = "success";
        $result[1] = count($num_pics);
        $result[2] = array();
        $count = 0;
        foreach ($num_pics as $individual)
        {
          $result[2][$count] = File::getById($individual->profile_pic);
          $result[2][$count]->individual_id = $individual->id;
          $count++;
        }
        return $result;
      }
      else
      {
        return null;
      }
    }
  }

  public static function getSubmissions($user = null)
  {
    $database = cbSQLConnect::connect('object');
    $result = array();
    if (isset($database))
    {
      if (isset($user) && isset($user->id)){
        $data = $database->QuerySingle("SELECT DISTINCT * FROM `person` WHERE `submitter`='".$user->id."'");
      } else {
        $data = $database->QuerySingle("SELECT DISTINCT * FROM `person` WHERE `status`='I'");
      }
      if (count($data) == 0)
      {
        return false;
      }
      else
      {
        foreach($data as $aperson) {
          $aperson = recast("Person", $aperson);
          $aperson->displayableName = $aperson->displayName();
          $aperson->selectableName = $aperson->selectName();
          $aperson->typeahead = $aperson->selectName()." (".$aperson->yearBorn.")";
          $result[] = $aperson;
        }
        return $result;
      }
    }
  }

  public static function getLastNames($letter, $all = false, $user = null)
  {
    $allStatus = (isset($user) && isset($user->rights) && ($user->rights === 'super' || $user->rights === 'admin'))? true: false;
    $database = cbSQLConnect::connect('array');
    if (isset($database))
    {
      if ($all) {
        $letter = $letter."%";
      }
      if ($allStatus){
        $data = $database->QuerySingle("SELECT DISTINCT * FROM `person` WHERE `lastName` LIKE '".$letter."' GROUP BY `lastName`");
      } else if(isset($user) && isset($user->id)) {
        $data = $database->QuerySingle("SELECT DISTINCT * FROM `person` WHERE `lastName` LIKE '".$letter."' AND (`status`='A' OR `submitter`='".$user->id."') GROUP BY `lastName`");
      } else {
        $data = $database->QuerySingle("SELECT DISTINCT * FROM `person` WHERE `lastName` LIKE '".$letter."' AND (`status`='A') GROUP BY `lastName`");
      }
      if (count($data) == 0)
      {
        return NULL;
      }
      else
      {
        return $data;
      }
    }
  }

  public static function getFirstNames($lastname, $all = false, $user = null)
  {
    $allStatus = (isset($user) && isset($user->rights) && ($user->rights === 'super' || $user->rights === 'admin'))? true: false;
    $database = cbSQLConnect::connect('array');
    if (isset($database))
    {
      if ($all) {
        $lastname = $lastname."%";
      }
      if ($allStatus){
        $data = $database->QuerySingle("SELECT * FROM `person` WHERE `lastName` LIKE '".$lastname."' ORDER BY `firstName`");
      } else if(isset($user) && isset($user->id)) {
        $data = $database->QuerySingle("SELECT * FROM `person` WHERE `lastName` LIKE '".$lastname."' AND (`status`='A' OR `submitter`='".$user->id."') ORDER BY `firstName`");
      } else {
        $data = $database->QuerySingle("SELECT * FROM `person` WHERE `lastName` LIKE '".$lastname."' AND `status`='A' ORDER BY `firstName`");
      }
      if (count($data) == 0)
      {
        return NULL;
      }
      else
      {
        return $data;
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
        $person = $database->getObjectById('person', $id);
        if ($person) {
          $person = recast('Person', $person);
        }
        return $person;
      }
    }
    else
      return NULL;
  }

  public static function getAll($orderBy = 'lastName, firstName', $submissions = false) {
    $database = cbSQLConnect::connect('object');
    if (isset($database)) {
      $orders = array("firstName", "lastName", "middleName", "yearBorn", "yearDead", "lastName, firstName", "firstName, lastName");
      $key = array_search($orderBy, $orders);
      $order = $orders[$key];
      $submissionsKey = $submissions ? 'I' : 'A';
      $sql = "SELECT * FROM `person` WHERE `status`=:submissionsKey ORDER BY $order";
      $params = array(':submissionsKey' => $submissionsKey);
      $results = $database->QueryForObject($sql, $params);
      return !empty($results)? $results : NULL;
    }
    return NULL;
  }

  public static function getChildrenByParents($id, $spouseid) {
    if ($id && $spouseid) {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
        $sql = "SELECT * FROM `parents` WHERE `child` IN (SELECT child FROM `parents` WHERE `parentId`=:id) AND `parentId`=:spouseId";
        $params = array(':id'=>$id, ':spouseId'=>$spouseid);
        $results = $database->QueryForObject($sql, $params);
        return  !empty($results)? $results : false;
      }
    }
  }

  public function appendNames()
  {
    $this->displayableName = $this->displayName();
    $this->succinctName = $this->displayName(true);
    $this->selectableName = $this->selectName();
    $this->typeahead = $this->selectName()." (".$this->yearBorn.")";
  }

  public function displayName($ignoreMiddle = false)
  {
    $name = $this->firstName." ";
    if ($this->middleName && !$ignoreMiddle)
    {
      $name .= $this->middleName." ";
    }
    $name .= $this->lastName;
    return $name;
  }

  public function selectName()
  {
    $name = $this->lastName.", ";
    $name .= $this->firstName;
    if ($this->middleName)
    {
      $name .= " ".$this->middleName;
    }
    return $name;
  }


  public function getParents()
  {

    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $data = $database->QuerySingle("SELECT * FROM `parents` WHERE `child`='".$this->id."' ORDER BY `gender`");
      return $data;
    }

  }
  public static function getParentsById($id = NULL)
  {

    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $data = $database->QuerySingle("SELECT * FROM `parents` WHERE `child`='".$id."' ORDER BY `gender`");
      return $data;
    }

  }

  public function getChildren()
  {

    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $data = $database->QuerySingle("SELECT * FROM `parents` WHERE `parentId`='".$this->id."' ORDER BY `gender`");
      return $data;
    }

  }

  public static function getChildrenById($id = NULL)
  {

    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $data = $database->QuerySingle("SELECT * FROM `parents` WHERE `parentId`='".$id."' ORDER BY `gender`");
      return $data;
    }

  }

  public function getSpouse()
  {

    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $data = $database->QuerySingle("SELECT * FROM `spouse` WHERE `personId`='".$this->id."'");
      return $data;
    }

  }

  public static function getSpouseById($id = NULL)
  {

    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $data = $database->QuerySingle("SELECT * FROM `spouse` WHERE `personId`='".$id."'");
      return $data;
    }

  }

  public function save($user = null)
  {
    // return $this->id;
    if ($user === null){
      $user = User::current_user();
    }
    return isset($this->id) ? $this->update($user) : $this->create($user);
  }

  public function setProfilePic($pic_id)
  {
    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $this->profile_pic = $pic_id;
      return $this->save();
    }
  }

  // create the object if it doesn't already exits.
  protected function create($user)
  {
    if ($user === null){
      $user = User::current_user();
    }
    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $fields = self::$db_fields;
      $data = array();
      $this->submitter = (int)$user->id;
      $this->status = ($user->rights === 'super' || $user->rights === 'admin')? 'A': 'I';
      foreach($fields as $key)
      {
        if ($this->{$key})
        {
          $data[$key] = $this->{$key};
        }
        else
          $data[$key] = NULL;

      }
      // send email to admin here that an individual has been submitted by a non-admin;
      if (!($user->rights === 'super' || $user->rights === 'admin')){
        $message = "A new Individual has been added by a non-admin:<br><br>";
        $message .= "<a href='http://dev.familyhistorydatabase.org/#/individual?individual=".$this->id."&tab=default'>".$this->displayName()."</a><br><br>";
        $message .= "by ".$user->username." ".$user->email;
        $subject = "New Individual for approval";
        sendOwnerUpdate($message, $subject);
      }
      // return true if sucess or false
      $insert = $database->SQLInsert($data, "person");
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
  protected function update($user)
  {
    if ($user === null) {
      $user = User::current_user();
    }
    $database = cbSQLConnect::connect('object');
    if (isset($database))
    {
      $fields = self::$db_fields;
      // $this->submitter = (int)$user->id;
      if (isset($user) && !isset($user->rights) || !($user->rights === 'super' || $user->rights === 'admin')) {
        $this->status = 'I';
        $tempPerson = Person::getById($this->id);
        if ($tempPerson->status === 'A') {
          $message = "An old Individual has been updated and will require aproval to the changes:<br><br>";
          $message .= "<a href='http://dev.familyhistorydatabase.org/#/individual?individual=".$this->id."&tab=default'>".$this->displayName()."</a><br><br>";
          $message .= "by ".$user->username." ".$user->email;
          $message .= "<br><br>Changes Include:<br>";
          $message .= print_r(recursive_array_diff((array)$this, (array)$tempPerson), true);
          $subject = "Old Individual for approval";
          sendOwnerUpdate($message, $subject);
        }
      } else if (is_null($user)) {
        return false;
      }
      foreach($fields as $key) {
        $flag = $database->SQLUpdate("person", $key, $this->{$key}, "id", $this->id);
        if ($flag == "fail")
        {
          break;
        }
      }
      if ($flag == "fail") {
        return 'we failed on check2';
        return false;
      } else {
        return $this->id;
      }
    }
    return 'this happened';
  }

  // update the object if it does already exist.
  public function activate($user)
  {
    $database = cbSQLConnect::connect('object');
    if (isset($database) && $user && ($user->rights === 'super' || $user->rights === 'admin'))
    {
      $fields = self::$db_fields;
      // $this->submitter = (int)$user->id;
      $this->status = 'A';
      foreach($fields as $key)
      {
        $flag = $database->SQLUpdate("person", $key, $this->{$key}, "id", $this->id);
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

  public function deactivate($user)
  {
    $database = cbSQLConnect::connect('object');
    if (isset($database) && $user && ($user->rights === 'super' || $user->rights === 'admin'))
    {
      $fields = self::$db_fields;
      // $this->submitter = (int)$user->id;
      $this->status = 'I';
      foreach($fields as $key)
      {
        $flag = $database->SQLUpdate("person", $key, $this->{$key}, "id", $this->id);
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
    $init = new Person();

    $init->id          = NULL;
    $init->submitter   = NULL;
    $init->status      = 'I';
    $init->firstName   = $data['fninput'];
    $init->middleName  = $data['mninput'];
    $init->lastName    = $data['lninput'];
    if ($data['birth_date'])
    {
      $date = $data['birth_date'];
      $date = explode("/", $date);
      $init->yearBorn = $date[2];
      if (isset($data['birth_date_overide']))
      {
        $init->yearB = false;
      }
      else
      {
        $init->yearB = true;
      }
    }
    if ($data['death_date'] )
    {
      $date = $data['death_date'];
      $date = explode("/", $date);
      $init->yearDead = $date[2];
      if (isset($data['death_date_overide']))
      {
        $init->yearD = true;
      }
      else
      {
        $init->yearD = false;
      }
    }
    $init->sex = $data['sex'];
    $init->relationship = $data['relationship_to_michele'];

    return $init;
  }


}


?>
