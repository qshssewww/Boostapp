<?php
require_once '../../../app/init.php';
require_once '../../Classes/Utils.php';
require_once '../../Classes/ClassStudioAct.php';

if (!isset($_GET['actId']))
    return json_encode(['Message' => 'actId is required']);

if (!isset($_GET['clientWaiting']))
    return json_encode(['Message' => 'clientWaiting is required']);
    
$studioActObj = new ClassStudioAct($_GET['actId']);
$waitingTrainee = $studioActObj->getEmbbededTraineeData();

$studioDateObj = new ClassStudioDate($studioActObj->__get('ClassId'));
$ClassActInfo = $waitingTrainee['StudioActDetails'];
//get client membership info.
$ClientInfo = $waitingTrainee['clientInfo'];
//$ClientActivity = new ClientActivities();
$ClientActiveInfo = $waitingTrainee['ClientActivity'];

$i = 0;
?>
<tr data-clientid="<?php echo $ClientInfo->id ?>" data-classid="<?php echo $ClassActInfo->ClassId; ?>" data-actid="<?php echo $ClassActInfo->id ?>">
    <td class=" <?php if (Auth::userCan('163')){ echo 'js-row-draggable';}?> d-flex text-gray-400" >
        <div class="d-flex justify-content-center  text-gray-500 w-100 mie-10">
            <span>
                <i class="fas fa-grip-vertical fa-2x"></i>
            </span>
        </div>
        <?php echo $_GET['clientWaiting'] ?>
    </td>
    <td>
        <div class="d-flex">
            <div class="position-relative">
                <div class="js-img-to-check bsapp-img-to-check  mie-8 w-40p h-40p rounded-circle <?php echo $waitingTrainee['iconArr']['hasDebt'] ? 'border border-danger' : '' ?>">
                    <img alt="<?php echo $ClientInfo->id; ?>" src="<?php
                    echo $ClientInfo->ProfileImage ?
                            '/camera/uploads/large/' . $ClientInfo->ProfileImage :
                            'https://ui-avatars.com/api/?length=1&name=' .$ClientInfo->FirstName . '&background=f3f3f4&color=000&font-size=0.5';
                    ?>"
                            class="">
                    <div class="form-group form-check d-none">
                        <input type="checkbox" class="form-check-input js-client-checkbox" id="t2customSelectSample">
                        <label class="form-check-label" for="t2customSelectSample"></label>
                    </div>
                </div>
                <?php if ($waitingTrainee['iconArr']['hasDebt']) { ?>
                    <div class="bsapp-absolute-circle h-20p w-20p bg-info bg-danger mis-20  bsapp-top-25p    bsapp-fs-12 bsapp-z-1">
                                        <a data-toggle="tooltip" data-placement="top" title="<?php echo lang('client_has_debt') ?>" class="text-white" ><i class="fal fa-shekel-sign"></i></a>
                    </div> <?php } ?>
            </div>

            <div class="d-flex flex-column position-relative">
                <div>
                    <a class="cursor-pointer bsapp-link-cta js-modal-user " data-client-id="<?php echo $ClientInfo->id ?>" data-activity-id="<?php echo $ClientActiveInfo->id ?>" data-act-id="<?php echo $ClassActInfo->id ?>">
                        <span class=""> <?php echo($ClientInfo->CompanyName); ?></span>
                    </a>
                    <?php
                    //Check for icons display (Medical, Notice,...,).
                    $ClientMedicalInfo = $waitingTrainee['ClientMedical'];
                    if ($ClientMedicalInfo) {
                        $timeLeft = strtotime($ClientMedicalInfo->TillDate) - time();
                        if ($timeLeft >= 0) {
                            echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('customer_card_medical_records') . '">
                                <i class="fal fa-notes-medical text-danger"></i></a>';
                        }
                    }
                    if ($waitingTrainee['ClientCrm']) {
                        echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('note_exists_client') . '">
                            <i class="fal fa-clipboard text-warning"></i></a>';
                    }
                    if ($waitingTrainee['iconArr']['firstClass']) {
                        echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('first_class') . '">
                            <i class="fas fa-star-of-life text-info"></i></a>';
                    } else if ($waitingTrainee['iconArr']['tryMembership']) {
                        echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('trial_lesson') . '">
                            <i class="fal fa-star-of-life text-info" ></i></a>';
                    }
                    if ($waitingTrainee['iconArr']['hasBirthday']) {
                        echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('celebrate_birthday_today') . '">
                            <i class="fal fa-birthday-cake text-danger"></i></a>';
                    }
                    if ($waitingTrainee['iconArr']['regularAssignment']) {
                        echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('setting_permanently') . '">
                            <i class="fal fa-sync text-info"></i></a>';
                    }
                    if ($waitingTrainee['iconArr']['greenpass']) {
                        echo $waitingTrainee['iconArr']['greenpass'];
                    }
                    if ($ClientActiveInfo->TrueClientId != 0) {
                        echo '<a href="#" class="mie-5  bsapp-fs-18 text-gray-400" data-toggle="tooltip" data-placement="top" title="' . lang('family_membersip') . '">
                                                    <i class="fal fa-users"></i></a>';
                    }
                    ?>
                </div>
                <div>
                    <?php
                    echo '<span class="badge badge-light badge-pill  bsapp-fs-13 font-weight-normal" style="padding:4px 10px !important;"';
                    //Check if membership string is to long and cut if necessary.
                    if (mb_strlen($ClientActiveInfo->ItemText) > 25) {
                        echo ' title="' . $ClientActiveInfo->ItemText . '">' . mb_substr($ClientActiveInfo->ItemText, 0, 25) . '...';
                    } else {
                        echo '">' . $ClientActiveInfo->ItemText;
                    }
                    echo ' <a class="badge badge-info badge-pill  bsapp-fs-13 py-4 px-10">';
                    if ($ClassActInfo->Status == 16) {
                        echo lang('without_charge');
                    }
                    echo '</a></span>';
                    ?>
                </div>
            </div>
        </div>
    </td>
    <td class="d-none d-sm-table-cell">
        <div>
            <?php
            //Print membership time info
            $timeLeft = Utils::getTimeLeftInMembershipTxt($ClientActiveInfo->TrueDate);
            if ($ClientActiveInfo->Department == 1) {
                if (!empty($timeLeft)) {
                    echo '<div class="text-start"><div>' . $timeLeft . '</div>';
                } else {
                    echo '<div class="text-start">'.lang('doc_meshulam_36').'</div>';
                    echo '<div class="text-start text-danger">'.date('d/m/Y',strtotime($ClientActiveInfo->TrueDate)).'</div>';
                }
            } elseif ($ClientActiveInfo->Department == 2 || $ClientActiveInfo->Department == 3) {
                $flg = 0;
                if (!empty($timeLeft)) {
                    echo '<div class="text-start"><div>' . $timeLeft . '</div>';
                } else if ($ClientActiveInfo->TrueDate != null) {
                    $flg = 1;
                    echo '<div class="text-start">' . lang('doc_meshulam_36') . '</div>';
                    echo '<div class="text-start text-danger">' . date('d/m/Y', strtotime($ClientActiveInfo->TrueDate)) . '</div>';
                }

                if ($ClientActiveInfo->TrueBalanceValue == 0 && $flg == 0) {
                    echo '<div class="text-start text-danger">'.$ClientActiveInfo->TrueBalanceValue. ' ' . lang('out_of') . ' ' . $ClientActiveInfo->BalanceValue.'</div>';
                } else {
                    echo '<div class="text-start ">' . $ClientActiveInfo->TrueBalanceValue . ' ' . lang('out_of') . ' ' . $ClientActiveInfo->BalanceValue . '</div>';
                }
            }
            ?>
        </div>
    </td>
    <td class="bsapp-td-cta">
        <div>
            <a class="js-assign-waiting btn btn-outline-gray-500 btn-rounded btn-sm "> <?php echo lang('customer_card_embed') ?> </a>
        </div>
    </td>
    <td class="bsapp-td-cta cursor-pointer pie-0" style="padding-inline-end:0px !important;">
        <div class="d-flex justify-content-end">
            <a href="javascript:;" class="text-gray-700 dropdown-toggle bsapp-fs-44 pis-30 pie-10  pie-md-15   d-flex justify-content-center   text-decoration-none " data-toggle="dropdown">
                <i class="fal fa-ellipsis-v "></i>
            </a>
            <div class="dropdown-menu  text-start w-250p">
                <a class="js-remove-client-from-class dropdown-item  text-gray-700  px-8" href="#"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> <?php echo lang('remove_client_from_class') ?> </span></a>
                <a class="dropdown-item px-8 text-gray-700 select-item"  href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-paper-plane"></i></span> <span> <?php echo lang('send_message_button') ?></span></a>
                <!-- <a class="dropdown-item  text-gray-700  px-8" href="#"><span class="w-20p d-inline-block  text-center"><i class="fal fa-clipboard-check"></i></span> <span> <?php //echo lang('mark_as_free_class')        ?> </span></a> -->
                <!-- <a class="dropdown-item px-8 text-gray-700" href="javascript:;" data-toggle="modal"  data-target="#js-modal-window-message"><span class="w-20p d-inline-block  text-center"><i class="fal fa-paper-plane"></i></span> <span>Message Modal</span></a>-->
            </div>
        </div>
    </td>
</tr>
