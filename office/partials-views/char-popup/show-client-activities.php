<?php
require_once '../../../app/init.php';
require_once "../../Classes/ClientActivities.php";


if (!isset($_POST['clientId']))
    return;
$clientId = $_POST['clientId'];

$classDate = $_POST['classDate'];

$clientActivitiesObj = new ClientActivities();
$clientActivities = ClientActivities::getClientActivities($clientId);

if (empty($clientActivities)):
?>
<p><?= lang('this_client_has_no_memberships') ?></p>
<?php
endif;


$currentDate = date('Y-m-d');

foreach ($clientActivities as $key => $activity):

    //Get the balance if Department 2 or 3
    $balance = $activity->Department == 2 || $activity->Department == 3 ? lang("punch_card_balance"). ": ". $activity->TrueBalanceValue. '/' . $activity->BalanceValue: false;
    $warningArray = array();

    //Checks for warnings,if there is an insert in the array
    $classDate < $activity->StartDate ? array_push($warningArray,lang("customer_card_start_date") . ": ". date("d/m/y", strtotime($activity->StartDate))): "";
    $activity->TrueDate && $activity->TrueDate < $classDate ? array_push($warningArray,lang("expires_at") . ": ". date("d/m/y", strtotime($activity->TrueDate))): "";
    if($balance && $activity->TrueBalanceValue <= 0 ) {
        array_push($warningArray,$balance );
        $balance = false;
    }
    ?>
<div class="custom-control custom-radio mb-15 ">
    <input type="radio" id="customRadio<?php echo $key ?>" name="membership" class="custom-control-input"
           value="<?= $activity->id ?>" <?= ($key == 0) ? "checked" : "" ?> data-department="<?php echo $activity->Department ?>">
    <label class="custom-control-label" for="customRadio<?php echo $key ?>">
        <?php echo $activity->ItemText;
        if ($activity->TrueClientId) {
            echo
                '<a data-toggle="tooltip" data-placement="top" title="' . lang('family_membersip') . '">
                     <i class="fas fa-user-friends"></i>
                 </a> </label> ';

        }
        // print balance if department 2 or 3 and no balance warning
        if($balance) {
            echo '<span>'. ' (' . $balance  . ')' .'</span>';
        }
        if(!empty($warningArray)) {
            echo '<span class="text-danger">' . ' (' . implode(', ', $warningArray) . ')' . '</span>';
        }?>
</div>

<?php endforeach; ?>

