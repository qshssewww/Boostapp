<?php
require_once '../../app/init.php';
require_once '../Classes/OrderLogin.php';

if (!isset($errorMessage)) {
    $errorMessage = '';
}
if (!isset($status)) {
    $status = 'pay';
}

if (!isset($type)) {
    $type = null;
}

if (!isset($tokenId)) {
    $tokenId = null;
}

?>


<body>
<div class="mainHomePageContent" id="page-transitions">
    טוען תשלום...
</div>

<div class="mainHomePageContent" id="">
    <?php echo $errorMessage ?>
</div>
<script>
    <?php if ($status === 'close') { ?>
        window.parent.paymentStatus = 'close';
        window.parent.paymentType = 'payment';
    <?php } elseif ($status === 'meshulamSuccess') { ?>
        window.parent.paymentStatus = 'success_meshulam';
        window.parent.paymentType = 'payment';
    <?php } elseif ($status === 'success' && in_array($type, [OrderLogin::TYPE_ADD_NEW_CARD, OrderLogin::TYPE_REFUND_NEW_CARD])) { ?>
        window.parent.paymentStatus = 'success';
        window.parent.paymentType = '<?= $type ?>';
        window.parent.paymentTokenId = '<?= $tokenId ?>';
    <?php } elseif (!$errorMessage) { ?>
        window.parent.paymentStatus = 'success';
    <?php } else { ?>
        window.parent.paymentStatus = 'error';
    <?php } ?>
</script>
</body>
