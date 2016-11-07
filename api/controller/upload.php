<?php

// echo "<pre>";
// print_r($_FILES["file"]);
// //echo this;
// echo "</pre>";
// echo "<pre>";
// print_r($_REQUEST);
// //echo this;
// echo "</pre>";
// exit;
$result = File::uploadFile($_REQUEST, $_FILES);
if ($result > 0)
{
   // echo "<pre>";
   // print_r($result);
   //    //echo this;
   // echo "</pre>";
   //    // exit;
   echo "Your upload was successful!";
}
else
{
   if ($result == -6)
   {
      echo "The thumbnail wasn't created and the upload was currupted. Please try again!";
   }
   else if ($result == -5)
   {
      echo "The upload already exists. Please rename the file and try again!";
   }
   else if ($result == -4)
   {
      echo "The file wasn't saved correctly. Please try again.";
   }
   else if ($result == -3)
   {
      echo "There was an error saving the upload. Please try again!";
   }
   else if ($result == -2)
   {
      echo "There was an unknown error uploading the file. Please try again.";
   }
   else if ($result == -1)
   {
      echo "There has already been an upload made with a file by that name.<br/>Please rename the file and try again.";
   }
   else
   {
      echo "<pre>";
      print_r($result);
      //echo this;
      echo "</pre>";
      // exit;
      echo "There was an unknown error. Please try again!";
   }
}
exit;


?>