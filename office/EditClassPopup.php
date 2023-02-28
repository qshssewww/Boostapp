<?php
include_once('loader/loader.php');
?>


<link href="/office/calendarPopups/assets/css/popup.css" rel="stylesheet">
<link href="/office/calendarPopups/assets/css/editPopup.css" rel="stylesheet">
<link href="/office/calendarPopups/assets/css/createNewCalendar.css" rel="stylesheet">
<link href="/office/calendarPopups/assets/css/frequencySettingsPopup.css" rel="stylesheet">
<link href="/office/calendarPopups/assets/css/createNewClassType.css" rel="stylesheet">
<link href="/office/calendarPopups/assets/css/cancelPopup.css" rel="stylesheet">
<link href="/office/calendarPopups/assets/css/zoomPopup.css" rel="stylesheet">
<script src="/office/calendarPopups/assets/js/newCalendar.js"></script>
<script src="/office/calendarPopups/assets/js/editPopup.js"></script>
<script src="/office/calendarPopups/assets/js/repeatPopup.js"></script>
<script src="/office/calendarPopups/assets/js/cancelPopup.js"></script>
<script src="/office/calendarPopups/assets/js/createNewClassPopup.js"></script>
<script src="/office/calendarPopups/assets/js/createNewLocationPopup.js"></script>
<script src="/assets/office/js/jquery.Jcrop.min.js"></script>
<script src="/assets/office/js/jquery.imgpicker.js"></script>
<script src="/office/calendarPopups/assets/js/uploadImage.js"></script>


<?php include_once('calendarPopups/mainPopup.php'); ?>
<?php include_once('calendarPopups/editPopup.php'); ?>
<?php include_once('calendarPopups/createNewClassTypePopup.php'); ?>
<?php include_once('calendarPopups/createNewCalendarPopup.php'); ?>
<?php include_once('calendarPopups/frequencySettingsPopup.php'); ?>
<?php include_once('calendarPopups/cancelPopup.php'); ?>
<?php include_once('calendarPopups/zoomPopup.php'); ?>
<?php include_once("calendarPopups/UploadImg.php");?>

<script src="https://cdn.tiny.cloud/1/xaim09qncidvaryqjfpfu4i32rwf3objj6he6zajudj143hf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script type="text/javascript">
    tinymce.init({
        selector: '#classContent',
        directionality: "rtl",
        menubar: false,
        statusbar: false,
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | forecolor backcolor',
        plugins: "textcolor"
    });
</script>
