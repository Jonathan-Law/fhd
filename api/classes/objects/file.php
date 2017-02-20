<?php


require_once(TOOLS."cbSQLRetrieveData.php");
require_once(TOOLS."cbSQLConnectVar.php");
require_once(TOOLS."cbSQLConnectConfig.php");
// Require the Database

class File
{

   protected static $table_name = "file";
   protected static $db_fields = array('id', 'link', 'thumblink', 'viewlink', 'title', 'author', 'comments', 'date', 'type', 'status');
   public static function get_db_fields()
   {
      $fields = array('id', 'link', 'thumblink', 'viewlink', 'title', 'author', 'comments', 'date', 'type', 'status');
      return $fields;
   }
   public static function nameMe()
   {
      return "file";
   }

   // Attributes in place table
   public $id;
   public $link;
   public $thumblink;
   public $viewlink;
   public $title;
   public $author;
   public $comments;
   public $date;
   public $message;
   public $type;
   public $status;


   public static function dropFile($temp_id = NULL)
   {


      // $database = cbSQLConnect::adminConnect('both');
      // if (isset($database))
      // {
      //    return $database->SQLDelete('place', 'fkey', $temp_id);
      // }
   }


   public static function deleteTagById($id = NULL)
   {
      $database = cbSQLConnect::adminConnect('both');
      if (isset($database))
      {
         return $database->SQLDelete('tags', 'id', $id);
      }
   }

   public static function addTag($pid = NULL, $fid = NULL, $type = 'other')
   {
      $database = cbSQLConnect::adminConnect('both');
      if (isset($database))
      {
         $person = recast("Person", Person::getById($pid));
         $result = array();
         if ($pid != NULL)
         {
            $result[] = saveTags($person->selectName(), "file", $fid, "tag_after_file_upload", $pid, $type, URL.$link);
         }
         else
         {
            $result[] = saveTags($person->selectName(), "file", $fid, "tag_after_file_upload", -1, $type, URL.$link);
         }
         // $fields = array();
         // $fields['name'] = $tag;
         // $fields['ftable'] = $table;
         // $fields['fid'] = $id;
         // $fields['category'] = $group;
         // $fields['personid'] = $personId > 0? $personId : -1;
         // $fields['type'] = $type;
         return $result;
      }
   }

   public static function getSomething($thing, $table, $lookup)
   {
      $database = cbSQLConnect::connect('array');
      if (isset($database))
      {
         $data = $database->QuerySingle("SELECT $thing FROM `file` WHERE `fkey`=$lookup AND `ft_name`='{$table}' ORDER BY `id` LIMIT 1");
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


   public static function getByTagType($val, $type = NULL, $limit, $individual){
      $database = cbSQLConnect::connect('object');
      $limitNum = isset($limit) && $limit === true ? 10 : false;
      if (isset($database)){
         if ($type !== NULL && $type === 'person'){
            $query = "SELECT * FROM `file` WHERE `id` IN (SELECT `fileid` FROM `tag` WHERE `foreignid` IN (SELECT `id` FROM `person` WHERE MATCH(`firstName`, `middleName`, `lastName`) AGAINST('".$val."' IN BOOLEAN MODE)))";
            if ($individual !== null) {
               $query .= " AND `id` IN (SELECT `fileid` FROM `tag` WHERE `enum`='person' AND `foreignid`=".$individual.")";
            }
            if ($limitNum) {
               $query .= "LIMIT 0, $limitNum";
            }
         } else if ($type !== NULL && $type === 'place'){
            $place = Place::getByAll($val);
            if ($place){
               $query = "SELECT * FROM `file` WHERE `id` IN (SELECT `fileid` FROM `tag` WHERE `foreignid` IN (SELECT `id` FROM `place` WHERE `id`=".$place->id."))";
               if ($individual !== null) {
                  $query .= " AND `id` IN (SELECT `fileid` FROM `tag` WHERE `enum`='person' AND `foreignid`=".$individual.")";
               }
               if ($limitNum) {
                  $query .= "LIMIT 0, $limitNum";
               }
            } else {
               $query = "SELECT *, MATCH(title, author, comments) AGAINST('".$val."' IN BOOLEAN MODE) AS score FROM `file` WHERE MATCH(title, author, comments) AGAINST('".$val."' IN BOOLEAN MODE)";
               if ($individual !== null) {
                  $query .= " AND `id` IN (SELECT `fileid` FROM `tag` WHERE `enum`='person' AND `foreignid`=".$individual.")";
               }
               $query .=" ORDER BY score DESC";
               if ($limitNum) {
                  $query .= "LIMIT 0, $limitNum";
               }
            }
         } else if ($type !== NULL && $type === 'collection'){
            $query = "SELECT * FROM `file` WHERE `id` IN (SELECT `fileid` FROM `tag` WHERE MATCH(`text`) AGAINST('".$val."' IN BOOLEAN MODE))";
            if ($individual !== null) {
               $query .= " AND `id` IN (SELECT `fileid` FROM `tag` WHERE `enum`='person' AND `foreignid`=".$individual.")";
            }
            if ($limitNum) {
               $query .= "LIMIT 0, $limitNum";
            }
         } else {
            $query = "SELECT *, MATCH(title, author, comments) AGAINST('".$val."' IN BOOLEAN MODE) AS score FROM `file` WHERE MATCH(title, author, comments) AGAINST('".$val."' IN BOOLEAN MODE) OR `link` LIKE '%".$val."%'";
            if ($individual !== null) {
               $query .= " AND `id` IN (SELECT `fileid` FROM `tag` WHERE `enum`='person' AND `foreignid`=".$individual.")";
            }
            $query .= " ORDER BY score DESC";
            if ($limitNum) {
               $query .= "LIMIT 0, $limitNum";
            }
            $database = cbSQLConnect::connect('array');
            return $database->QuerySingle($query);
         }
         return $database->QuerySingle($query);
      }
      return false;
   }

   public static function getByInd($id, $type){
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $name = 'file';
         if ($type != null) {
            $sql = "SELECT * FROM $name WHERE `id` IN (SELECT `fileid` FROM `tag` WHERE `enum`='person' AND `foreignid`=:id) AND `type`=:type";
            $params = array(':id' => $id, ':type'=>$type);
         } else {
            $sql = "SELECT * FROM $name WHERE `id` IN (SELECT `fileid` FROM `tag` WHERE `enum`='person' AND `foreignid`=:id)";
            $params = array(':id' => $id);
         }
         $results_array = $database->QueryForObject($sql, $params);
         return !empty($results_array) ? $results_array : false;
      }
   }

   public static function getAll($individual){
      if (isset($individual)) {
         return File::getByInd($individual, null);
      }
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $name = 'file';
         $sql = "SELECT * FROM $name";
         $params = array();
         $results_array = $database->QueryForObject($sql, $params);
         return !empty($results_array) ? $results_array : false;
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
            $file = $database->getObjectById($name, $id);
            if ($file) {
               $file->tags = Tag::getByFileId($id);
               return recast('File', $file);
            } else {
               return false;
            }
         }
      }
      else
         return NULL;
   }

   public static function getTagsById($id = null)
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $name = 'tags';
         $sql = "SELECT * FROM $name WHERE `fid`=:id";
         $params = array(':id' => $id);
         array_unshift($params, '');
         unset($params[0]);
         $results_array = $database->QueryForObject($sql, $params);
         return !empty($results_array) ? $results_array : false;
      }
   }

   public static function getTagListById($id = null)
   {
      $database = cbSQLConnect::connect('object');
      if (isset($database))
      {
         $name = 'tag';
         $sql = "SELECT * FROM $name WHERE `fileid`=:id";
         $params = array(':id' => $id);
         array_unshift($params, '');
         unset($params[0]);
         $results_array = $database->QueryForObject($sql, $params);
         return !empty($results_array) ? $results_array : false;
      }
   }

   public static function getByLink($temp_link = NULL)
   {
      if ($temp_link)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM $name WHERE `link`=:link";
            $params = array(':link' => $temp_link);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            return !empty($results_array) ? array_shift($results_array) : false;
         }
      }
   }

   public static function getPhotos($id)
   {
      if ($id)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM `tags` WHERE `personid`=".$id." AND `type`='image'";
            $params = array(':link' => $temp_link);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);

            $result = array();
            foreach($results_array as $instance)
            {
               if ($instance->ftable == "file")
                  $result[] = self::getById($instance->fid);
            }
            return $result;
         }
      }
   }
   public static function getDocuments($id)
   {
      if ($id)
      {
         $database = cbSQLConnect::connect('object');
         if (isset($database))
         {
            $name = self::$table_name;
            $sql = "SELECT * FROM `tags` WHERE `personid`=".$id." AND `type`='other'";
            $params = array(':link' => $temp_link);
            array_unshift($params, '');
            unset($params[0]);
            $results_array = $database->QueryForObject($sql, $params);
            // return $results_array;
            $result = array();
            foreach($results_array as $instance)
            {
               if ($instance->ftable == "file")
                  $result[] = self::getById($instance->fid);
            }
            return $result;
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
         if (!isset($data['status'])) {
            $data['status'] = 'I';
         }
         $insert = $database->SQLInsert($data, "file"); // return true if sucess or false
         if ($insert) {
            return json_encode($data);
            // return $insert;
         }
         else
         {
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
            $flag = $database->SQLUpdate("file", $key, $this->{$key}, "id", $this->id);
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
         $tags = $this->getTagListById($this->id);
         foreach ($tags as $tag) {
            $temp = recast('Tag', $tag);
            $temp->delete();
         }
         return ($database->SQLDelete(self::$table_name, 'id', $this->id));
      }
   }


   public static function uploadFile($data = NULL, $files = NULL)
   {
      // error_reporting(E_ALL);
      // ini_set('display_errors', '1');
      // echo "<pre>";
      // print_r($files);
      // echo "</pre>";
      // echo "<pre>";
      // print_r($data);
      // echo "</pre>";
      $tags = isset($data['tag'])? $data['tag'] : array();
      $places = isset($data['place'])? $data['place'] : array();
      $title = isset($data['title'])? $data['title'] : "No Title";
      $author = isset($data['author'])? $data['author'] : "Unknown";

      $comments = $data['file_comments'];
      $newname = $data['newname'];
      $split = explode(".", $files['file']['name']);
      $extension = end($split);
      $savename = $newname.".".$extension;
      $link = UPLOAD."$savename";
      $temp = File::getByLink($link);
      // return $temp;
      if (!$temp)
      {
         // return UPLOAD.$savename;
         $file_path = "upload/".$savename;
         if(file_exists($file_path))
         {
            clearstatcache();
            //the file already exists
            return -1;
         }
         if ($files["file"]["error"] > 0)
         {
            //there was an error uploading the file
            return -2;
         }
         else
         {
            $result = array();
            if (move_uploaded_file($files["file"]["tmp_name"], "upload/" . $savename))
            {
               if (($files["file"]["type"] == "image/gif") || ($files["file"]["type"] == "image/jpeg") || ($files["file"]["type"] == "image/jpg") || ($files["file"]["type"] == "image/bmp") || ($files["file"]["type"] == "image/png" ))
               {
                  $im = self::thumbnail($link, 75);
                  $im2 = self::thumbnail($link, 800);
                  $view_link = "upload/view/".$newname." view.".$extension;
                  $temp_thumblink = "upload/thumbs/".$newname." thumbnail.".$extension;
                  if ($im && $im2)
                  {
                     $imageMade = self::imageToFile($im, $temp_thumblink);
                     if ($imageMade)
                     {
                        $viewMade = self::imageToFile($im2, $view_link);
                        if (!$viewMade)
                        {
                           unlink($link);
                           return -6;
                        }
                     }
                     else
                     {
                        unlink($temp_thumblink);
                        unlink($link);
                        return -6;
                     }
                  }
                  else
                  {
                     unlink($link);
                     return -6;
                  }
                  $type = "image";
               }
               else
               {
                  $view_link = null;
                  $temp_thumblink = "changeMeToDocThumb";
                  $type = "other";
               }
               $result[] = "Stored in: " . "upload/" . $savename;
               $init = new File();
               $init->id = null;
               $init->link = $link;
               $init->thumblink = $temp_thumblink;
               $init->viewlink = $view_link;
               $init->title = $title? $title : "Untitled";
               $init->author = $author;
               $init->comments = $comments;
               $init->date = null;
               $init->status = isset($data->status) ? $data->status : isset($this->status) ? $this->status : 'I';
               $init_id = $init->save();
               if ($init_id)
               {
                  $init->id = $init_id;
                  foreach($tags as $tag)
                  {
                     $temp_name = explode("||", $tag);
                     if ($temp_name[2] != NULL)
                     {
                        $result[] = saveTags($temp_name[0], "file", intval($init->id), "file_upload", $temp_name[2], $type, URL.$link);
                     }
                     else
                     {
                        $result[] = saveTags($temp_name[0], "file", intval($init->id), "file_upload", -1, $type, URL.$link);
                     }
                  }
                  foreach($places as $place)
                  {
                     $result[] = savePlaces($place, "file", intval($init->id), "file_upload");
                  }
                  $init->message = $result;
                  return 1;
               }
               else
               {
                  unlink($temp_thumblink);
                  unlink($link);
                  //database connection wasn't saved
                  return -4;
               }
            }
            else
            {
               //file wasn't moved
               return -3;
            }
         }
      }
      //the database entry already exists
      return -5;
   }

   /**
   * Create a thumbnail image from $inputFileName no taller or wider than
   * $maxSize. Returns the new image resource or false on error.
   * Author: mthorn.net
   */
   public function thumbnail($inputFileName, $maxSize = 75)
   {
      $info = getimagesize ($inputFileName);
      $type = isset ($info['type']) ? $info['type'] : $info[2];
      // Check support of file type
      if ( !(imagetypes() & $type) )
      {
         // Server does not support file type
         return false;
      }

      $width  = isset ($info['width'])  ? $info['width']  : $info[0];
      $height = isset ($info['height']) ? $info['height'] : $info[1];

        // Calculate aspect ratio
      $wRatio = $maxSize / $width;
      $hRatio = $maxSize / $height;

        // ini_set("gd.jpeg_ignore_warning", 1);
      switch ($type)
      {
         case 1 :
         $sourceImage = imageCreateFromGif($inputFileName);
         break;
         case 2 :
         $sourceImage = imageCreateFromJpeg($inputFileName);
         break;
         case 3 :
         $sourceImage = imageCreateFromPng($inputFileName);
         break;
         case 6 :
         $sourceImage = imageCreateFromBmp($inputFileName);
         break;
      }
        // Calculate a proportional width and height no larger than the max size.
      if ( ($width <= $maxSize) && ($height <= $maxSize) )
      {
            // Input is smaller than thumbnail, do nothing
         return $sourceImage;
      }
      elseif ( ($wRatio * $height) < $maxSize )
      {
            // Image is horizontal
         $tHeight = ceil ($wRatio * $height);
         $tWidth  = $maxSize;
      }
      else
      {
            // Image is vertical
         $tWidth  = ceil ($hRatio * $width);
         $tHeight = $maxSize;
      }
      $thumb = imagecreatetruecolor($tWidth, $tHeight);

      if ( $sourceImage === false )
      {
            // Could not load image
         return false;
      }

        // Copy resampled makes a smooth thumbnail
      imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
      imagedestroy($sourceImage);

      return $thumb;
   }

   /**
   * Save the image to a file. Type is determined from the extension.
   * $quality is only used for jpegs.
   * Author: mthorn.net
   */
   public function imageToFile($im, $fileName, $quality = 80)
   {
      if ( !$im || file_exists ($fileName) )
      {
         return false;
      }

      $ext = strtolower (substr ($fileName, strrpos ($fileName, '.')));

      switch ( $ext )
      {
         case '.gif':
         imagegif($im, $fileName);
         break;
         case '.jpg':
         case '.jpeg':
         imagejpeg($im, $fileName, $quality);
         break;
         case '.png':
         imagepng($im, $fileName);
         break;
         case '.bmp':
         imagewbmp($im, $fileName);
         break;
         default:
         return false;
      }

      return true;
   }


}

?>
