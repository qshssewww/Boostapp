<?php
/*
 * ======================================
 * SUPPORT BOARD - UPLOAD PHP ADMIN
 * ======================================
 *
 * This file is used for all the attachments: 
 * admin attachments, registration profile image, clien-side attachments of desk and chat
 */

session_start();
if (0 < $_FILES['file']['error']) {
    echo 'Error into upload.php file.';
} else {
    $id = $_POST['user_id'];
    $type =  $_POST['type'];
    if (!file_exists('uploads/' . $id)) {
        mkdir('uploads/' . $id, 0777, true);
    }
    $infos = pathinfo($_FILES['file']['name']);
    if ($type == "attachments") {
        $allowed_extensions = array("psd","ai","eps","pptx","rtf","wma","odp","ods","sxw","sxi","sxc","dwg","xps","jpg","jpeg","png","gif","svg","pdf","doc","docx","key","ppt","odt","xls","xlsx","zip","rar","mp3","m4a","ogg","wav","mp4","mov","wmv","avi","mpg","ogv","3gp","3g2","mkv","txt","ico","exe","csv","java","js","xml","unx","ttf","font","css");
        if (in_array($infos['extension'], $allowed_extensions)) {
            $file_name = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], "uploads/" . $id . "/" . $file_name);
        }
    } else {
        if ($infos['extension'] == "jpg" || $infos['extension'] == "png") {
            $url = "uploads/" . $id . "/" . $id;
            move_uploaded_file($_FILES['file']['tmp_name'], $url . "." . $infos['extension']);
            if ($infos['extension'] == "png") {
                imagejpeg(imagecreatefrompng($url . "." . $infos['extension']), $url . ".jpg", 90);
                unlink($url . "." . $infos['extension']);
            }
        }
    }
}
?>
