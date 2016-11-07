<?php
require_once("includes/initialize.php"); // Include all Necissary Files

if (!isset($session))
{
   $session = mySession::getInstance();
}
if ($session->isLoggedIn())
{
   $user = User::current_user();
   $user = recast("User", $user);
   if (!($user->rights == "admin" || $user->rights == "super"))
   {
      redirect_home();
      exit;
   }
}
else
{
   redirect_home();
   exit;
}

if(isset($_REQUEST['action']))
{
   $action = $_REQUEST['action'];
}
if (isset($action))
{
   //when we send the action to individuals we want the form for editiing individuals
   if ($action == 'individuals')
   {
      $session->save("content", "people/form.php");
      include_page('home.php');
      exit;
   }
   //when we send the action to individuals we want the form for uploading files
   if ($action == 'upload')
   {
      $session->save("content", "upload/upload.php");
      include_page('home.php');
      exit;
   }   
   //when we send the action to individuals we want the form for editiing files
   if ($action == 'editFiles')
   {
      $session->save("content", "upload/editupload.php");
      include_page('home.php');
      exit;
   }   
   //when we send the action to individuals we want the form for editiing files
   if ($action == 'editFile')
   {
      $session->save("fileID", getRequest("id"));
      $session->save("content", "upload/fileform.php");
      include_page('home.php');
      exit;
   }   //when we send the action to individuals we want the form for editiing files
   if ($action == 'getFilteredData')
   {
      $key = getRequest("key");
      $result = retrieveFilteredData($key);
      echo json_encode($result);
      exit;
   }
   if ($action == 'users')
   {
      $session->save("content", "user/users.php");
      include_page('home.php');
      exit;
   }
   if ($action == 'deleteTag')
   {
      $id = getRequest('id');
      if ($id)
      {
         echo json_encode(File::deleteTagById($id));         
      }
      exit;
   }

   if ($action == 'addTag')
   {
      $pid = getRequest('pid');
      $fid = getRequest('fid');
      $type = getRequest('type');
      if ($pid && $fid)
      {
         echo json_encode(File::addTag($pid, $fid, $type));         
      }
      exit;
   }

   if ($action == 'updateFile')
   {
      $file = getRequest('file');
      $file = json_decode($file);
      $file = recast("File", $file);
      $result = $file->save();
      echo json_encode($result);         
      exit;
   }


   if ($action == 'deleteIndividual')
   {
      $response = array();
      // $response[] = "we're here";
      $response = array();
      $check = (isset($_REQUEST['check']))? $_REQUEST['check'] : false;
      if ($check == 'New Individual')
      {
         $msg = "Save Individual's data first then try again.";
         $response[0] = $msg; 
         $response[1] = false;
         echo json_encode($response);
         exit;
      }
      $line = explode("|| ", $check);
      $temp_id = $line[2];
      $spouse = Spouse::getById($temp_id);
      foreach ($spouse as $key) 
      {
         Place::dropById($key->place);
      }
      Spouse::dropByPerson($temp_id);
      Parents::dropByPerson($temp_id);
      Birth::dropByPerson($temp_id);
      Burial::dropByPerson($temp_id);
      Death::dropByPerson($temp_id);
      Place::dropByPerson($temp_id);
      Person::dropByPerson($temp_id);
      $response[] = "Person was Deleted";
      echo json_encode($response);
      exit;
   }

   if ($action == 'getPlace')
   {
      if (isset($_REQUEST['place']));
      {
         $place = $_REQUEST['place'];
         $set = Place::getByTown($place);
         echo json_encode(array_values(get_object_vars($set)));
         exit;
      }
      exit;
   }

// this will get us the list of individuals
   if ($action == 'getIndividuals')
   {
      $people = Person::getIndividuals();
      if ($people)
      {
         echo json_encode($people);
      }
      else
      {
         echo "failed";
      }
      exit;
   }

   // this will get us the list of individuals
   if ($action == 'getPlaces')
   {

      $database = cbSQLConnect::connect('object');
      $result = array();
      if (isset($database))
      {
         $places = $database->QuerySingle("SELECT DISTINCT `id`, `name`, `ftable`, `fid`, `category` FROM `places`  GROUP BY `name`");
         if ($places)
         {
            foreach($places as $aplace)
            {
               $temp = array();
               $temp[] = $aplace->id;
               $temp[] = $aplace->name;
               $temp[] = $aplace->ftable;
               $temp[] = $aplace->fid;
               $temp[] = $aplace->category;
               $result[] = $temp;
            }
         }
         else
         {
            return "none";
         }   
      }   
      if ($result)
      {
         echo json_encode($result);
      }
      else
      {
         echo "failed";
      }
      exit;
   }

// this will get us the list of individuals
   if ($action == 'adjustParent')
   {
      $response = array();
      $check = (isset($_REQUEST['individual']))? $_REQUEST['individual'] : false;
      if ($check == "")
      {
         $response[0] = "There were no individuals selected.  Select someone and retry the submission.";
         $response[1] = false;
         echo json_encode($response);
         exit;
      }
      if ($check == 'New Individual')
      {

         $msg = "Save child's data first then try again.";
         $response[0] = $msg; 
         $response[1] = false;
         echo json_encode($response);
         exit;
      }
      $line = explode("|| ", $check);
      $temp_id = $line[2];
      if ($temp_id)
      {
         if ($child = Person::getById($temp_id))
         {
            $line = explode("|| ", $_REQUEST['parentId']);
            $parentId = $line[2];
            $temp = Person::getById($parentId);
            $parent = Parents::createInstance($temp->id, $temp->sex, $child->id);
            $do = (isset($_REQUEST['do']))? $_REQUEST['do'] : false;
            $temp_id = $parent->save();
            if ($temp->id)
            {
               if ($result = ($do == 'deleteParent')? $parent->dropById($parent->id) : true)
               {
                  $response[0] = ($do == 'saveParent')? "Parents were saved" : "Parent was removed";
                  $response[1] = true;
               }
               else
               {
                  $response[0] = ($do == 'saveParent')? "Parents were not saved" : "Parent was not removed"; 
                  $response[1] = false;
               }
            }
            else
            {
               $response[0] = "Request not completed. Action was unclear";
               $response[1] = false;
            }
         } 
      }
      echo json_encode($response);
      exit;
   }

// this will get us the list of individuals
   if ($action == 'adjustSpouse')
   {
      $response = array();
      $check = (isset($_REQUEST['individual']))? $_REQUEST['individual'] : false;
      if ($check == "")
      {
         $response[0] = "There were no individuals selected.  Select someone and retry the submission.";
         $response[1] = false;
         echo json_encode($response);
         exit;
      }
      if ($check == 'New Individual')
      {
         $msg = "Save Individual's data first then try again.";
         $response[0] = $msg; 
         $response[1] = false;
         echo json_encode($response);
         exit;
      }
      $line = explode("|| ", $check);
      $temp_id = $line[2];
      if ($temp_id)
      {
         if ($individual = Person::getById($temp_id))
         {
            $line = explode("|| ", $_REQUEST['spouseId']);
            $spouseId = $line[2];

            $temp = Person::getById($spouseId);

            $spouse = Spouse::createInstance($temp_id, $spouseId, $_REQUEST['date'], $_REQUEST['date_overide']);
            $spouse2 = Spouse::createInstance($spouseId, $temp_id, $_REQUEST['date'], $_REQUEST['date_overide']);
            $sid = $spouse->save();
            $sid2 = $spouse2->save();
            $do = (isset($_REQUEST['do']))? $_REQUEST['do'] : false;

            $spouse->id = $sid;
            $spouse2->id = $sid2;
            $data = array();
            $data['town']    =   (isset($_REQUEST['town']))? $_REQUEST['town'] : NULL;
            $data['county']  =   (isset($_REQUEST['county']))? $_REQUEST['county'] : NULL;
            $data['state']   =   (isset($_REQUEST['state']))? $_REQUEST['state'] : NULL;
            $data['country'] =   (isset($_REQUEST['country']))? $_REQUEST['country'] : NULL;
            $data['cemetary']=   (isset($_REQUEST['cemetary']))? $_REQUEST['cemetary'] : NULL;
            $data['table']   =   'spouse';
            $data['key']     =   $sid;

            $data2['town']    =   (isset($_REQUEST['town']))? $_REQUEST['town'] : NULL;
            $data2['county']  =   (isset($_REQUEST['county']))? $_REQUEST['county'] : NULL;
            $data2['state']   =   (isset($_REQUEST['state']))? $_REQUEST['state'] : NULL;
            $data2['country'] =   (isset($_REQUEST['country']))? $_REQUEST['country'] : NULL;
            $data2['cemetary']=   (isset($_REQUEST['cemetary']))? $_REQUEST['cemetary'] : NULL;
            $data2['table']   =   'spouse';
            $data2['key']     =   $sid2;

            $object_id = Place::getSomething('id', $data['table'], $data['key']);
            $object = Place::createInstance($data);
            $object->id = $object_id;
            $spouse->place = $object->save();
            
            $object_id2 = Place::getSomething('id', $data2['table'], $data2['key']);
            $object2 = Place::createInstance($data2);
            $object2->id = $object_id2;
            $spouse2->place = $object2->save();
            
            $flag = $spouse->save();
            $flag2 = $spouse2->save();

            if ($flag && $flag2)
            {
               $result1 = ($do == 'deleteSpouse')? Place::dropById($spouse->place) : true;
               $result2 = ($do == 'deleteSpouse')? Place::dropById($spouse2->place) : true;
               if ($result1 && $result2)
               {
                  $result1 = ($do == 'deleteSpouse')? $spouse->dropById($spouse->id) : true;
                  $result2 = ($do == 'deleteSpouse')? $spouse2->dropById($spouse2->id) : true;
                  if ($result1 && $result2)
                  {

                     $response[0] = ($do == 'saveSpouse')? "Spouses were saved" : "Spouse was removed";
                     $response[1] = true;
                  }
               }
               else
               { 

                  $response[0] = ($do == 'saveSpouse')? "Spouses were not saved" : "Spouse was not removed"; 
                  $response[1] = false;
               }
            }
            else
            {
               $response[0] = "Request not completed. Action was unclear";
               $response[1] = false;
            }
         } 
      }
      echo json_encode($response);
      exit;
   }

//this will get us the individual's data
   if ($action == 'getIndData')
   {   
      $check = $_REQUEST['check'];
      if ($check != 'New Individual')
      {
         $line = explode("|| ", $check);
         $id = $line[2];
         $result = array();
         $result[] = array_values(get_object_vars($person = Person::getById($id))); 
         $result[] = array_values(get_object_vars($birth = Birth::getById($id)));
         $result[] = array_values(get_object_vars($death = Death::getById($id)));
         $result[] = array_values(get_object_vars($burial = Burial::getById($id)));
         $result[] = array_values(get_object_vars(Place::getById($birth->place)));
         $result[] = array_values(get_object_vars(Place::getById($death->place))); 
         $result[] = array_values(get_object_vars(Place::getById($burial->place)));
         $parents = array();
         $parent = Parents::getAllParentsById($id); 
         foreach ($parent as $pops) 
         {
            $temp = array();
            $temp[0] = 'parent';
            $temp_person = Person::getById($pops->parentId);
            $temp[1] = "".$temp_person->lastName.", ".$temp_person->firstName." ".$temp_person->middleName." || Born: ".$temp_person->yearBorn.", Death: ".$temp_person->yearDead." || ".$temp_person->id;
            $parents[] = $temp;
         }
         $result[] = $parents;
         $spouses = array();
         $spouse = Spouse::getAllSpousesById($id); 
         foreach ($spouse as $spo) 
         {
            $temp = array();
            $temp[0] = 'spouse';
            $temp_spouse = Person::getById($spo->spouse);
            $temp[1] = "".$temp_spouse->lastName.", ".$temp_spouse->firstName." ".$temp_spouse->middleName." || Born: ".$temp_spouse->yearBorn.", Death: ".$temp_spouse->yearDead." || ".$temp_spouse->id;
            $temp[2] = $spo->day."/".$spo->month."/".$spo->year;
            $temp[3] = $spo->yearM;
            $temp[4] = array_values(get_object_vars(Place::getById($spo->place)));
            $spouses[] = $temp;
         }
         $result[] = $spouses;
         echo json_encode($result);

      }
      exit;
   }

//this will get us the individual's data
   if ($action == 'getFileData')
   {   
      $id = getRequest('id');
      if ($id)
      {
         $result['file'] = $file = File::getById($id); 
         $result['tags'] = $tags = File::getTagsById($id);
         echo json_encode($result);
      }
      exit;
   }

//this will make the changes for us 
   if ($action == 'makeChanges')
   {
      if(isset($_REQUEST['formtype']))
      {
         $formtype = $_REQUEST['formtype'];
      }
      if (isset($formtype))
      {
         if ($formtype == 'individual')
         {
            $check = $_REQUEST['individual'];
            $temp = Person::createInstance($_REQUEST);
            if ($check == "")
            {
               $response[0] = "There were no individuals selected.  Select someone and retry the submission.";
               $response[1] = false;
               echo json_encode($response);
               exit;
            }
            else if ($check == 'New Individual')
            {
               $person = $temp->save();
            }
            else
            {
               $line = explode("|| ", $check);
               $temp->id = $line[2];
               $person = $temp->save(); 
            }
            $data = array();

            if ($person)
            {
               $birth = getInfo($_REQUEST, $person, 'Birth', $check);
               $death = getInfo($_REQUEST, $person, 'Death', $check);
               $burial = getInfo($_REQUEST, $person, 'Burial', $check);
               if (isset($birth->id) && isset($death->id) && isset($burial->id))
               {


                  $data['town']    =   (isset($_REQUEST['bp_town']))? $_REQUEST['bp_town'] : NULL;
                  $data['county']  =   (isset($_REQUEST['bp_county']))? $_REQUEST['bp_county'] : NULL;
                  $data['state']   =   (isset($_REQUEST['bp_state']))? $_REQUEST['bp_state'] : NULL;
                  $data['country'] =   (isset($_REQUEST['bp_country']))? $_REQUEST['bp_country'] : NULL;
                  $data['cemetary']=   (isset($_REQUEST['bp_cemetary']))? $_REQUEST['bp_cemetary'] : NULL;
                  $data['table']   =   'birth';
                  $data['key']     =   $birth->id;
                  $bplace = getPlaceInfo($data, 'birth', $birth->id, $check);
                  $bplace->save();
                  $birth->place = $bplace->id;
                  $flag = $birth->save();
                  if ($flag)
                  {

                     $data['town']    =   (isset($_REQUEST['dp_town']))? $_REQUEST['dp_town'] : NULL;
                     $data['county']  =   (isset($_REQUEST['dp_county']))? $_REQUEST['dp_county'] : NULL;
                     $data['state']   =   (isset($_REQUEST['dp_state']))? $_REQUEST['dp_state'] : NULL;
                     $data['country'] =   (isset($_REQUEST['dp_country']))? $_REQUEST['dp_country'] : NULL;
                     $data['cemetary']=   (isset($_REQUEST['dp_cemetary']))? $_REQUEST['dp_cemetary'] : NULL;
                     $data['table']   =   'death';
                     $data['key']     =   $death->id;
                     $dplace = getPlaceInfo($data, 'death', $death->id, $check);
                     $dplace->save();
                     $death->place = $dplace->id;
                     $flag = $death->save();
                     if ($flag)
                     {

                        $data['town']    =   (isset($_REQUEST['bup_town']))? $_REQUEST['bup_town'] : NULL;
                        $data['county']  =   (isset($_REQUEST['bup_county']))? $_REQUEST['bup_county'] : NULL;
                        $data['state']   =   (isset($_REQUEST['bup_state']))? $_REQUEST['bup_state'] : NULL;
                        $data['country'] =   (isset($_REQUEST['bup_country']))? $_REQUEST['bup_country'] : NULL;
                        $data['cemetary']=   (isset($_REQUEST['bup_cemetary']))? $_REQUEST['bup_cemetary'] : NULL;
                        $data['table']   =   'burial';
                        $data['key']     =   $burial->id;
                        $buplace = getPlaceInfo($data, 'burial', $burial->id, $check);
                        $buplace->save();
                        $burial->place = $buplace->id;
                        $flag = $burial->save();
                        if ($flag)
                        {
                           echo "All Data Was Saved";
                           return $person;
                           exit;
                        }
                     }
                  }
               }
               else
               {
                  echo "Data but NOT Places were saved";
                  exit;
               }
            }
         }
      }
      echo "fail";
      exit;
   }
}

function retrieveFilteredData($key)
{
   return $key;
}

function getInfo($data, $person, $class, $test)
{
   $object_id = $class::getSomething('id', $person);
   $object = $class::createInstance($data, $person);
   if ($test == "New Individual")
   {
      $temp_id = $object->save();
      $object->id = $temp_id;
   }
   else
   {
      $object->id = $object_id;
   }
   return $object;
}
function getPlaceInfo($data, $ft_name, $key, $test)
{
   $object_id = Place::getSomething('id', $ft_name, $key);
   $object = Place::createInstance($data);
   if ($test == "New Individual")
   {
      $temp_id = $object->save();
      $object->id = $temp_id;
   }
   else
   {
      $object->id = $object_id;
   }
   return $object;
}

if ($session->isLoggedIn())
{
   $user = User::current_user();
   $user = recast('User', $user);
   if ($user->rights == "super")
   {
      $session->save("content", "admin/admin.php");
      include_page('home.php');
   }
   else
   {
      $session->save("content", "home.php");
      include_page('home.php');
   }
}
else
{
   $session->save("content", "home.php");
   include_page('home.php');
}

exit;

?>