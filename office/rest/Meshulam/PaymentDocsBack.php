<script src="https://login.boostapp.co.il/assets/office/js/vendor/jquery-1.11.1.min.js"></script>
<script>
$(".payment_loader", window.parent.document).hide();
</script>


<?php
require_once '../../../app/init.php';

$Response = $_REQUEST['response'];
$ActionM = @$_REQUEST['ActionM'];
$TempId = @$_REQUEST['TempId'];
$ClientId = @$_REQUEST['ClientId'];
$TypeDoc = @$_REQUEST['TypeDoc'];

if ($Response=='success'){
?>

<script>

$(document).ready(function () { 
window.parent.BN('0','כרטיס האשראי חויב בהצלחה');
<?php if ($TypeDoc=='320') { ?>	
window.parent.$("#DocsPayments").load("/office/DocPaymentInfo.php?TypeDoc=<?php echo $TypeDoc; ?>&TempId=<?php echo @$TempId ?>&Act=99"); 
<?php } else { ?>
window.parent.$("#DocsPayments").load("/office/DocPaymentInfoReceipt.php?TypeDoc=<?php echo $TypeDoc; ?>&TempId=<?php echo @$TempId ?>&Act=99&ClientId=<?php echo $ClientId; ?>&Finalinvoicenum=<?php echo $ClientId; ?>"); 	
<?php } ?>	
window.parent.$('#DivPayments').hide();
window.parent.$('#CreditValue3').val('');
<?php if ($ActionM=='1') { ?>
window.parent.$('#ReceiptBtn').prop('disabled', false); 	
window.parent.$('#ReceiptBtn').trigger('click'); 
<?php } ?>	
//parent.location.reload();
});      

</script>

<?php }
else { 
$decoded = base64_decode(@$_REQUEST['json']);    
$responseArr = json_decode(@$decoded, true);       
$err_message = @$responseArr['err']['message'];
?>

<script>

$(document).ready(function () { 
window.parent.BN('1','<?php echo @$err_message ?>');   
window.parent.$('#CreateNewPayments').trigger('click'); 
window.parent.$("#DocsPayments").load("/office/DocPaymentInfo.php?TypeDoc=<?php echo $TypeDoc; ?>&TempId=<?php echo @$TempId ?>&Act=99"); 	

});      

</script>


<?php 
}
?>

