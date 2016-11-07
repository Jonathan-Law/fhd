<?php
require_once("includes/initialize.php"); // Include all Necissary Files

if (!isset($session))
{
   $session = mySession::getInstance();
}

$action = getRequest('action');

if (isset($action) && $action != null)
{
   if ($action == 'num_pic')
   {
      $result = Person::getNumPics();
      if (!empty($result[1]))
      {
         echo json_encode($result);
      }
      else
      {
         echo json_encode("failure");
      }
      exit;
   }
   if ($action == 'homepage')
   {
      $id = getRequest("id");
      $session->save("indvidual_id", $id);
      $session->save("content", "people/individual/individual.php");
      include_page('home.php');
      exit;
   }
   if ($action == 'photo_album')
   {
      $id = getRequest("id");
      $session->save("indvidual_id", $id);
      $session->save("content", "people/photo/photo_album.php");
      include_page('home.php');
      exit;
   }
   if ($action == 'documents')
   {
      $id = getRequest("id");
      $session->save("indvidual_id", $id);
      $session->save("content", "people/documents/document.php");
      include_page('home.php');
      exit;
   }
   if ($action == 'family')
   {
      $id = getRequest("id");
      if ($id)
      {
         $session->save("indvidual_id", $id);
         $session->save("content", "people/family/familychart.php");
         include_page('home.php');
      }
      exit;
   }
   if ($action == 'add_to_favorites')
   {
      $user = User::current_user();
      $user = recast('User', $user);
      $id = getRequest('id');

      $check = Favorites::getFavorites($user->id, $id);
      if (!$check)
      {
         $temp = new Favorites();
         $temp->id = null;
         $temp->user_id = $user->id;
         $temp->person_id = $id;
         $result = $temp->save();
         if ($result)
         {
            echo "Success";
            exit;
         }
         else
         {
            echo "Failure";
            exit;
         }
      }
      else
      {
         echo "That person was already favorited";
      }
      exit;
   }
   if ($action == 'remove_from_favorites')
   {
      $user = User::current_user();
      $user = recast('User', $user);
      $id = getRequest('id');

      $check = Favorites::checkFavoriteById($id, $user->id);
      if ($check)
      {
         // error_reporting(E_ALL);
         // ini_set('display_errors', '1');
         $check = recast("Favorites", $check[0]);
         $result = $check->delete();
         if ($result)
         {
            $leftovers = Favorites::getFavoritesByUser($user->id);
            $data = array();
            $data[0] = "success";
            $data[1] = array();
            $count = 0;
            foreach($leftovers as $person)
            {
               $temp = Person::getById($person->person_id); 
               $temp = recast("Person", $temp);
               $data[1][$count] = $temp;
               $data[1][$count]->displayName = $temp->displayName();
               $count++;
            }
            echo json_encode($data);
            exit;
         }
         else
         {
            echo "Failure";
            exit;
         }
      }
      echo "Failure";
      exit;
   }
   if ($action == 'getProfilePic')
   {
      $id = getRequest("id");
      if ($id)
      {
         $person = Person::getById($id);
         if ($person->profile_pic)
         {
            $response = File::getById($person->profile_pic);

            if ($response)
            {
               echo json_encode($response);
            }
            else
               echo json_encode(false);
         }
         else
            echo json_encode(false);         
         exit;
      }
      else
         echo json_encode(false);
      exit;
   }
   if ($action == 'setProfilePic')
   {
      $person_id = getRequest("person_id");
      $id = getRequest('pic_id');
      $person = Person::getById($person_id);
      $person = recast("Person", $person);

      if ($person->setProfilePic($id))
      {
         echo "The new profile picture for ";
         echo $person->firstName." ";
         if ($person->middleName)
         {
            echo $person->middleName." ";
         }
         echo $person->lastName;
         echo " was saved!";
      }
      else
      {
         echo "Failure";
      }
      exit;
   }
   if ($action == 'getFamily')
   {

      $data = array();
      $id = getRequest("id");
      if ($id)
      {
         $data["response"] = array();
         $data["response"]["individual"]           = Person::getById($id);
         $data["response"]["children"]             = array();
         $data["response"]["spouse"]               = array();
         //parents
         $data["response"]["parents"]              = array();
         $data["response"]["parents"]["father"]    = array();
         $data["response"]["parents"]["mother"]    = array();
         
         //Grand parents
         $data["response"]["fparents"]             = array();
         $data["response"]["fparents"]["father"]   = array();
         $data["response"]["fparents"]["mother"]   = array();
         $data["response"]["mparents"]             = array();
         $data["response"]["mparents"]["father"]   = array();
         $data["response"]["mparents"]["mother"]   = array();

         //Great Grand parents
         $data["response"]["ffparents"]             = array();
         $data["response"]["ffparents"]["father"]   = array();
         $data["response"]["ffparents"]["mother"]   = array();
         $data["response"]["mfparents"]             = array();
         $data["response"]["mfparents"]["father"]   = array();
         $data["response"]["mfparents"]["mother"]   = array();
         $data["response"]["mmparents"]             = array();
         $data["response"]["mmparents"]["father"]   = array();
         $data["response"]["mmparents"]["mother"]   = array();
         $data["response"]["fmparents"]             = array();
         $data["response"]["fmparents"]["father"]   = array();
         $data["response"]["fmparents"]["mother"]   = array();
         
         $parents  = Person::getParentsById($id);
         $children = Person::getChildrenById($id);
         $spouse   = Person::getSpouseById($id);
         foreach ($parents as $key ) 
         {
            $temp_person = Person::getById($key->parentId);
            if ($temp_person->sex == 'male')
            {
               $data["response"]["parents"]["father"][] = $temp_person;
            }
            else
            {
               $data["response"]["parents"]["mother"][] = $temp_person;
            }
         }
         foreach ($children as $key ) 
         {
            $data["response"]["children"][] = Person::getById($key->child);
         }
         foreach ($spouse as $key ) 
         {
            $data["response"]["spouse"][] = Person::getById($key->spouse);
         }
         
         $father = Person::getParentsById($data["response"]["parents"]["father"][0]->id);
         $mother = Person::getParentsById($data["response"]["parents"]["mother"][0]->id);
         foreach ($father as $key) 
         {
            $temp_person = Person::getById($key->parentId);
            if ($temp_person->sex == 'male')
            {
               $data["response"]["fparents"]["father"][] = $temp_person;
            }
            else
            {
               $data["response"]["fparents"]["mother"][] = $temp_person;
            }
         }
         foreach ($mother as $key) 
         {
            $temp_person = Person::getById($key->parentId);
            if ($temp_person->sex == 'male')
            {
               $data["response"]["mparents"]["father"][] = $temp_person;
            }
            else
            {
               $data["response"]["mparents"]["mother"][] = $temp_person;
            }
         }
         $ffather = Person::getParentsById($data["response"]["fparents"]["father"][0]->id);
         $fmother = Person::getParentsById($data["response"]["fparents"]["mother"][0]->id);
         $mfather = Person::getParentsById($data["response"]["mparents"]["father"][0]->id);
         $mmother = Person::getParentsById($data["response"]["mparents"]["mother"][0]->id);
         foreach ($mfather as $key) 
         {
            $temp_person = Person::getById($key->parentId);
            if ($temp_person->sex == 'male')
            {
               $data["response"]["mfparents"]["father"][] = $temp_person;
            }
            else
            {
               $data["response"]["mfparents"]["mother"][] = $temp_person;
            }
         }
         foreach ($mmother as $key) 
         {
            $temp_person = Person::getById($key->parentId);
            if ($temp_person->sex == 'male')
            {
               $data["response"]["mmparents"]["father"][] = $temp_person;
            }
            else
            {
               $data["response"]["mmparents"]["mother"][] = $temp_person;
            }
         }
         foreach ($ffather as $key) 
         {
            $temp_person = Person::getById($key->parentId);
            if ($temp_person->sex == 'male')
            {
               $data["response"]["ffparents"]["father"][] = $temp_person;
            }
            else
            {
               $data["response"]["ffparents"]["mother"][] = $temp_person;
            }
         }
         foreach ($fmother as $key) 
         {
            $temp_person = Person::getById($key->parentId);
            if ($temp_person->sex == 'male')
            {
               $data["response"]["fmparents"]["father"][] = $temp_person;
            }
            else
            {
               $data["response"]["fmparents"]["mother"][] = $temp_person;
            }
         }
      }
      else
         $data["response"] = "There was no id";
      echo json_encode($data);
      exit;
   }
}
?>