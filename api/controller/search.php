<?php
require_once("includes/initialize.php"); // MAke sure all necessary files are included

if (!isset($session))
{
   $session = mySession::getInstance();
}

if ($session->isLoggedIn())
{
   $user = User::current_user();
   $user = recast("User", $user);
}
$action = getRequest("action");

if ($action == "main-search")
{
   $session->save("content", "results.php");
   $search = getRequest("search");
   $session->save("search", $search);
   include_page("home.php");
   exit;
}
if ($action == "showMainSearch")
{
   $session->save("content", "search.php");
   include_page("home.php");
   exit;
}
if ($action == "getResults")
{
   // error_reporting(E_ALL);
   // ini_set('display_errors', '1');

   $database = cbSQLConnect::connect('array');
   $search_query = getRequest("search");
   if ($session->get("recentSearch"))
   {
      $oldsearch = $session->get("recentSearch");
   }
   if ($oldsearch != $search_query && $search_query != '')
   {
      $session->save("recentSearch", $search_query);
   }
   else
   {
      $search_query = $oldsearch;
   }
   $search_result_people = array();
   $search_result_file = array();
   $search_result_other = array();

   $results = array();


   if (isset($database) && !empty($search_query))
   {
      $data = $database->QuerySingle("SELECT *, MATCH(title, author, comments) AGAINST('".$search_query."*' IN BOOLEAN MODE) AS score FROM `file` WHERE MATCH(title, author, comments) AGAINST('".$search_query."*' IN BOOLEAN MODE) ORDER BY score DESC");
      //$results[] = $data;
      if (count($data) == 0) 
      {
         //echo "Search found nothing in File\n";
      }
      else
      {
         foreach($data as $instance)
         {
            $temp = recast("File", arrayToObject($instance));
            $search_result_file[] = $temp;
         }
      }
      unset($data);
      $data = $database->QuerySingle("SELECT *, MATCH(firstName, middleName, lastName) AGAINST('".$search_query."*' IN BOOLEAN MODE) AS score FROM `person` WHERE MATCH(firstName, middleName, lastName) AGAINST('".$search_query."*' IN BOOLEAN MODE) ORDER BY score DESC");
      //$results[] = $data;
      if (count($data) == 0) 
      {
         //echo "Search found nothing in Person\n";
      }
      else
      {
         foreach($data as $instance)
         {
            $temp = recast("Person", arrayToObject($instance));
            $parents = $temp->getParents();
            $temp->parents = array();
            $temp->profilePic = File::getById($temp->profile_pic);
            foreach($parents as $parent)
            {
               $tempParent = Person::getById($parent->parentId);
               $tempParent = recast("Person", $tempParent);
               $tempParent->profilePic = File::getById($tempParent->profile_pic);
               $tempParent->dName = $tempParent->displayName();
               $temp->parents[] = $tempParent;
            }
            $temp->dName = $temp->displayName();
            $search_result_people[] = $temp;
         }
      }
      unset($data);
      $data = $database->QuerySingle("SELECT *, MATCH(town, county, state, country, cemetary) AGAINST('".$search_query."*' IN BOOLEAN MODE) AS score FROM `place` WHERE MATCH(town, county, state, country, cemetary) AGAINST('".$search_query."*' IN BOOLEAN MODE) ORDER BY score DESC");
      //$results[] = $data;
      if (count($data) == 0) 
      {
         //echo "Search found nothing in Place\n";
      }
      else
      {
         foreach($data as $instance)
         {
            $temp = recast("Place", arrayToObject($instance));
            $search_result_other[] = $temp;
         }
      }
      unset($data);
      $data = $database->QuerySingle("SELECT *, MATCH(name) AGAINST('".$search_query."*' IN BOOLEAN MODE) AS score FROM `places` WHERE MATCH(name) AGAINST('".$search_query."*' IN BOOLEAN MODE) ORDER BY score DESC");
      //$results[] = $data;
      if (count($data) == 0) 
      {
         //echo "Search found nothing in Place Tags\n";
      }
      else
      {
         foreach($data as $instance)
         {
            $temp = arrayToObject($instance);
            $search_result_other[] = $temp;
         }
      }
      unset($data);
      $data = $database->QuerySingle("SELECT *, MATCH(name) AGAINST('".$search_query."*' IN BOOLEAN MODE) AS score FROM `tags` WHERE MATCH(name) AGAINST('".$search_query."*' IN BOOLEAN MODE) ORDER BY score DESC");
      //$results[] = $data;
      if (count($data) == 0) 
      {
         //echo "Search found nothing in Tags\n";
      }
      else
      {
         foreach($data as $instance)
         {
            $temp = arrayToObject($instance);
            $search_result_other[] = $temp;
         }
      }

      // echo json_encode($results);

      usort($search_result_people, function($a, $b)
      {
         return $a->score < $b->score;
      });
      usort($search_result_file, function($a, $b)
      {
         return $a->score < $b->score;
      });
      usort($search_result_other, function($a, $b)
      {
         return $a->score < $b->score;
      });
      $results = array();
      $results[] = $search_result_people;
      $results[] = $search_result_file;
      $results[] = $search_result_other;
      echo json_encode($results);
      // echo "<pre>";
      // foreach ($search_result as $result)
      // {
      //    echo "\n";
      //    $className = get_class($result);
      //    if ($className)
      //    {
      //       echo $className;
      //    }
      //    else
      //    {
      //       echo "STDCLASS OBJ";
      //    }
      // }
      //    echo "\n";
      // print_r($search_result);
      // echo "</pre>";
      // echo count($search_result);

   }
   exit;
}
if ($action = 'getFilteredData')
{
   //    error_reporting(E_ALL);
   // ini_set('display_errors', '1');

   $database = cbSQLConnect::connect('array');
   $search_query = getRequest("key");
   $search_result_data = array();
   $search_result_tags = array();


   $results = array();


   if (isset($database) && !empty($search_query))
   {
      $data = $database->QuerySingle("SELECT *, MATCH(title, author, comments) AGAINST('".$search_query."*' IN BOOLEAN MODE) AS score FROM `file` WHERE MATCH(title, author, comments) AGAINST('".$search_query."*' IN BOOLEAN MODE) ORDER BY score DESC");
      //$results[] = $data;
      if (count($data) == 0) 
      {
         //echo "Search found nothing in File\n";
      }
      else
      {
         foreach($data as $instance)
         {
            $temp = recast("File", arrayToObject($instance));
            if ($temp->comments !== null)
            {
               $results[] = $temp->comments;
            }
            if ($temp->title != null)
            {
               $results[] = $temp->title;
            }
            if ($temp->author != null)
            {
               $results[] = $temp->author;
            }
         }
      }
      unset($data);
      $data = $database->QuerySingle("SELECT *, MATCH(name) AGAINST('".$search_query."*' IN BOOLEAN MODE) AS score FROM `tags` WHERE MATCH(name) AGAINST('".$search_query."*' IN BOOLEAN MODE) ORDER BY score DESC");
      //$results[] = $data;
      if (count($data) == 0) 
      {
         $search_result_tags[] = "THERE ARE NO TAGS";
         //echo "Search found nothing in Tags\n";
      }
      else
      {
         foreach($data as $instance)
         {
            $temp = arrayToObject($instance);
            $results[] = $temp->name;
            // $search_result_tags[] = $temp;
         }
      }
      // $results[] = $search_result_tags;
      // $results[] = $search_result_data;
      echo json_encode($results);
   }
   exit;
}


exit;
?>