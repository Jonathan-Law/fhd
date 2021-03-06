<?php
  function handleUserAPI($args, $that) {
    require_once(APIROOT.'controller/user.php');
    if ($that->method === 'POST') {
      if ($that->verb === 'login') {
        $result = $that->file;
        $result = login(isset($result->username)? $result->username: null,
          isset($result->password)? $result->password: null);
        return User::current_user();
      } else if ($that->verb === 'logout') {
        return $session->logout();
      } else if ($that->verb === 'register') {
        $result = $that->file;
        $result->username = isset($result->username)? $result->username: null;
        $result->password = isset($result->password)? $result->password: null;
        $result->email = isset($result->email)? $result->email: null;
        $result->first = isset($result->first)? $result->first: null;
        $result->last = isset($result->last)? $result->last: null;
        $result->gender = isset($result->gender)? $result->gender: null;

        $result = register($result);
        return User::current_user();
      } else if ($that->verb === 'sendAdminMessage') {
        $user = User::current_user();
        $that->file->name = $user->displayableName;
        $that->file->email = $user->email;
        return sendAdminMessage($that->file);
      } else if ($that->verb === 'resetPassword') {
        $user = User::getByUsername($that->file->username);
        if ($user) {
          return $user->resetPassword();
        }
      }
    }
    if ($that->method === 'GET') {
      if ($that->verb === '') {
        $session = mySession::getInstance();
        $user_id = $session->getVar('user_id');
        if ($user_id) {
          $user = User::getById($user_id);
          unset($user->password);
          return $user;
        } else {
          return false;
        }
      }
      if ($that->verb === 'validate') {
        $id = getRequest('id');
        $value = getRequest('validate');
        return validate($id, $value);
      }
      if ($that->verb === 'isLoggedIn') {
        return User::current_user();
      }
      if ($that->verb === 'getUserInfo' && $session->isLoggedIn() && $session->isAdmin()){
        $id = intval(array_shift($args));
        if ($id && is_numeric($id)) {
          $user = User::getById($id);
          unset($user->password);
          return $user;
        } else {
          return User::getAllUsers();
        }
      }
      $user = User::current_user();
      unset($user->password);
      return $user;
      // return "that is a test";
    } else {
      return "Only accepts GET AND POSTS requests";
    }
  }

  function sendAdminMessage($message) {
    $body = "<div>
      Message from User: ".$message->name." &mdash; ".$message->email."
      <br>
      <br>
      ".$message->message."
    </div>";
    $body;
    return sendOwnerUpdate($body, 'FHD: User Message from '.$message->name);
  }
?>
