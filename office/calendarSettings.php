<?php
require_once '../app/init.php';
require_once '../app/views/headernew.php';
?>

<link href="../assets/css/fixstyle.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<!-- content goes here -->
<?php require_once '../app/views/calendarSettings.php'; ?>
<?php require_once '../app/views/footernew.php'; ?>

<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script type="text/javascript" src="js/settingsDialog/settingsDialog.js?<?php echo filemtime('js/settingsDialog/settingsDialog.js') ?>"></script>
<script type="text/javascript" src="js/settingsDialog/calendarSettings.js"></script>