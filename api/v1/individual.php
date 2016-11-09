<?php
function handleIndividualAPI($args, $that) {
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
            $person->profilePicture = File::getById($person->profile_pic);
            return $person;
          } else {
            return false;
          }
        } else {
          return false;
        }
      }
    } else if ($that->verb === 'submissions') {
      $user = User::current_user();
      $submissions = Person::getSubmissions($user);
      return $submissions;
    } else if ($that->verb === 'allSubmissions' && $session->isAdmin()) {
      $submissions = Person::getSubmissions();
      return $submissions;
    } else if ($that->verb === 'children') {
      if (count($args) > 2 || count($args) < 2) {
        return array();
      } else {
        $id = intval(array_shift($args));
        $spouseid = intval(array_shift($args));
        if ($id && is_numeric($id) && $spouseid && is_numeric($spouseid)){
          $children = Person::getChildrenByParents($id, $spouseid);
          $result = array();
          if ($children && is_array($children) && count($children)) {
            foreach ($children as $child) {
              $person = Person::getById($child->child);
              $person->appendNames();
              $person->profilePicture = File::getById($person->profile_pic);
              $result[] = $person;
            }
          }
          return $result;
        } else {
          return array();
        }
      }
    } else if ($that->verb === 'families') {
      if (!empty($args)) {
        $letter = array_shift($args);
      } else {
        $letter = 'a';
      }
      $all = array_shift($args);
      $all = $all === "true"? true: false;
      $names = array();
      $families = Person::getLastNames($letter, $all);
      if ($families) {
        foreach ($families as $key) {
          $names[] = $key['lastName'];
        }
      }
      return $names;
    } else if ($that->verb === 'family') {
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $person = Person::getById($id);
        $person->appendNames();
        $family = new stdClass();
        $family->self = $person;
        $family->parents = array();
          // $family->siblings = array();
        $children = $person->getChildren();
        $family->children = array();
        foreach ($children as $child) {
          $temp = Person::getById($child->child);
          $temp->appendNames();
          $family->children[] = $temp;
        }
        $spouses = $person->getSpouse();
        $family->spouses = array();
        foreach ($spouses as $spouse) {
          $temp = Person::getById($spouse->spouse);
          $temp->appendNames();
          $family->spouses[] = $temp;
        }
        $siblings = array();
        $tempsiblings = array();
        $person->getParentsGen(4);
        $family->parents = $person->parents;
          // foreach ($parents as $key) {
          //   $parent = Person::getById($key->parentId);
          //   $parent->appendNames();
          //   $family->parents[] = $parent;
          //   // $siblings[] = $parent->getChildren();
          // }
          // foreach ($siblings as $sibling) {
          //   foreach ($sibling as $key) {
          //     $test = true;
          //     foreach ($tempsiblings as $value) {
          //       if ($key->child === $value->child) {
          //         $test = false;
          //       }
          //     }
          //     if ($test) {
          //       $tempsiblings[] = $key;
          //     }
          //   }
          // }
          // foreach ($tempsiblings as $sibling) {
          //   if ($sibling->child !== $person->id) {
          //     $family->siblings[] = Person::getById($sibling->child);
          //   }
          // }
          // foreach ($family->parents as $parent) {
          //   $grandparents = $parent->getParents();
          //   foreach ($grandparents as $grandparent) {
          //     $temp = Person::getById($grandparent->parentId);
          //     $temp->child = $parent->id;
          //     $temp->appendNames();
          //     $family->grandParents[] = $temp;
          //   }
          // }
          // foreach ($family->grandParents as $parent) {
          //   $grandparents = $parent->getParents();
          //   foreach ($grandparents as $grandparent) {
          //     $temp = Person::getById($grandparent->parentId);
          //     $temp->child = $parent->id;
          //     $temp->appendNames();
          //     $family->greatGrandParents[] = $temp;
          //   }
          // }
          // foreach ($family->greatGrandParents as $parent) {
          //   $grandparents = $parent->getParents();
          //   foreach ($grandparents as $grandparent) {
          //     $temp = Person::getById($grandparent->parentId);
          //     $temp->child = $parent->id;
          //     $temp->appendNames();
          //     $family->greatGreatGrandParents[] = $temp;
          //   }
          // }
        return $family;
      } else {
        return new stdClass();
      }
    } else if ($that->verb === 'familyNames') {
      if (!empty($args)) {
        $lastName = array_shift($args);
      } else {
        $lastName = 'Law';
      }
      $all = array_shift($args);
      $all = $all === "true"? true: false;

      $names = array();
      $user = User::current_user();

      $familyNames = Person::getFirstNames($lastName, $all, $user);
      if ($familyNames) {
        foreach ($familyNames as $key) {
          $key = recast('Person', arrayToObject($key));
          $key->appendNames();
          $names[] = $key;
        }
      }
      return $names;
    } else if ($that->verb === 'pictures') {
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $session = mySession::getInstance();
        if ($id > -1) {
          $person = Person::getById($id);
          if ($person) {
            return File::getByInd($person->id, 'image');
          }
        }
      } else {
        return false;
      }
    } else if ($that->verb === 'documents') {
      $id = intval(array_shift($args));
      if ($id && is_numeric($id)) {
        $session = mySession::getInstance();
        if ($id > -1) {
          $person = Person::getById($id);
          if ($person) {
            return File::getByInd($person->id, 'document');
          }
        }
      } else {
        return false;
      }
    }
      // } else {
      // return false;
      // }
  } else if (($that->method === 'DELETE') && $session->isLoggedIn()&& $session->isAdmin()){
    // } else if ($that->method === 'DELETE'){
    $id = intval($args[0]);
    if (is_numeric($id)){
      $person = Person::getById($id);
      if ($person) {
        $birth = Birth::getById($id);
        if ($birth) {
          $birth = recast('Birth', $birth);
            $birth->delete();//delete
          }
          $death = Death::getById($id);
          if ($death) {
            $death = recast('Death', $death);
            $death->delete();//delete
          }
          $burial = Burial::getById($id);
          if ($burial) {
            $burial = recast('Burial', $burial);
            $burial->delete();//delete
          }
          $parents = Parents::getParentsOf($id);
          if ($parents) {
            foreach ($parents as $parent) {
              $parent = recast('Parents', $parent);
              $parent->delete();//delete $parent
            }
          }
          $children = Parents::getChildrenOf($id);
          if ($children) {
            foreach ($children as $child) {
              $child = recast('Parents', $child);
              $child->delete();//delete $child
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
                  $otherSpouse->delete();//delete $otherSpouse
                }
              }
              $spouse->delete();//delete $spouse
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
    } else if (($that->method === 'POST' || $that->method === 'PUT') && $session->isLoggedIn()){
      $user = User::current_user();
      if (!$user->id) { return false; };
      $result = $that->file;
      if (empty($result) || empty($result->person) || empty($result->birth) || empty($result->death)) {
        return 'we failed on check1';
        return false;
      }
      // return $result;
      $person = recast('Person', $result->person);
      if ($person->id) {
        $tempPerson = Person::getById($person->id);
        $person->submitter = $tempPerson->submitter;
        $person->status = $tempPerson->status;
        if ($person->submitter !== $user->id && !($user->rights === 'super' || $user->rights === 'admin')) {
          return 'we failed on check2';
          return false;
        }
      }
      if (!empty($person)) {
        $personId = $person->save($user);
      } else {
        return false;
      }
      if (!!$personId) {
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
          return 'test';
          return false;
        }
        if ($result->birthPlace) {
          $birthPlace = recast('Place', $result->birthPlace);
          $birthPlace->ft_name = "birth";
          $birthPlace->fkey = $birthId;
          $birth->place = $birthPlace->save();
          $birth->save();
        } else {
          $birth->place = null;
          $birth->save();
        }
        if ($result->deathPlace) {
          $deathPlace = recast('Place', $result->deathPlace);
          $deathPlace->ft_name = "death";
          $deathPlace->fkey = $deathId;
          $death->place = $deathPlace->save();
          $death->save();
        } else {
          $death->place = null;
          $death->save();
        }
        if ($burial) {
          if ($result->burialPlace) {
            $burialPlace = recast('Place', $result->burialPlace);
            $burialPlace->ft_name = "burial";
            $burialPlace->fkey = $burial->id;
            $burial->place = $burialPlace->save();
            $burial->save();
          } else {
            $burial->place = null;
            $burial->save();
          }
        } else {
          $burial = Burial::getById($person->id);
          if ($burial && $burial->id) {
            $burial = recast('Burial', $burial);
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
        return $person;
      }
      return false;
    } else {
      return "Only accepts POST and GET requests";
    }
  }
?>
