<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] ."/app/init.php";

$arr = array(
    "message" => "failed to charge",
    "file_path" => "errorRespone.php",
);
$arr["data"] = json_encode([ "GET" => $_GET], JSON_PRETTY_PRINT);
DB::table("boostapp.update_payment_log")->insertGetId($arr);

$errMsg = $_GET['errMsg'] ?? '';
?>
<div>
    Redirecting...
</div>

<script>
    window.parent.location.href = '<?php echo App::url("office/?action=PaymentError&err=".$errMsg) ?>';
</script>