<?php
class Dropzone
{

  protected static $table_name = "birth";
  protected static $db_fields = array('author', 'description', 'docType', 'fileInfo', 'newName', 'tags', 'title', 'file', 'new', 'thumbnail', 'status');
  public static function get_db_fields()
  {
    $fields = array('author', 'description', 'docType', 'fileInfo', 'newName', 'tags', 'title', 'file', 'new', 'thumbnail', 'status');
    return $fields;
  }
  public static function nameMe()
  {
    return "Dropzone";
  }

  public $author;
  public $description;
  public $docType;
  public $fileInfo;
  public $newName;
  public $tags;
  public $title;
  public $file;
  public $new;
  public $thumbnail;
  public $status;

  public static function updateFile($data = NULL) {
    $file = File::getById($data->id);
    $file->type = isset($data->type)? $data->type: NULL;
    $file->comments = isset($data->comments)? $data->comments: NULL;
    $file->author = isset($data->author)? $data->author: NULL;
    $file->title = isset($data->title)? $data->title: NULL;
    $file->status = isset($data->status)? $data->status : (isset($file->status) ? $file->status : 'I');
    if (isset($data->tags)){

      if (isset($data->tags->person) && $file->tags['person'] !== NULL) {
        foreach ($data->tags->person as $key => $tag) {
          $check = false;
          foreach ($file->tags['person'] as $datakey => $value) {
            if (isset($tag->id) && isset($value->id) && $tag->id === $value->id) {
              $check = true;
            }
          }
          if (!$check) {
            $tempTag = new Tag();
            $tempTag->fileid = $file->id;
            $tempTag->text = NULL;
            $tempTag->foreignid = $tag->id;
            $tempTag->enum = 'person';
            $tempTag->save();
          }
        }
      }
      if (isset($data->tags->place) && $file->tags['place'] !== NULL) {
        foreach ($data->tags->place as $key => $tag) {
          $place = Place::getByAll($tag);
          if ($place) {
            $check = false;
            foreach ($file->tags['place'] as $datakey => $value) {
              if (isset($place->id) && isset($value->id) && $place->id === $value->id) {
                $check = true;
              }
            }
            if (!$check) {
              $tempTag = new Tag();
              $tempTag->fileid = $file->id;
              $tempTag->text = NULL;
              $tempTag->foreignid = $place->id;
              $tempTag->enum = 'place';
              $tempTag->save();
            }
          }
          else {
            $place = recast('Place', $tag);
            $placeId = $place->save();
            $tempTag = new Tag();
            $tempTag->fileid = $file->id;
            $tempTag->text = NULL;
            $tempTag->foreignid = $placeId;
            $tempTag->enum = 'place';
            $tempTag->save();
          }
        }
      }
      if (isset($data->tags->other) && $file->tags['other'] !== NULL) {
        foreach ($data->tags->other as $key => $tag) {
          $check = false;
          foreach ($file->tags['other'] as $datakey => $value) {
            if (isset($tag->id) && isset($value->id) && $tag->id === $value->id) {
              $check = true;
            }
          }
          if (!$check) {
            if (isset($tag->id)) {
              $tag = recast('Tag', $tag);
              $tag->id = NULL;
              $tag->fileid = $file->id;
              $tag->foreignid = NULL;
              $tag->enum = 'other';
            } else {
              $text = $tag->text;
              $tag = new Tag();
              $tag->fileid = $file->id;
              $tag->foreignid = NULL;
              $tag->enum = 'other';
              $tag->text = $text;
            }
            $tag->save();
          }
        }
      }


      if (isset($file->tags['person']) && $file->tags['person'] !== NULL) {
        foreach ($file->tags['person'] as $key => $tag) {
          $check = false;
          foreach ($data->tags->person as $datakey => $value) {
            if (isset($tag->id) && isset($value->id) && $tag->id === $value->id) {
              $check = true;
            }
          }
          if (!$check) {
            $tempTag = new Tag();
            $tempTag->fileid = $file->id;
            $tempTag->text = NULL;
            $tempTag->foreignid = $tag->id;
            $tempTag->enum = 'person';
            $temp = Tag::getTagByData($tempTag);
            if ($temp){
              $temp = recast('Tag', $temp);
              $temp->delete();
            }
            // convert a person into a tag;
            unset($file->tags['person'][$key]);
          }
        }
      }
      if (isset($file->tags['place']) && $file->tags['place'] !== NULL) {
        foreach ($file->tags['place'] as $key => $tag) {
          $check = true;
          foreach ($data->tags->place as $datakey => $value) {
            $place = Place::getByAll($value);
            if ($place) {
              if ($tag->id && $tag->id === $place->id) {
                $check = false;
              }
            }
          }
          if ($check) {
            $tempTag = new Tag();
            $tempTag->fileid = $file->id;
            $tempTag->text = NULL;
            $tempTag->foreignid = $tag->id;
            $tempTag->enum = 'place';
            $temp = Tag::getTagByData($tempTag);
            if ($temp){
              $temp = recast('Tag', $temp);
              $temp->delete();
            }
            // convert a place into a tag;
            unset($file->tags['place'][$key]);
          }
        }
      }
      if (isset($file->tags['other']) && $file->tags['other'] !== NULL) {
        foreach ($file->tags['other'] as $key => $tag) {
          if(!in_array($tag, $data->tags->other, true)){
            $temp = recast('Tag', $tag);
            $temp->delete();
            unset($file->tags['other'][$key]);
          }
        }
      }
    }
    // return $file;
    return $file->save();
  }

  public function save()
  {
    return isset($this->new) ? $this->create() : $this->update();
  }

  // create the object if it doesn't already exits.
  protected function create()
  {
    if (empty($this->newName) || empty($this->file)) {
      return false;
    }

    // return $this;
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    // echo "<pre>";
    // print_r($files);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";
    if (isset($this->tags)) {
      $person = isset($this->tags->person)? $this->tags->person : array();
      $other = isset($this->tags->other)? $this->tags->other : array();
      $place = isset($this->tags->place)? $this->tags->place : array();
    }
    $title = isset($this->title)? $this->title : "No Title";
    $author = isset($this->author)? $this->author : "Unknown";
    $comments = $this->description;
    $newname = $this->newName;
    $split = explode(".", $this->file->name);
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
      if ($this->file->error > 0)
      {
        //there was an error uploading the file
        return -2;
      }
      else
      {
        $result = array();
        if (move_uploaded_file($this->file->tmp_name, IMAGEPATH.$link))
        {
          if (($this->file->type == "image/gif") || ($this->file->type == "image/jpeg") || ($this->file->type == "image/jpg") || ($this->file->type == "image/bmp") || ($this->file->type == "image/png" ))
          {
            $im = File::thumbnail(IMAGEPATH.$link, 75);
            $im2 = File::thumbnail(IMAGEPATH.$link, 800);
            $view_link = UPLOAD."view/".$newname." view.".$extension;
            $temp_thumblink = UPLOAD."thumbs/".$newname." thumbnail.".$extension;
            if ($im && $im2)
            {
              $imageMade = File::imageToFile($im, IMAGEPATH.$temp_thumblink);
              if ($imageMade)
              {
                $viewMade = File::imageToFile($im2, IMAGEPATH.$view_link);
                if (!$viewMade)
                {
                  unlink($link);
                  throw new Exception('Unable to create view version');
                }
              }
              else
              {
                unlink($temp_thumblink);
                unlink($link);
                throw new Exception('Unable to create thumbnail version');
              }
            }
            else
            {
              unlink($link);
              throw new Exception('Unable to move file');
            }
            $type = $this->docType;
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
          $init->type = $this->docType;
          $init->status = isset($this->status)? $this->status : 'I';
          $init_id = $init->save();
          if ($init_id)
          {
            if (isset($this->tags) && !empty($this->tags)) {
              foreach ($this->tags as $key => $value) {
                if ($key === 'person') {
                  foreach ($value as $person) {
                    $tag = new Tag();
                    $tag->enum = 'person';
                    $tag->fileid = $init_id;
                    $tag->foreignid = $person->id;
                    $tag->save();
                  }
                } else if ($key === 'place') {
                  foreach ($value as $place) {
                    $tag = new Tag();
                    $tag->enum = 'place';
                    $tag->foreignid = false;
                    $tempPlace = Place::getByAll($place);
                    if ($tempPlace) {
                      $tag->foreignid = $tempPlace->id;
                    } else {
                      $tempPlace = recast('Place', $place);
                      $tpId = $tempPlace->save();
                      if ($tpId) {
                        $tag->foreignid = $tpId;
                      }
                    }
                    if ($tag->foreignid) {
                      $tag->fileid = $init_id;
                      $tag->save();
                    }
                  }
                } else if ($key === 'other') {
                  foreach ($value as $tag) {
                    $tag = recast('Tag', $tag);
                    if ($tag) {
                      $tag->fileid = $init_id;
                      $tag->enum = 'other';
                      $tag->id = NULL;
                      $tag->save();
                    }
                  }
                }
              }
            }
            return $init_id;
          }
          else
          {
            unlink($temp_thumblink);
            unlink($link);
            //database connection wasn't saved
            throw new Exception('Database connection unstable');
          }
        }
        else
        {
          throw new Exception('File wasn\'t moved');
        }
      }
    }
    throw new Exception('File entry already exists');
  }

  // update the object if it does already exist.
  protected function update()
  {
    return $this;
    // update the file
  }

  protected function doSave()
  {
    return false;
  }


  // Delete the object from the table.
  public function delete()
  {
    return $this;
    // delete the file
  }
}
?>
