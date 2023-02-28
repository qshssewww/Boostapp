<?php
require_once "../../../app/init.php";
require_once "../../Classes/Client.php";
require_once '../../Classes/Utils.php';
require_once '../../Classes/ClassStudioDate.php';

if (Auth::check()):
    $clientId = $_GET['clientId'] ?? 0;
    $activityId = $_GET['activityId'] ?? 0;
    $actId = $_GET['actId'] ?? 0;
    // echo $actId;
    if (!$clientId) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'the client was not found', 'code' => 500)));
    }
    $clientObj = new Client($clientId);
    if (!$clientObj) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'the client was not found', 'code' => 500)));
    }
    $clientData = $clientObj->getClientPopUpInfo($actId, $activityId);
    $statusJson = json_decode($clientData["clientAct"]->StatusJson);
    foreach (array_reverse($statusJson->data) as $statusData) {
        if ($statusData->Status == '16') {
            $userName = $statusData->UserName;
            $userId = $statusData->UserId;
            $dates = $statusData->Dates;
            break;
        }
    }

    $ClassDate = ClassStudioDate::find($clientData["clientAct"]->ClassId);
    $isMeeting = !!$ClassDate->meetingTemplateId;
    $CompanyNum = Auth::user()->CompanyNum;
    $CheckUserApp = (new UserBoostappLogin())->findUserByClientIDCompanyNum($clientId, $CompanyNum);

    $isKeva = (new Item())->getItemPaymentById($clientData["activity"]->ItemId);
    //todo-bp-909 (cart) remove-beta - remove this
    $betaCode = Settings::getBetaCode($CompanyNum);

    ?>

    <div class="d-flex flex-column  h-100 ">
        <div class="d-flex justify-content-between align-items-center w-100  border border-light">
            <h5 class="bsapp-fs-18 font-weight-bold p-15"><?php echo lang('trainers_client_info') ?></h5>
            <a href="javascript:;" class="text-dark p-15" data-dismiss="modal"><i class="fal fa-times h4"></i></a>
        </div>
        <div class="bsapp-scroll d-flex flex-column pt-15 overflow-auto js-scroll-height pb-75">
            <div class="w-100  mb-15">
                <div class="d-flex align-items-center  w-100  justify-content-between">
                    <div class="d-flex align-items-center px-15">

                        <?php
                     if (!empty($CheckUserApp->__get('UploadImage'))){ ?>
                            <img src= "<?php echo get_appboostapp_domain(); ?>/camera/uploads/large/<?php echo $CheckUserApp->__get('UploadImage') ?>"
                     <?php } else { ?>
                           <img src= "<?php echo 'https://ui-avatars.com/api/?length=1&name=' . $clientData["clientInfo"]->FirstName . '&background=f3f3f4&color=000&font-size=0.5'?>"
                     <?php } ?>
                             class="w-50p h-50p rounded-circle  mie-8"/>
                        <div class="d-flex flex-column justify-content-between">
                            <div class="h5 bsapp-fs-20 mb-3">
                                <?php echo $clientData["clientInfo"]->CompanyName; ?> <a class="text-gray-400"
                                                                                         href="/office/ClientProfile.php?u=<?php echo $clientData["clientInfo"]->id ?>"
                                                                                         target="_blank"><i
                                            class="fal fa-external-link"></i></a>
                            </div>
                            <div class="d-flex ">
                                <?php if ($clientData["clientLevel"]) : ?>
                                    <div class="text-gray-400 bsapp-fs-14 rounded border border-light py-5 px-7">
                                        <?php echo $clientData["clientLevel"]->Level; ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                if ($clientData["clientInfo"]->parentClientId):
                                    $parentClient = new Client($clientData["clientInfo"]->parentClientId);

                                    $relationshipArr = [
                                        1 => lang('father'),
                                        2 => lang('mother'),
                                        3 => lang('brother_or_sister'),
                                        4 => lang('relative'),
                                        5 => lang('other'),
                                    ];
                                    ?>
                                    <div class="text-gray-400 bsapp-fs-14">
                                        <?php echo lang('minor_client') ?>:
                                        <a target="_blank" class="text-primary px-3 py-5"
                                           href="/office/ClientProfile.php?u=<?php echo $clientData["clientInfo"]->parentClientId ?>">
                                            <?php echo $parentClient->__get('CompanyName') ?>
                                        </a>
                                        <?php echo $clientData["clientInfo"]->relationship ? "(" . $relationshipArr[$clientData["clientInfo"]->relationship] . ")" : "" ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($clientData["clientInfo"]->ContactMobile): ?>
                        <div class="d-flex justify-content-end px-15">
                            <a href="tel:<?php echo $clientData["clientInfo"]->ContactMobile ?>"
                               class="btn btn-sm btn-light mie-8 mb-5 bsapp-text-sm-18"><i class="fal fa-phone"></i></a>
                            <a class="btn btn-sm  btn-light mb-5 text-success bsapp-text-sm-18"
                               href="https://wa.me/<?php echo str_replace('+', '', $clientData["clientInfo"]->ContactMobile) ?>"
                               target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-whatsapp fa-lg"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="w-100  pb-15 px-15 py-5 d-flex flex-column align-items-center border-bottom">
                <div class="bg-light border-light border btn-rounded font-weight-bold text-dark px-15 mb-5"><?php echo $clientData["activity"]->ItemText ?></div>
                <?php if (isset($isKeva) && $isKeva == 2) { ?>
                    <div class="text-gray-400"><?= lang('membership_renew') ?></div>
                <?php } elseif ($clientData["activity"]->TrueDate) { ?>
                    <div class="text-gray-400"><?php echo lang('expires_at') . ':' ?> <span
                                class="<?php echo strtotime($clientData["activity"]->TrueDate) < strtotime("now") ? 'text-danger' : ''; ?>"><?php echo date("d/m/Y", strtotime($clientData["activity"]->TrueDate)) ?></span>
                    </div>
                <?php } ?>
                <?php if ($clientData["activity"]->Department == 2 || $clientData["activity"]->Department == 3) { ?>
                    <div class="text-gray-400"><?php echo lang('remainder_classes') . ':' ?> <span
                                class="<?php echo $clientData["activity"]->TrueBalanceValue <= 0 ? 'text-danger' : ''; ?>"><?php echo $clientData["activity"]->TrueBalanceValue . '/' . $clientData["activity"]->BalanceValue ?></span>
                    </div>
                <?php } ?>
            </div>
            <?php if (Auth::userCan('170')): ?>
                <?php if (in_array($clientData["clientAct"]->Status, [7, 16, 23])) : ?>
                    <div class="w-100 js-charge-div px-15 py-10 w-100 border-bottom border-light rounded-lg">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                <label class="font-weight-bold bsapp-fs-14"><?php echo lang('without_charge') ?></label>
                                <span class=""><span class="mie-7 text-info"><i
                                                class="fal fa-gift fa-lg"></i></span> <?php echo isset($userName) ? lang('set_as_free_class') . ' <span class="font-weight-bold">' . $userName . '</span>' : lang('free_class') ?></span>
                            </div>
                            <div>
                                <a data-actid="<?php echo $actId; ?>"
                                   class="js-remove-charge btn btn-light text-dark px-30 py-5 font-weight-bold bsapp-fs-14"
                                   href="javascript:;"><?php echo lang('action_cacnel') ?></a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($clientData["clientInfo"]->BalanceAmount > 0) : ?>
                    <div class="w-100  px-15 py-10 w-100 border-bottom border-light rounded-lg">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                <label class="font-weight-bold bsapp-fs-14"><?php echo lang('reports_debt_ramain') ?></label>
                                <span class=""><span class="mie-7 text-danger"><i class="fal fa-shekel-sign fa-lg"></i></span> <?php echo lang('client_debt_remain') . ' ' . $clientData["clientInfo"]->BalanceAmount ?> <i
                                            class="fal fa-shekel-sign fa-sm"></i></span>
                            </div>
                            <div>
                                <!-- todo-bp-909 (cart) remove-beta - remove this-->
                                <?php if(in_array($betaCode, [1])) { ?>
<!--                                    <a class="btn btn-light text-dark px-15 py-5 font-weight-bold bsapp-fs-14"-->
<!--                                       href="/office/cart.php?u=--><?php //echo $clientData["clientInfo"]->id;
//                                       ?><!--#user-pay">--><?php //echo lang('charge_client') ?><!--</a>-->
                                <?php } else { ?>
                                <a class="btn btn-light text-dark px-15 py-5 font-weight-bold bsapp-fs-14"
                                   href="/office/ClientProfile.php?u=<?php echo $clientData["clientInfo"]->id . '&client_activities_number=' . $activityId;
                                   ?>#user-pay"><?php echo lang('charge_client') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                if ($clientData["activity"]->TrueClientId):
                    $PayingClient = new Client($clientData["activity"]->ClientId);
                    ?>

                    <div class="w-100  px-15 py-10 w-100 border-bottom border-light rounded-lg">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="d-flex flex-column">
                                <label class="font-weight-bold bsapp-fs-14"><?php echo lang('family_membersip') ?></label>
                                <span class="">
                            <span class="mie-7"><i class="fal fa-users fa-lg"></i></span>
                            <?php echo lang('membership_owner') ?>:
                             <a target="_blank" class="text-primary px-5 py-5"
                                href="/office/ClientProfile.php?u=<?php echo $clientData["activity"]->ClientId ?>#user-activity">
                                 <?php echo $PayingClient->__get('CompanyName') ?>
                             </a>
                        </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (strtotime($clientData["clientInfo"]->Dob) && $clientData["clientInfo"]->Dob != "0000-00-00"): ?>
                    <div class="w-100 px-15 py-10 w-100  border-bottom border-light rounded-lg rounded-lg d-flex justify-content-between align-items-center">
                        <div>
                            <label class="font-weight-bold bsapp-fs-14"><?php echo lang('birthday_single') ?></label>
                            <div class="">
                                <span class="text-info mie-15"><i class="fal fa-birthday-cake bsapp-fs-20"></i></span>
                                <?php echo $clientData["clientInfo"]->CompanyName . ' ' . lang('client_has_birthday_on') . " " . date("d/m", strtotime($clientData["clientInfo"]->Dob)) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($clientData["isFirstClass"] && $clientData["activity"]->Department != 3) : ?>
                    <div class="w-100 px-15 py-10 w-100  border-bottom border-light rounded-lg rounded-lg">
                        <label class="font-weight-bold bsapp-fs-14"><?php echo lang('attention_app_out') ?></label>
                        <div>
                            <span class="mie-5 text-info"><i class="fas fa-star-of-life"></i></span>
                            <?php echo lang('clients_first_class') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($clientData["activity"]->Department == 3) : ?>
                    <div class="w-100 px-15 py-10 w-100 border-bottom border-light rounded-lg rounded-lg">
                        <label class="font-weight-bold bsapp-fs-14"><?php echo lang('attention_app_out') ?></label>
                        <div>
                            <span class="text-info mie-5"><i class="fal fa-star-of-life bsapp-fs-20"></i></span>
                            <?php echo lang('under_try_membership') ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($clientData["clientAct"]->RegularClassId != 0 && $clientData["clientAct"]->RegularClass == 1) : ?>
                    <div class="w-100 js-regular-assignment-div px-15 py-10 w-100 border-bottom border-light rounded-lg">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                <label class="font-weight-bold bsapp-fs-14"><?php echo lang('setting_permanently') ?></label>
                                <span class=""><span class="mie-7 text-info" data-toggle="tooltip" data-placement="top"
                                                     title="<?php echo lang('setting_permanently') ?>"><i
                                                class="fal fa-sync fa-lg"></i></span> <?= lang('regular_assigned_to_' . ($isMeeting ? 'meeting' : 'class')) ?></span>
                            </div>
                            <?php if (!$isMeeting) : ?>
                            <div>
                                <a data-regular-id="<?php echo $clientData["clientAct"]->RegularClassId; ?>"
                                   data-toggle="modal" data-target="#js-remove-regular-assignment-modal"
                                   class="js-remove-regular-assignment btn btn-light text-dark px-30 py-5 font-weight-bold bsapp-fs-14"
                                   href="javascript:;"><?php echo lang('a_remove_single') ?></a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif;
            foreach ($clientData["ClientMedical"] as $medical) :
                require "modal-client-info-crm-medical.php";
            endforeach;
            ?>
            <?php
            foreach ($clientData["ClientCrm"] as $crm) :
                require "modal-client-info-crm-medical.php";
            endforeach;
            ?>
            <input type="hidden" class="clientIdInput" value="<?php echo $clientId; ?>"/>
        </div>

    </div>
    <script>
        $(".js-scroll-height").height($("#js-modal-user-content").height() - 60 + 'px');
    </script>
<?php endif ?>
<?php if (Auth::guest()): ?>
    <?php redirect_to('../index.php'); ?>
<?php endif ?>