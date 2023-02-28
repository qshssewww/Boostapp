<?php
require_once '../../../app/init.php';
require_once '../../Classes/Utils.php';
require_once '../../Classes/ClassStudioAct.php';

if (!isset($_GET['actId']))
    return json_encode(['Message' => 'actId is required']);

$studioActObj = new ClassStudioAct($_GET['actId']);
$activeTrainee = $studioActObj->getEmbbededTraineeData();

$studioDateObj = new ClassStudioDate($studioActObj->ClassId);
$ClassActInfo = $activeTrainee['StudioActDetails'];
//get client membership info.
$ClientInfo = $activeTrainee['clientInfo'];
//$ClientActivity = new ClientActivities();
$ClientActiveInfo = $activeTrainee['ClientActivity'];

$i = $_GET['index'] ?? 0;
?>

<tr data-clientid="<?php echo $ClientInfo->id; ?>" data-classid="<?php echo $ClassActInfo->ClassId; ?>"
    data-actid="<?php echo $ClassActInfo->id; ?>">
    <td>
        <div class="d-flex">
            <div class="position-relative">
                <?php
                //Check if client is in debt.
                if ($ClientInfo->BalanceAmount > 0) {
                    echo '<div class="js-img-to-check bsapp-img-to-check  mie-8 w-40p h-40p rounded-circle border border-danger">';
                } else {
                    echo '<div class="js-img-to-check bsapp-img-to-check  mie-8 w-40p h-40p rounded-circle">';
                }
                ?>
                <img src="<?php
                //display avatar.
                if (!is_null($ClientInfo->ProfileImage)) {
                    echo '/camera/uploads/large/' . $ClientInfo->ProfileImage;
                } else {
                    echo 'https://ui-avatars.com/api/?length=1&name=' . $ClientInfo->FirstName . '&background=f3f3f4&color=000&font-size=0.5';
                }
                ?>" class="">
                <div class="form-group form-check d-none">
                    <input type="checkbox" class="form-check-input js-client-checkbox" id="customSelectSample">
                    <label class="form-check-label" for="customSelectSample"></label>
                </div>
            </div>
            <?php
            if ($activeTrainee['iconArr']['hasDebt']):
                ?>
                <div class="bsapp-absolute-circle h-20p w-20p bg-danger mis-20 bsapp-top-25p bsapp-fs-12 bsapp-z-1-important">
                    <a data-toggle="tooltip" data-placement="top" title="<?php echo lang('client_has_debt') ?>"
                       class="text-white"><i class="fal fa-shekel-sign"></i></a>
                </div>
            <?php
            endif;
            ?>
        </div>
        <div class="d-flex flex-column position-relative">
            <div class="js-client-icons-div">
                <?php if (Auth::userCan('170') || Auth::userCan('171')): ?>
                    <a class="cursor-pointer bsapp-link-cta bsapp-fs-18 js-modal-user"
                       data-client-id="<?php echo $ClientInfo->id ?>"
                       data-activity-id="<?php echo $ClientActiveInfo->id ?>"
                       data-act-id="<?php echo $ClassActInfo->id ?>">
                        <span class="company-name"><?php echo $ClientInfo->CompanyName; ?></span>
                    </a>
                <?php else: ?>
                    <span class="company-name"><?php echo $ClientInfo->CompanyName; ?></span>
                <?php endif;
                //Check for icons display (Medical, Notice,...,).
                $ClientMedicalInfo = $activeTrainee['ClientMedical'];
                if ($ClientMedicalInfo) {
                    $timeLeft = strtotime($ClientMedicalInfo->TillDate) - time();
                    if ($timeLeft >= 0) {
                        echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('customer_card_medical_records') . '">
                        <i class="fal fa-notes-medical text-danger"></i></a>';
                    }
                }
                if ($activeTrainee['ClientCrm']) {
                    echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('note_exists_client') . '">
                    <i class="fal fa-clipboard text-warning">
                    </i></a>';
                }
                if ($activeTrainee['iconArr']['firstClass']) {
                    echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('first_class') . '">
                    <i class="fas fa-star-of-life text-info"></i></a>';
                } else if ($activeTrainee['iconArr']['tryMembership']) {
                    echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('trial_lesson') . '">
                    <i class="fal fa-star-of-life text-info"></i></a>';
                }
                if ($activeTrainee['iconArr']['hasBirthday']) {
                    echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('celebrate_birthday_today') . '">
                    <i class="fal fa-birthday-cake text-danger"></i></a>';
                }
                if ($activeTrainee['iconArr']['regularAssignment']) {
                    echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('setting_permanently') . '">
                    <i class="fal fa-sync text-info"></i></a>';
                }
                if ($activeTrainee['iconArr']['greenpass']) {
                    echo $activeTrainee['iconArr']['greenpass'];
                }
                if ($ClientActiveInfo->TrueClientId != 0) {
                    echo '<a href="#" class="mie-5  bsapp-fs-18 text-gray-400" data-toggle="tooltip" data-placement="top" title="' . lang('family_membersip') . '">
                                                    <i class="fal fa-users"></i></a>';
                }
                ?>
            </div>
            <div>
                <?php
                echo '<span class="badge badge-light badge-pill  bsapp-fs-13 font-weight-normal" style="padding:4px 10px !important;""';
                //Check if membership string is to long and cut if necessary.
                if (mb_strlen($ClientActiveInfo->ItemText) > 25) {
                    echo ' title="' . $ClientActiveInfo->ItemText . '">' . mb_substr($ClientActiveInfo->ItemText, 0, 25) . '...';
                } else {
                    echo '">' . $ClientActiveInfo->ItemText;
                }
                echo '<span class="js-badge-without-charge-content" style="display: none;">' . lang('without_charge') . '</span>';
                echo ' <a class="js-badge-without-charge badge badge-info badge-pill  bsapp-fs-13 py-4 px-10 text-white">';
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
        <?php
        if ($ClassActInfo->Status == 17) {
            echo '<div class="text-start">' . DB::table('class_status')->where('id', $ClassActInfo->Status)->pluck('Title') . '</div>';
        } else {
            //Print membership time info
            $timeLeft = Utils::getTimeLeftInMembershipTxt($ClientActiveInfo->TrueDate);
            if ($ClientActiveInfo->Department == 1) {
                if (!empty($timeLeft)) {
                    echo '<div class="text-start"><div>' . $timeLeft . '</div>';
                } else {
                    echo '<div class="text-start">' . lang('doc_meshulam_36') . '</div>';
                    echo '<div class="text-start text-danger">' . date('d/m/Y', strtotime($ClientActiveInfo->TrueDate)) . '</div>';
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
                    echo '<div class="text-start text-danger">' . $ClientActiveInfo->TrueBalanceValue . ' ' . lang('out_of') . ' ' . $ClientActiveInfo->BalanceValue . '</div>';
                } else {
                    echo '<div class="text-start ">' . $ClientActiveInfo->TrueBalanceValue . ' ' . lang('out_of') . ' ' . $ClientActiveInfo->BalanceValue . '</div>';
                }
            }
        }
        ?>
    </td>
    <?php if ($studioDateObj->__get('ClassDevice')): ?>
        <td>
            <div class="d-flex align-items-baseline" id="device-column<?php echo $ClassActInfo->id ?>">
                <?php if ($ClassActInfo->DeviceId): ?>
                    <div class="mx-5 small px-5 bg-light"><?php echo DB::table('numberssub')->where('id', '=', $ClassActInfo->DeviceId)->pluck('name') ?></div>
                    <a data-actid="<?php echo $ClassActInfo->id ?>" class="js-remove-device cursor-pointer"><i
                                class="fas fa-do-not-enter"></i></a>
                <?php else: ?>
                    <div>
                        <a class="js-add-device-button cursor-pointer" data-toggle="modal"
                           data-target="#add-device-modal">הוספת מכשיר +</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="js-no-device-exist" style="display: none;">
                <a class="js-add-device-button cursor-pointer" data-toggle="modal" data-target="#add-device-modal">הוספת
                    מכשיר +</a>
            </div>
        </td>
    <?php endif; ?>
    <td class="bsapp-td-cta p-0">
        <?php
        if ($ClassActInfo->Status == 17): ?>
            <div class="d-flex justify-content-between w-100 pie-15 bsapp-max-w-100p mt-20">
                <button class="btn btn-outline-gray rounded-pill text-danger js-remove-client-from-class px-6 py-3"><?php echo lang('a_remove_single') ?></button>
                <button class="btn btn-outline-gray rounded-pill text-info js-assign-waiting px-6 py-3 mx-7"><?php echo lang('assign_single') ?></button>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between w-100 pie-15 bsapp-max-w-60p checkbox-wrapper mt-sm-16 bsapp-attendence-checkboxes mx-auto">
                <div data-status='8'
                     class="js-status-8 js-mark-attended custom-control new-custom-checkbox radioTypecheckbox control-danger bsapp-control-times custom-control-inline">
                    <?php if (Auth::userCan('163')): ?>
                        <input type="checkbox" id="customCheckboxInline-2<?php echo $i; ?>"
                               name="customCheckboxInline-single<?php echo $i; ?>"
                               class="custom-control-input chb" <?php
                        if (in_array($ClassActInfo->Status, [7, 8])) {
                            echo 'checked';
                        }
                        ?>>
                        <label class="custom-control-label fal fa-times"
                               for="customCheckboxInline-2<?php echo $i; ?>"></label>
                    <?php else: ?>
                        <label class="fal fa-times" style="border: #959595 solid 1px;width: 36px;
                                height: 36px;border-radius: 50%;text-align: center;line-height: 36px;"></label>
                        <label class="fal fa-check" style="border: #959595 solid 1px;width: 36px;
                                height: 36px;border-radius: 50%;text-align: center;line-height: 36px;"></label>
                    <?php endif; ?>

                </div>
                <div data-status='2'
                     class="js-status-2 js-mark-attended custom-control new-custom-checkbox radioTypecheckbox control-success  bsapp-control-check  custom-control-inline">
                    <?php if (Auth::userCan('163')): ?>
                        <input type="checkbox" id="customCheckboxInline-1<?php echo $i; ?>"
                               name="customCheckboxInline-single<?php echo $i; ?>"
                               class="custom-control-input chb" <?php
                        if (in_array($ClassActInfo->Status, [2, 23])) {
                            echo 'checked';
                        }
                        ?>>
                        <label class="custom-control-label fal fa-check"
                               for="customCheckboxInline-1<?php echo $i; ?>"></label>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </td>
    <?php if (Auth::userCan('164') || Auth::userCan('168')): ?>
        <td class="bsapp-td-cta cursor-pointer pie-0" style="padding-inline-end:0px !important;">
            <div class="d-flex justify-content-end">
                <a class="text-gray-700 dropdown-toggle bsapp-fs-44 pis-30 pie-10  pie-md-15   d-flex justify-content-center   text-decoration-none "
                   data-toggle="dropdown">
                    <i class="fal fa-ellipsis-v "></i>
                </a>
                <div class="dropdown-menu  text-start  w-250p ">

                    <?php if ($ClassActInfo->Status == 17):
                        if (Auth::userCan('164')): ?>
                            <a class="js-add-waiting-list dropdown-item px-8 text-gray-700"
                               onclick="charPopup.clientAddToWaitingList(this);"><span
                                        class="w-20p d-inline-block  text-center"><i class="fal fa-forward"></i></span>
                                <span><?php echo lang('move_to_waiting_list') ?></span> </a>
                        <?php endif;
                    else:
                        if (Auth::userCan('164')): ?>
                            <a class="js-remove-client-from-class dropdown-item  text-gray-700  px-8"
                               data-classid="<?php echo $ClassActInfo->ClassId ?>"
                               data-actid="<?php echo $ClassActInfo->id ?>" href="#"><span
                                        class="w-20p d-inline-block  text-center"><i
                                            class="fal fa-minus-circle"></i></span>
                                <span> <?php echo lang('remove_client_from_class') ?></span></a>

                        <?php endif;
                        if (Auth::userCan('168')): ?>
                            <a class="dropdown-item select-item px-8 text-gray-700 custom-btn" href="javascript:;"><span
                                        class="w-20p d-inline-block  text-center"><i
                                            class="fal fa-paper-plane"></i></span>
                                <span><?php echo lang('send_message_button') ?></span> </a>
                        <?php endif;
                        if (Auth::userCan('164')): ?>
                            <a style="display: none;" class="js-mark-with-charge dropdown-item px-8 text-gray-700"><span
                                        class="w-20p d-inline-block  text-center"><i
                                            class="fal fa-badge-dollar"></i></span>
                                <span><?php echo lang('mark_as_paid_class') ?></span> </a>
                            <a style="display: none;"
                               class="js-mark-without-charge dropdown-item px-8 text-gray-700"><span
                                        class="w-20p d-inline-block  text-center"><i class="fal fa-gift"></i></span>
                                <span><?php echo lang('mark_as_free_class') ?></span> </a>
                        <?php endif; ?>
                        <?php if (Auth::userCan('164')):
                        if (in_array($ClassActInfo->Status, [7, 16, 23])): ?>
                            <a class="js-mark-with-charge dropdown-item px-8 text-gray-700"><span
                                        class="w-20p d-inline-block  text-center"><i
                                            class="fal fa-badge-dollar"></i></span>
                                <span><?php echo lang('mark_as_paid_class') ?></span> </a>
                        <?php else: ?>
                            <a class="js-mark-without-charge dropdown-item px-8 text-gray-700"><span
                                        class="w-20p d-inline-block  text-center"><i class="fal fa-gift"></i></span>
                                <span><?php echo lang('mark_as_free_class') ?></span> </a>
                        <?php endif; ?>
                        <a class="js-add-waiting-list dropdown-item px-8 text-gray-700"
                           onclick="charPopup.clientAddToWaitingList(this);"><span
                                    class="w-20p d-inline-block  text-center"><i class="fal fa-forward"></i></span>
                            <span><?php echo lang('move_to_waiting_list') ?></span> </a>
                    <?php endif;
                    endif; ?>
                </div>
            </div>
        </td>
    <?php endif; ?>
</tr>