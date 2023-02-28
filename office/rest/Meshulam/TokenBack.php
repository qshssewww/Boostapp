<script src="https://login.boostapp.co.il/assets/office/js/vendor/jquery-1.11.1.min.js"></script>
<script>
$(".payment_loader", window.parent.document).hide();
</script>


<?php
require_once '../../../app/init.php';

$Response = $_REQUEST['response'];

if ($Response=='success'){
?>

<script>

$(document).ready(function () { 
window.parent.BN('0','כרטיס האשראי הוצפן בהצלחה');   
parent.location.reload();
});      

</script>

<?php }
else { 
$decoded = base64_decode(@$_REQUEST['json']);    
$responseArr = json_decode(@$decoded, true);       
$err_message = @$responseArr['err']['message'];
//print_r($responseArr);      
?>

<script>

$(document).ready(function () { 
window.parent.BN('1','<?php echo @$err_message ?>');  
window.parent.$('#CreateNewToken').trigger('click');    
//parent.location.reload();
});      

</script>


<?php 
}
?>

