<?php

$imgFilename = $_FILES['file']['name'];
$imgLocation = "calendarSettingsImgUpload/".$imgFilename;
$uploadOk = 1;
$imgFileType = pathinfo($location,PATHINFO_EXTENSION);

/* Valid Extensions */
$valid_extensions = array("jpg","jpeg","png");
/* Check Extension */
if (!in_array(strtolower($imgFileType),$valid_extensions)) {
   $uploadOk = 0;
}

if ($uploadOk == 0) {
   echo 0;
} else {
   if (move_uploaded_file($_FILES['file']['tmp_name'],$imgLocation)){
      echo $imgLocation;
   } else{
      echo 0;
   }
}