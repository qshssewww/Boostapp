<?php
$spid = $payment->id ?? '';
?>
<div>
    Redirecting...
</div>

<script>
    window.parent.location.href = '<?php echo App::url("office/?action=PaymentSuccess&spid=".$spid) ?>';
</script>