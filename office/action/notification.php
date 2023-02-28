<?php

require_once '../../app/initcron.php';

if (Auth::guest()) {
?>
<script>
window.top.location.href = "<?php echo App::url('logout.php') ?>";
</script>
<?php
} else {

    $CompanyNum = Auth::user()->CompanyNum;
    $UserId = Auth::user()->id;
    $Today = date('Y-m-d');
    $TodayTime = date('H:i:s');

    $notifications = DB::table('appnotification')
        ->where('Date', '=', $Today)->where('Time', '<=', $TodayTime)->where('Status', '=', '0')->where('Type', '=', '3')->where('CompanyNum', '=', $CompanyNum)
        ->Orwhere('Date', '<', $Today)->where('Status', '=', '0')->where('Type', '=', '3')->where('CompanyNum', '=', $CompanyNum)
        ->count();

    $_SESSION["notification"] = $notifications;
    $_SESSION["CompanyNum"] = $CompanyNum;
    echo $notifications;

}

