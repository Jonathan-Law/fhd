<?php
$session = mySession::getInstance();

function login($username, $password){
  $session = mySession::getInstance();
  if ($session->isLoggedIn())
  {
    $user = User::current_user();
    unset($user->password);
    // return 0;
    return $user;
    exit;
  }
  $username = val_string($username);
  $password = val_string($password);
  // $page = $_REQUEST['page'];
  $password = sha1($password);

  // Check if user has been found in database
  $found_user = User::authenticate($username,$password);
  if($found_user)
  {
    // log them in
    $session->login($found_user);
    // return User::current_user();
    // grab the account status
    $profile_status = $found_user->status;

    if($profile_status != 'current')
    {
      return false;
      // their account isn't set up so log them out send them to the login page.
      $session->logout();
      return false;
      exit;
    }
    else
    {
      unset($found_user->password);
      // return 1;
      return $found_user;
      exit;
    }
  }
  else
  {
    // If User Not Found
    return false;
  }
  exit;

}

function register($info = null){
  $session = mySession::getInstance();
  if ($session->isLoggedIn())
  {
    $session->logout();
  }
  if ($info !== null) {
    //check email and username they have to be unique
    $check = User::getUserByCred($info->email, $info->username);
    if (!$check) {
      $temp_user = new User();
      $temp_user->username   = $info->username;
      $temp_user->email      = $info->email;
      $temp_user->password   = sha1($info->password);
      $temp_user->first_name = $info->first;
      $temp_user->last_name  = $info->last;
      $temp_user->gender     = $info->gender;
      $temp_user->rights     = 'normal';
      $temp_user->status     = 'current';
      $temp_user->valid      = 1;
      $temp_user->save();
      $found_user = User::authenticate($temp_user->username,$temp_user->password);
      if($found_user)
      {
        // log them in
        $session->login($found_user);

        // grab the account status
        $profile_status = $found_user->status;

        if($profile_status != 'current')
        {
          // their account isn't set up so log them out send them to the login page.
          $session->logout();
          $session->message("Creating your user profile failed");
          return false;
        }
        else
        {
          // send verification email.
          $to = $found_user->email;
          $check = substr($found_user->password, -12);
          $subject = "familyhistorydatabase.org verification email";
          $message = "You are now registered at familyhistorydatabase.org.\n";
          $message = $message."\nTo verify your membership click on the link below.\nIf you weren't requesting membership, please ignore this email, and we send our appologies!";
          $message = $message."\n".APIURL."user/validate/?id=".$found_user->id."&validate=$check";

          $from = "noreply@familyhistorydatabase.org";
          $headers = "From:" . $from;
          mail($to,$subject,$message,$headers);

          unset($found_user->password);
          return $found_user;
        }
      }
    }
    else
    {
      // If User Not Found
      return false;
    }
  }
  else
  {
    // If User Not Found
    return false;
  }
  exit;
}

function validate($id = null, $value = null) {
  $session = mySession::getInstance();
  if ((isset($id) && $id !== null) && (isset($value) && $value !== null)) {
    $user = User::getUserById($id);
    $user = recast('User', $user);
    if ($value ===  substr($user->password, -12))
    {
      $user->rights = "medium";
      $user->save();
      $session->login($user);
      redirect_home();
      exit;
    }
    else
    {
      return "The Validation Failed. Please try the link from your email one more time.\n If this issue persists, contact the site owner.";
    }

  }
  else
  {
    return "The Validation Failed. Please try the link from your email one more time.\n If this issue persists, contact the site owner.";
  }
  exit;
}


// ////////////////////////////////////////////////////////////////////////////////
// // 8888888b.  8888888b.   .d88888b.  8888888888 8888888 888      8888888888
// // 888   Y88b 888   Y88b d88P" "Y88b 888          888   888      888
// // 888    888 888    888 888     888 888          888   888      888
// // 888   d88P 888   d88P 888     888 8888888      888   888      8888888
// // 8888888P"  8888888P"  888     888 888          888   888      888
// // 888        888 T88b   888     888 888          888   888      888
// // 888        888  T88b  Y88b. .d88P 888          888   888      888
// // 888        888   T88b  "Y88888P"  888        8888888 88888888 8888888888
// //
// ////////////////////////// move to the profile page ////////////////////////////
// if($action == "profile")
// {
//    $session = mySession::getInstance();
//    if (!$session->isLoggedIn())
//    {
//       // log them out and send them home.
//       $session->logout();
//       $session->save("content", "home.php");
//       redirect_home();
//    }
//    else
//    {

//    // grab the session
//       $session = mySession::getInstance();

//    // log them out and send them home.
//       $session->save("content", "user/profile.php");
//       include_page('home.php');
//    }
//    exit;
// }
// ///////////////////////////// modify the profile ///////////////////////////////
// if($action == "profile_change")
// {
//    $user = User::current_user();
//    $user = recast('User', $user);

//    // $session = mySession::getInstance();
//       // echo "<pre>";
//       // print_r($user);
//       // echo "</pre>";
//    // exit;


//    if(isset($_REQUEST['username_reset']))
//    {
//       $username_reset = $_REQUEST['username_reset'];
//    }
//    if(isset($_REQUEST['email_reset']))
//    {
//       $email_reset = $_REQUEST['email_reset'];
//    }
//    if(isset($_REQUEST['first_reset']))
//    {
//       $first_reset = $_REQUEST['first_reset'];
//    }
//    if(isset($_REQUEST['last_reset']))
//    {
//       $last_reset = $_REQUEST['last_reset'];
//    }
//    if(isset($_REQUEST['company_reset']))
//    {
//       $company_reset = $_REQUEST['company_reset'];
//    }

//    //variables we'll use as flags
//    $continue = FALSE;
//    $go = TRUE;

//    //get the lower case username reset
//    $check = strtolower($username_reset);

//    // if the check isn't already registered and is not the current name/email we're good
//    if (in_array($check, User::get_all_something('username')))
//    {
//       if ($username_reset != $user->username)
//       {
//          // echo "it exists";
//          $go = FALSE;
//       }
//    }
//    if (in_array($email_reset, User::get_all_something('email')) && $email_reset != $user->email)
//    {
//       // echo "it exists";
//       $go = FALSE;
//    }


//    // if we can continue we will.
//    if ($go == TRUE)
//    {
//       // here we change the objects status before we save it if it needs changing
//       if ($username_reset)
//       {
//          if ($user->username != $username_reset)
//          {
//             $user->username = $username_reset;
//             $continue = TRUE;
//          }
//       }
//       if ($email_reset)
//       {
//          if ($user->email != $email_reset)
//          {
//             $user->email = $email_reset;
//             $continue = TRUE;
//          }
//       }
//       if ($first_reset)
//       {
//          if ($user->first_name != $first_reset)
//          {
//             $user->first_name = $first_reset;
//             $continue = TRUE;
//          }
//       }
//       if ($last_reset)
//       {
//          if ($user->last_name != $last_reset)
//          {
//             $user->last_name = $last_reset;
//             $continue = TRUE;
//          }
//       }
//       if ($user->company_position != $company_reset)
//       {
//          $user->company_position = $company_reset;
//          $continue = TRUE;
//       }
//    }
//    // echo "<pre>";
//    // print_r($user);
//    // echo "</pre>";
//    // $user = User::current_user();
//    // $user = recast('User', $user);

//    // echo "<pre>";
//    // print_r($user);
//    // echo "</pre>";
//    // exit;

//    $session = mySession::getInstance();
//    // if the information isn't already taken, we continue
//    if ($go === TRUE)
//    {
//       // if there actually was a change, we continue.
//       if ($continue)
//       {
//          if ($user->save())
//          {
//             // if the save was successful
//             // $session->message("Your profile changes were saved successfully");
//             echo "Your profile changes were saved successfully.";
//          }
//          else
//          {
//             // if the save was unsuccessful
//             // $session->message("There was an error and your profile changes were not saved successfully");
//             echo "There was an error and your profile changes were not saved successfully.";
//          }
//       }
//       else
//       {
//          // if there wer no changes
//          // $session->message("There were no changes made");
//          echo "There were no changes necessary.";
//       }
//    }
//    else
//    {
//       // if some of the data was already taken
//       // $session->message("A Username or email was duplicated. Changes were not made perminent");
//       echo "The supplied Username or email was a duplicate. Please change it and try again. Changes were not made perminent.";
//    }
//    exit;
//    // resetUri();
// }

// //////////////////////////////// check username ////////////////////////////////
// if ($action == "check_user")
// {
//    // grab the user
//    $user = User::current_user();
//    $user = recast('User', $user);

//    // grab the variable we'll check
//    if(isset($_REQUEST['check']))
//    {
//       $check = $_REQUEST['check'];
//    }

//    // make it comparable
//    $check = str_replace("_", " ", $check);
//    $check = strtolower($check);

//    // compare them
//    if ($check == strtolower($user->username))
//    {
//       echo "no change";
//    }
//    else
//    {
//       $usernames = $user->get_all_usernames();
//       if (in_array($check, $usernames))
//          echo "in use";
//       else
//          echo "valid";
//    }
//    exit;
// }
// //////////////////////////////// check email ////////////////////////////////
// if ($action == "check_email")
// {
//    // grab the user
//    $user = User::current_user();
//    $user = recast('User', $user);


//    // grab the variable we'll check
//    if(isset($_REQUEST['check']))
//    {
//       $check = $_REQUEST['check'];
//    }

//    // compare them
//    if ($check == $user->email)
//    {
//       echo "no change";
//    }
//    else
//    {
//       $emails = $user->get_all_emails();
//       if (in_array($check, $emails))
//       {
//          echo "in use";
//       }
//       else
//          echo "valid";
//    }
//    exit;
// }
// //////////////////////////////// check first name //////////////////////////////
// if ($action == "check_first")
// {

//    // grab the user
//    $user = User::current_user();
//    $user = recast('User', $user);


//    // grab the variable we'll check
//    if(isset($_REQUEST['check']))
//    {
//       $check = $_REQUEST['check'];
//    }

//    // make it comparable
//    $check = str_replace("_", " ", $check);
//    $check = strtolower($check);

//    // compare them
//    if ($check == strtolower($user->first_name))
//    {
//       echo "no change";
//    }
//    else
//    {
//       echo "valid";
//    }
//    exit;
// }
// //////////////////////////////// check last name ///////////////////////////////
// if ($action == "check_last")
// {

//    // grab the user
//    $user = User::current_user();
//    $user = recast('User', $user);


//    // grab the variable we'll check
//    if(isset($_REQUEST['check']))
//    {
//       $check = $_REQUEST['check'];
//    }

//    // make it comparable
//    $check = str_replace("_", " ", $check);
//    $check = strtolower($check);

//    // compare them
//    if ($check == strtolower($user->last_name))
//    {
//       echo "no change";
//    }
//    else
//    {
//       echo "valid";
//    }
//    exit;
// }
// //////////////////////////////// check company /////////////////////////////////
// if ($action == "check_company")
// {

//    // grab the user
//    $user = User::current_user();
//    $user = recast('User', $user);


//    // grab the variable we'll check
//    if(isset($_REQUEST['check']))
//    {
//       $check = $_REQUEST['check'];
//    }

//    // make it comparable
//    $check = str_replace("_", " ", $check);
//    $check = strtolower($check);

//    // compare them
//    if ($check == strtolower($user->company_position))
//    {
//       echo "no change";
//    }
//    else
//    {
//       echo "valid";
//    }
//    exit;
// }

// // if there was an error write it to the screen
// // if (isset($session->message))
// //    echo $session->message;


// ////////////////////////////////////////////////////////////////////////////////
// // 8888888b.  8888888888 888      8888888888 88888888888 8888888888
// // 888  "Y88b 888        888      888            888     888
// // 888    888 888        888      888            888     888
// // 888    888 8888888    888      8888888        888     8888888
// // 888    888 888        888      888            888     888
// // 888    888 888        888      888            888     888
// // 888  .d88P 888        888      888            888     888
// // 8888888P"  8888888888 88888888 8888888888     888     8888888888
// /////////////////////////////////// Login User /////////////////////////////////
// if ($action == "deleteUser")
// {
//    $user = User::current_user();
//    $user = recast('User', $user);
//    $session->message($user->id);
//    $result = $user->delete();
//    if ($result === true)
//    {
//       echo "It worked!";
//    }
//    else
//    {
//       // $session->logout();
//       echo $result;
//    }
//    $session->logout();
//    exit;
// }

// ////////////////////////////////////////////////////////////////////////////////////////////////////////
// // 8888888888     d8888 888     888  .d88888b.  8888888b.  8888888 88888888888 8888888888 .d8888b.
// // 888           d88888 888     888 d88P" "Y88b 888   Y88b   888       888     888       d88P  Y88b
// // 888          d88P888 888     888 888     888 888    888   888       888     888       Y88b.
// // 8888888     d88P 888 Y88b   d88P 888     888 888   d88P   888       888     8888888    "Y888b.
// // 888        d88P  888  Y88b d88P  888     888 8888888P"    888       888     888           "Y88b.
// // 888       d88P   888   Y88o88P   888     888 888 T88b     888       888     888             "888
// // 888      d8888888888    Y888P    Y88b. .d88P 888  T88b    888       888     888       Y88b  d88P
// // 888     d88P     888     Y8P      "Y88888P"  888   T88b 8888888     888     8888888888 "Y8888P"
// /////////////////////////////////// Favorites List /////////////////////////////////////////////////////
// if ($action == "favorites")
// {
//    $session->save("content", "user/favorites.php");
//    include_page("home.php");
//    exit;
// }

// exit;


?>
