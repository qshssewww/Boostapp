<div class="tab-pane" id="js-participant-tab-2" role="tabpanel" aria-labelledby="pills-profile-tab">
    <div class="d-flex flex-column w-100 bsapp-tab-pane-waiting">
        <table class="table text-start js-datatable-draggable  border-bottom-0 bsapp-company-details flex-fill" data-tab="waiting" data-bottom-bar="js-participant-tab-2" >
            <thead>
                <tr>
                    <th class="border-0"></th>
                    <th class="border-0  d-flex bsapp-max-w-150p" >
                        <div class="form-group form-check mb-0 d-none js-tbl-select-all">
                            <input type="checkbox" class="form-check-input  js-check-select-all" id="tbl2-select-all">
                            <label class="form-check-label" for="tbl2-select-all"></label>
                        </div>
                        <i class="fal fa-user-friends mie-10 js-fa-user-friends"></i> <?php echo lang('registered') ?></th>
                    <th class="border-0 d-none d-sm-table-cell"> <?php echo lang('subscription_balance') ?></th>
                    <th class="border-0"> <?php echo lang('attendance') ?></th>
                    <th class="border-0"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($classInfo['waitingList'] as $key => $waiter) {
                    $ClientActiveInfo = $waiter->ClientActivity;
                    $ClassActInfo = $waiter;
                    $ClientInfo = $waiter->clientInfo;
                    $CheckUserApp = (new UserBoostappLogin())->findUserByClientIDCompanyNum($ClientInfo->id, $ClientInfo->CompanyNum);
                    ?>
                    <tr data-clientid="<?php echo $ClientInfo->id ?>" data-classid="<?php echo $classInfo['class']->id ?>" data-actid="<?php echo $ClassActInfo->id ?>">
                        <td class="<?php if (Auth::userCan('163')){ echo 'js-row-draggable';}?> d-flex text-gray-400" >
                            <div class="d-flex justify-content-center  text-gray-500 w-100 mie-10">
                                <span>
                                    <i class="fas fa-grip-vertical fa-2x"></i>
                                </span>
                            </div>
                            <?php echo($key + 1) ?>
                        </td>
                        <td    class="overflow-hidden bsapp-max-w-150p">
                            <div class="d-flex">
                                <div class="position-relative">
                                    <div class="js-img-to-check bsapp-img-to-check  mie-8 w-40p h-40p rounded-circle <?php echo $waiter->iconArr['hasDebt'] ? 'border border-danger' : '' ?>">
                                        <img src="<?php
                                        echo !empty($CheckUserApp->__get('UploadImage')) ?
                                                get_appboostapp_domain().'/camera/uploads/large/' . $CheckUserApp->__get('UploadImage') :
                                                'https://ui-avatars.com/api/?length=1&name=' . $ClientInfo->FirstName . '&background=f3f3f4&color=000&font-size=0.5'
                                        ?>"
                                             class="">
                                        <div class="form-group form-check d-none">
                                            <input type="checkbox" class="form-check-input js-client-checkbox" id="t2customSelectSample">
                                            <label class="form-check-label" for="t2customSelectSample"></label>
                                        </div>
                                    </div>
                                    <?php if ($waiter->iconArr['hasDebt']) { ?>
                                        <div class="bsapp-absolute-circle h-20p w-20p bg-info bg-danger mis-20  bsapp-top-25p    bsapp-fs-12 bsapp-z-1-important">
                                            <a data-toggle="tooltip" data-placement="top" title="<?php echo lang('client_has_debt') ?>" class="text-white" ><i class="fal fa-shekel-sign"></i></a>
                                        </div> <?php } ?>
                                </div>

                                <div class="d-flex flex-column position-relative">
                                    <div>
                                        <?php if (Auth::userCan('170') || Auth::userCan('171')): ?>
                                            <a class="cursor-pointer bsapp-link-cta js-modal-user " data-client-id="<?php echo $ClientInfo->id ?>" data-activity-id="<?php echo $ClientActiveInfo->id ?>" data-act-id="<?php echo $ClassActInfo->id ?>">
                                                <span class=""> <?php echo($waiter->clientInfo->CompanyName); ?></span>
                                            </a>
                                        <?php else: ?>
                                            <span class="company-name js-modal-user disabled" data-client-id="<?php echo $ClientInfo->id ?>" data-activity-id="<?php echo $ClientActiveInfo->id ?>" data-act-id="<?php echo $ClassActInfo->id ?>"><?php echo $ClientInfo->CompanyName; ?></span>
                                        <?php endif;

                                        //Check for icons display (Medical, Notice,...,).
                                        $ClientMedicalInfo = $waiter->ClientMedical;
                                        if ($ClientMedicalInfo) {
                                            $timeLeft = strtotime($ClientMedicalInfo->TillDate) - time();
                                            if ($timeLeft >= 0) {
                                                echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('customer_card_medical_records') . '">
                                                <i class="fal fa-notes-medical text-danger"></i></a>';
                                            }
                                        }
                                        if ($waiter->ClientCrm) {
                                            echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('note_exists_client') . '">
                                            <i class="fal fa-clipboard text-warning"></i></a>';
                                        }
                                        if ($waiter->iconArr['firstClass']) {
                                            echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('first_class') . '">
                                            <i class="fas fa-star-of-life text-info"></i></a>';
                                        } else if ($waiter->iconArr['tryMembership']) {
                                            echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('trial_lesson') . '">
                                            <i class="fal fa-star-of-life text-info" ></i></a>';
                                        }
                                        if ($waiter->iconArr['hasBirthday']) {
                                            echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('celebrate_birthday_today') . '">
                                            <i class="fal fa-birthday-cake text-danger"></i></a>';
                                        }
                                        if ($waiter->iconArr['regularAssignment']) {
                                            echo '<a href="#" class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('setting_permanently') . '">
                                            <i class="fal fa-sync text-info"></i></a>';
                                        }
                                        if ($waiter->iconArr['greenpass']) {
                                            echo $waiter->iconArr['greenpass'];
                                        }
                                        if ($waiter->ClientActivity->TrueClientId != 0) {
                                            echo '<a href="#" class="mie-5  bsapp-fs-18 text-gray-400" data-toggle="tooltip" data-placement="top" title="' . lang('family_membersip') . '">
                                                    <i class="fal fa-users"></i></a>';
                                        }
                                        ?>
                                    </div>
                                    <div>
                                        <?php
                                        echo '<span class="badge badge-light badge-pill bsapp-fs-13 font-weight-normal   position-relative d-flex align-items-center" style="padding:4px 10px !important;"';
                                        //Check if membership string is to long and cut if necessary.
                                        if (mb_strlen($ClientActiveInfo->ItemText) > 25) {
                                            echo ' title="' . $ClientActiveInfo->ItemText . '">' . mb_substr($ClientActiveInfo->ItemText, 0, 25) . '...';
                                        } else {
                                            echo '">' . $ClientActiveInfo->ItemText;
                                        }
                                        echo ' <a class="badge badge-info badge-pill  bsapp-fs-13 font-weight-normal bsapp-floating-badge-waiting"  >';
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
                                // check if keva
                                $isKeva = (new Item())->getItemPaymentById($ClientActiveInfo->ItemId);
                                $isKeva = isset($isKeva) && $isKeva == 2;

                                //Print membership time info
                                $timeLeft = Utils::getTimeLeftInMembershipTxt($ClientActiveInfo->TrueDate);
                                if ($isKeva) {
                                    echo '<div class="text-start">' . lang("membership_renew") . '</div>';
                                } else if ($ClientActiveInfo->Department == 1) {
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
                        <td class="bsapp-td-cta"  style="padding-inline-end:0px;">
                            <div>
                                <?php if (Auth::userCan('163')): ?>
                                    <a class="js-assign-waiting btn btn-outline-gray-500 btn-rounded btn-sm bsapp-btn-waiting mt-7"> <?php echo lang('customer_card_embed') ?> </a>
                                <?php else: ?>
                                    <a class="btn disabled btn-rounded btn-sm bsapp-btn-waiting mt-7"> <?php echo lang('class_in_waitlist') ?> </a>
                                <?php endif;?>
                            </div>
                        </td>
                        <?php if (Auth::userCan('164') || Auth::userCan('168')): ?>
                        <td class="bsapp-td-cta pie-0 bsapp-width-430" style="padding-inline-end:0px !important;">
                            <div class="d-flex justify-content-end position-relative">
                                <a href="javascript:;" class="text-gray-700 dropdown-toggle text-decoration-none bsapp-fs-44 pie-10 pis-30 pie-md-15 d-flex justify-content-center" data-toggle="dropdown">
                                    <i class="fal fa-ellipsis-v "></i>
                                </a>
                                <div class="dropdown-menu  text-start w-250p">
                                    <?php if (Auth::userCan('164')): ?>
                                    <a class="js-remove-client-from-class dropdown-item  text-gray-700  px-8" href="#"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> <?php echo lang('remove_client_from_class') ?> </span></a>
                                    <?php endif;
                                    if (Auth::userCan('168')):?>
                                    <a class="dropdown-item px-8 text-gray-700 select-item"  href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-paper-plane"></i></span> <span> <?php echo lang('send_message_button') ?></span></a>
                                    <!-- <a class="dropdown-item  text-gray-700  px-8" href="#"><span class="w-20p d-inline-block  text-center"><i class="fal fa-clipboard-check"></i></span> <span> <?php //echo lang('mark_as_free_class')        ?> </span></a> -->
                                    <!-- <a class="dropdown-item px-8 text-gray-700" href="javascript:;" data-toggle="modal"  data-target="#js-modal-window-message"><span class="w-20p d-inline-block  text-center"><i class="fal fa-paper-plane"></i></span> <span>Message Modal</span></a>-->
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <?php endif;?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <table class="d-none">
            <tr id="js-no-clients-waiting" class="odd">
                <td valign="top" colspan="5" class="dataTables_empty"><div class="text-center"> <?php echo lang('no_waiting_clients'); ?></div></td>
            </tr>
        </table> 
    </div>    
</div>

<style>
    .bsapp-max-w-150p{
     max-width: 150px !important;   
    }
    @media screen and (min-width:576px) and (max-width:767px){
        .bsapp-max-w-150p{
            max-width: 200px !important;
        }
    }
    @media screen and (min-width:767px) {
        .bsapp-max-w-150p{
            max-width: 250px !important;
        }
    }
</style>