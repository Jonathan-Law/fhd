<?php
function doInd($that, $args){
  $that = recast('MyAPI', $that);
  $session = mySession::getInstance();
  if ($that->method === 'GET') {
    if ($that->verb === ''){
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $session = mySession::getInstance();
        if ($id > -1) {
          $person = Person::getById($id);
          if ($person) {
            $person->appendNames();
            $person->birth = Birth::getById($id);
            if ($person->birth) {
              $person->birth->birthPlace = Place::getById($person->birth->place);
            }
            $person->death = Death::getById($id);
            if ($person->death) {
              $person->death->deathPlace = Place::getById($person->death->place);
            }
            $person->burial = Burial::getById($id);
            if ($person->burial) {
              $person->burial->burialPlace = Place::getById($person->burial->place);
            }
            $person->parents = Parents::getParentsOf($id);
            $person->children = Parents::getChildrenOf($id);
            $person->spouse = Spouse::getById($id);
            return $person;
          } else {
            return false;
          }
        } else {
          return false;
        }
      }
    } else if ($that->verb === 'families') {
      if (!empty($args)) {
        $letter = array_shift($args);
      } else {
        $letter = 'a';
      }
      $names = array();
      $families = Person::getLastNames($letter);
      if ($families) {
        foreach ($families as $key) {
          $names[] = $key['lastName'];
        }
      }
      return $names;
    } else if ($that->verb === 'familyNames') {
      if (!empty($args)) {
        $lastName = array_shift($args);
      } else {
        $lastName = 'Law';
      }
      $names = array();
      $familyNames = Person::getFirstNames($lastName);
      if ($familyNames) {
        foreach ($familyNames as $key) {
          $key = recast('Person', arrayToObject($key));
          $key->appendNames();
          $names[] = $key;
        }
      }
      return $names;
    }
  } else if (($that->method === 'DELETE') && $session->isLoggedIn()&& $session->isAdmin()){
    $id = intval($args[0]);
    if (is_numeric($id)){
      $person = Person::getById($id);
      if ($person) {
        $birth = Birth::getById($id);
        if ($birth) {
          $birth = recast('Birth', $birth);
          $birth->delete();
          //delete
        }
        $death = Death::getById($id);
        if ($death) {
          $death = recast('Death', $death);
          $death->delete();
          //delete
        }
        $burial = Burial::getById($id);
        if ($burial) {
          $burial = recast('Burial', $burial);
          $burial->delete();
          //delete
        }
        $parents = Parents::getParentsOf($id);
        if ($parents) {
          foreach ($parents as $parent) {
            $parent = recast('Parents', $parent);
            $parent->delete();
            //delete $parent
          }
        }
        $children = Parents::getChildrenOf($id);
        if ($children) {
          foreach ($children as $child) {
            $child = recast('Parents', $child);
            $child->delete();
            //delete $child
          }
        }
        $mySpouse = Spouse::getById($id);
        if ($mySpouse) {
          foreach ($mySpouse as $spouse) {
            $spouse = recast('Spouse', $spouse);
            $theirSpouse = Spouse::getById($spouse->personId);
            if ($theirSpouse) {
              foreach ($theirSpouse as $otherSpouse) {
                $otherSpouse = recast('Spouse', $otherSpouse);
                $otherSpouse->delete();
                //delete $otherSpouse
              }
            }
            $spouse->delete();
            //delete $spouse
          }
        }
        $tags = Tag::getByIndId($id);
        if ($tags) {
          foreach ($tags as $tag) {
            $tag = recast('Tag', $tag);
            $tag->delete();
          }
        }
        $person->delete();
        return true;
      } else {
        return true;
      }
    }
    return false;
  } else if (($that->method === 'POST' || $that->method === 'PUT') && $session->isLoggedIn()&& $session->isAdmin()){
    $result = $that->file;
    if (empty($result) || empty($result->person) || empty($result->birth) || empty($result->death)) {
      return false;
    }
      // return $result;
    $person = recast('Person', $result->person);
    if (!empty($person)) {
      $personId = $person->save();
    } else {
      return false;
    } 
    if ($personId) {
      $person->id = $personId;
      $birth = recast('Birth', $result->birth);
      $birth->personId = $personId;
      $birthId = $birth->save();
      $birth->id = $birthId;
      $death = recast('Death', $result->death);
      $death->personId = $personId;
      $deathId = $death->save();
      $death->id = $deathId;
      if ($result->burial) {
        $burial = recast('Burial', $result->burial);
        $burial->personId = $personId;
        $burialId = $burial->save();
        $burial->id = $burialId;
      } else {
        $burial = false;
      }
      if (empty($personId) || empty($birthId) || empty($deathId)) {
        return false;
      }
      if ($result->birthPlace) {
        $birthPlace = recast('Place', $result->birthPlace);
        $birthPlace->ft_name = "birth";
        $birthPlace->fkey = $birthId;
        $birth->place = $birthPlace->save();
        $birth->save();
      } else {
        if ($birth && $birth->birthPlace) {
          $birthPlace = Place::getById($birth->birthPlace->id);
          if ($birthPlace){
            $birthPlace = recast('Place', $birthPlace);
            if ($birthPlace) {
              $birthPlace->delete();
            }
          } 
        }
        $birthPlace = null;
      }
      if ($result->deathPlace) {
        $deathPlace = recast('Place', $result->deathPlace);
        $deathPlace->ft_name = "death";
        $deathPlace->fkey = $deathId;
        $death->place = $deathPlace->save();
        $death->save();
      } else {
        $deathPlace = Place::getById($death->deathPlace->id);
        if ($death && $death->deathPlace) {
          $deathPlace = recast('Place', Place::getById($death->deathPlace->id));
          if ($deathPlace) {
            $deathPlace->delete();
          }
        }
        $deathPlace = null;
      }
      if ($burial) {
        if ($result->burialPlace) {
          $burialPlace = recast('Place', $result->burialPlace);
          $burialPlace->ft_name = "burial";
          $burialPlace->fkey = $burial->id;
          $burial->place = $burialPlace->save();
          $burial->save();
        } else {
          $burialPlace = Place::getById($burial->place);
          if ($burial && $burialPlace) {
            $burialPlace = recast('Place', Place::getById($burial->place));
            if ($burialPlace) {
              $burialPlace->delete();
            }
          } 
        }
      } else {
        $burial = Burial::getById($person->id);
        if ($burial && $burial->id) {
          $burial = recast('Burial', $burial);
          $burialPlace = Place::getById($burial->place);
          if ($burialPlace) {
            $burialPlace = recast('Place', Place::getById($burial->place));
            if ($burialPlace) {
              $burialPlace->delete();
            }
          } 
          $burial->delete();            
        } 
      }
      if ($result->parents) {
        if ($person->id) {
          $parents = Parents::getParentsOf($person->id);
          if ($parents) {
            $missing = array();
            foreach ($parents as $parent) {
              if (!objectListContains($result->parents, 'id', $parent->parentId)) {
                $missing[] = $parent;
              }
            }
            foreach ($missing as $parent) {
              $parent = recast('Parents', $parent);
              $parent->delete();
            }
            foreach ($result->parents as $key) {
              if (!objectListContains($parents, 'parentId', $key->id)) {
                $newPadre = new Parents();
                $newPadre->child = $person->id;
                $newPadre->gender = ($key->sex === 'male')? 'father': 'mother';
                $newPadre->parentId = $key->id;
                $newPadre->save();
              }
            }
          } else {
            foreach ($result->parents as $key) {
              $newPadre = new Parents();
              $newPadre->child = $person->id;
              $newPadre->gender = ($key->sex === 'male')? 'father': 'mother';
              $newPadre->parentId = $key->id;
              $newPadre->save();
            }
          } 
        } else {
          return 'We have an error';
        }
      } else {
        $parents = Parents::getParentsOf($person->id);
        if ($parents) {
          foreach ($parents as $parent) {
            $parent = recast('Parents', $parent);
            $parent->delete();
          }
        }
      }
      if ($result->spouse) {
        $spouses = Spouse::getAllSpousesById($person->id);
        if ($spouses) {
          $missing = array();
          foreach ($spouses as $spouse) {
            if (!objectListContains($result->spouse, 'id', $spouse->spouse)) {
              $missing[] = $spouse;
            }
          }
          foreach ($missing as $spouse) {
            $spouse = recast('Spouse', $spouse);
            $place = Place::getById($spouse->place);
            if ($place) {
              $place = recast('Place', $place);
              $place->delete();
            }
            $otherSpouse = Spouse::getByPair($spouse->personId, $spouse->spouse);
            if ($otherSpouse) {
              $place = Place::getById($otherSpouse->place);
              if ($place) {
                $place = recast('Place', $place);
                $place->delete();
              }
              $otherSpouse = recast('Spouse', $otherSpouse);
              $otherSpouse->delete();
            }
            $spouse->delete();
          }
          foreach ($result->spouse as $spouse) {
            if (!objectListContains($spouses, 'spouse', $spouse->id)) {
              Spouse::addSpouse($spouse, $person->id, $spouse->id);
              Spouse::addSpouse($spouse, $spouse->id, $person->id);
            } else {
              Spouse::updateSpouse($spouse, $spouse->id, $person->id);
              Spouse::updateSpouse($spouse, $person->id, $spouse->id);
            }
          }
        } else {
          foreach ($result->spouse as $spouse) {
            Spouse::addSpouse($spouse, $person->id, $spouse->id);
            Spouse::addSpouse($spouse, $spouse->id, $person->id);
          }
        }
      } else {
        $spouses = Spouse::getAllSpousesById($person->id);
        if ($spouses){
          foreach ($spouses as $spouse) {
            $spouse = recast('Spouse', $spouse);
            $place = Place::getById($spouse->place);
            if ($place) {
              $place = recast('Place', $place);
              $place->delete();
            }
            $otherSpouse = Spouse::getByPair($spouse->personId, $spouse->spouse);
            if ($otherSpouse) {
              $place = Place::getById($otherSpouse->place);
              if ($place) {
                $place = recast('Place', $place);
                $place->delete();
              }
              $otherSpouse = recast('Spouse', $otherSpouse);
              $otherSpouse->delete();
            }
            $spouse->delete();
          }
        }
      }
      return true;
    }
    return false;
  }else {
    return "Only accepts POST and GET requests";
  }
}
?>