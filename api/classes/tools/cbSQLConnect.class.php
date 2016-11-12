<?php
/*
This file is part of cbSQLConnect.

cbSQLConnect is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

cbSQLConnect is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with cbSQLConnect.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
Cyber's SQL Connect using PDO Version 0.9.1

Changelog:
Version 0.9.1
- All entire class was wrote using PDO class
- CRUD 100%   can return as class or array assoc
- 2 types of query to search, normal  or using query param
- supports MySQL, Postgre
- connect using ODBC Driver, DSN

Version 0.8
- CRUD 100%
- Supoorts array assoc when insert or array_number
- Error Message can be costumized to your language
- MySQL Only

Version 0.7
- CRUD 100%
- customize error messages
- All class are static for mobility
- MySQL Only

Version 0.3
- 100% CRUD only
- customize error messages
- MySQL Only

*/



require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");

class cbSQLConnect {

public $LastQuery; // record last query (temporary)

private $db; // stores  setup class and all database related like tablenames, database name and connection string
private $data; // no use when i updated  query run mode

private $Rows; // store number of rows affected
private $FetchType; // stores type of fetch result of querys

private $PDOInstance; // class global PDO Instance

public function __construct(cbSQLConnectConfig &$setup, $mode = cbSQLConnectVar::FETCH_ASSOC){
  $this->db = &$setup;
  $this->FetchType = $mode;
  $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_FUNC);
  // $this->PDOInstance = cbSQLConnectConfig::CreatePDOInstance();
}

public function QuerySingle($query, $class = 'stdClass'){
  $instance = cbSQLConnectConfig::CreatePDOInstance();
  $stmt = $instance->prepare($query);

  switch( $this->FetchType){

    case cbSQLConnectVar::FETCH_ASSOC:
    $tp = $this->RunQuery($stmt);
    $this->LastQuery = $query;
    break;

    case cbSQLConnectVar::FETCH_LAZY:
    $this->LastQuery = $query;
    $tp = $this->RunQueryAsLazy();
    break;

    case cbSQLConnectVar::FETCH_OBJECT:
    $this->LastQuery = $query;
    $tp = $this->RunQueryAsObject($class);
    break;

    default:
    $this->LastQuery = $query;
    $stmt = $this->db->prepare($query);
    $tp = $this->RunQuery($stmt);
    break;

  }
  $instance = null;
  return $tp;
}

public function getObjectById($table, $id)
{
  $sql = "SELECT * FROM $table WHERE `id`= :id LIMIT 1";
  $params = array(':id' => $id);
  array_unshift($params, '');
  unset($params[0]);
  $results_array = $this->QueryForObject($sql, $params);
  return !empty($results_array) ? array_shift($results_array) : false;
}

public function getOtherObjectById($table, $id)
{
  $sql = "SELECT * FROM $table WHERE `personId`= :id LIMIT 1";
  $params = array(':id' => $id);
  array_unshift($params, '');
  unset($params[0]);
  $results_array = $this->QueryForObject($sql, $params);
  return !empty($results_array) ? array_shift($results_array) : false;
}

public function QueryForObject($query, $binds){
  $stmt = $this->db->prepare($query);
  foreach($binds as $bindKey => &$key){
    $stmt->bindParam($bindKey, $key, PDO::PARAM_STR);
  }
  return $this->RunObjectQuery($stmt, NULL , "stdClass");
}

private function RunObjectQuery(PDOStatement $state, $param  = NULL, $class = 'stdClass'){
  try{
    if(empty($param) || $param == NULL){
      $state->execute();
      $this->data = $state->fetchAll(PDO::FETCH_CLASS, $class);
      $this->Rows = $state->rowCount();
      $state->closeCursor();
      return $this->data;
    }else {
      $state->execute($param);
      $this->data = $state->fetchAll(PDO::FETCH_CLASS, $class);
      $this->Rows = $state->rowCount();
      $state->closeCursor();
      return $this->data;
    }
  }catch(PDOException $ex){
    throw new PDOException($ex->getMessage());
  }
}

public function Query($query, $binds){

  $stmt = $this->db->prepare($query);

  foreach($binds as $bindKey => &$key){

    $stmt->bindParam($bindKey, $key, PDO::PARAM_STR);
  }

  return $this->RunQuery($stmt);
}

public function prepareQuote($string = null){
  return $this->db->quote($string);
}

private function RunQuery(PDOStatement $state, $param  = NULL){

  try{
    if(empty($param) || $param == NULL){

      $state->execute();
      $this->data =  $state->fetchAll(PDO::FETCH_ASSOC);
      $this->Rows = $state->rowCount();
      $state->closeCursor();
      return $this->data;
    }else {

      $state->execute($param);
      $this->data =  $state->fetchAll(PDO::FETCH_ASSOC);
      $this->Rows = $state->rowCount();
      $state->closeCursor();
      return $this->data;


    }
  }catch(PDOException $ex){
    throw new PDOException($ex->getMessage());
  }
}



private function RunQueryAsObject($class){

  $stmt = $this->db->query($this->LastQuery);
  $result = $stmt->fetchAll(PDO::FETCH_CLASS, $class);

  return $result;
}

private function RunQueryAsLazy(){

  $stmt = $this->db->query($this->LastQuery);
  $result = $stmt->fetchAll(PDO::FETCH_BOTH);

  return $result;

}


private function implode2($glue1, $glue2, $array)
{
  return ((sizeof($array) > 2)? implode($glue1, array_slice($array, 0, -2)).$glue1 : "").implode($glue2, array_slice($array, -2));
}


private function addQuotes($word, $doublequotes = true){

  if($doublequotes){

    $pword = '"'.$word.'"';
  }else{
    $pword =  "'".$pword."'";
  }

  return $pword;

}




public function SQLInsert($array, $table){
  $inst = cbSQLConnectConfig::CreatePDOInstance();

  $fieldname = array_keys($array);

  $table = sprintf("INSERT INTO %s", $table);
  $fields  = '('.implode(",",$fieldname).')';
  $placeholder = 'VALUES(:'.implode(", :", $fieldname).')';


  $query = sprintf("%s %s %s", $table, $fields, $placeholder);

  $stmt = $inst->prepare($query);

  foreach($array  as $param => $key){
    $stmt->bindValue(':'.$param,  $array[$param]);
  }

  $stmt->execute();

  if($stmt->rowCount() != 0){
    $stmt->closeCursor();
    $insertedID = $inst->lastInsertId();
    $inst = null;
    return $insertedID;
  }else {
    $inst = null;
    $stmt->closeCursor();
    return false;
  }

}


public function SQLUpdate($table, $fieldToChange, $fieldValue, $idField, $idValue){
  $query = sprintf("UPDATE %s SET %s = :value WHERE %s = :comparevalue", $table, $fieldToChange, $idField);
  $stmt = $this->db->prepare($query);

  if(!$stmt){
    print_r( $stmt->errorInfo());
    return 'fail';
  }
  $stmt->bindParam(":value",  $fieldValue, PDO::PARAM_INT | PDO::PARAM_STR);
  $stmt->bindParam(":comparevalue",$idValue, PDO::PARAM_INT | PDO::PARAM_STR);

  $stmt->execute();

  if($stmt->rowCount() != 0){
    $stmt->closeCursor();
    return 'changed';
  }

  $stmt->closeCursor();
  return 'unchanged';
}


public function SQLDelete($table, $idTable, $idValue){

  $inst = cbSQLConnectConfig::CreatePDOInstance();

  $query = sprintf("DELETE FROM %s WHERE %s = :value", $table, $idTable);

  $stmt = $inst->prepare($query);

  $stmt->bindParam(":value", $idValue);
  $result = $stmt->execute();
  $stmt->closeCursor();
  $inst = null;
  return $result;
}

public function RowsAffected(){
  return $this->Rows;
}

public static function connect($kind)
{

$databaseSetup = new cbSQLConnectConfig( cbSQLConnectVar::DB_MYSQL, DB_SERVER, "3306", DB_NAME, DB_USER, DB_USR_PASS);  // Setup Class
if (!empty($databaseSetup))
{
  switch($kind)
  {
    case "array":
    $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_ASSOC);
    break;

    case "object":
    $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_OBJECT);
    break;

    case "both":
    $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_LAZY);
    break;

    default:
    $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_ASSOC);
    break;
  }
  return $database;
}
else
  return false;
}


public static function adminConnect($kind)
{

  $databaseSetup = new cbSQLConnectConfig( cbSQLConnectVar::DB_MYSQL, DB_SERVER, "3306", DB_NAME, DB_ADMIN, DB_ADMIN_PASS);
  if (!empty($databaseSetup))
  {
    switch($kind)
    {
      case "array":
      $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_ASSOC);
      break;

      case "object":
      $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_OBJECT);
      break;

      case "both":
      $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_LAZY);
      break;

      default:
      $database = new cbSQLConnect($databaseSetup, cbSQLConnectVar::FETCH_ASSOC);
      break;
    }
    return $database;
  }
  else
    return false;
}

}






?>
