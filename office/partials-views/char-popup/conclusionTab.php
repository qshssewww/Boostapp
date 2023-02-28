<?php

$presentClients = $classInfo['conclusionTab']['present'];
$missingClients = $classInfo['conclusionTab']['missing'];
$lateCancelClients = $classInfo['conclusionTab']['lateCancellation'];
$regularClients = $classInfo['regularTrainers'];
?>
<div class="tab-pane  px-15" id="js-participant-tab-3" role="tabpanel" >
    <ul class="nav nav-tabs bsapp-tabs-underline pis-0 d-none d-sm-flex" id="js-tabs-participants" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="js-participants-tab-1" data-toggle="tab" href="#js-participants-tab-section-1" role="tab" ><?php echo lang('trainers_present') . '- ' . '<span>' . count($presentClients) . '</span>'; ?> </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="js-participants-tab-2" data-toggle="tab" href="#js-participants-tab-section-2" role="tab"  > <?php echo lang('missing_trainers') . '- ' . '<span>' . count($missingClients) . '</span>'; ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="js-participants-tab-3" data-toggle="tab" href="#js-participants-tab-section-3" role="tab"  > <?php echo lang('late_cancellation') . '- ' . '<span>' . count($lateCancelClients) . '</span>'; ?> </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="js-participants-tab-4" data-toggle="tab" href="#js-participants-tab-section-4" role="tab"  > <?php echo lang('permanent_trainees') . '- ' . '<span>' . count($regularClients) . '</span>'; ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="js-participants-tab-5" data-toggle="tab" href="#js-participants-tab-section-5" role="tab"  ><?php echo lang('trainers_class_log') ?></a>
        </li>
    </ul>
    <div class="d-sm-none mt-30">
        <select class="js-tab-select form-control">
            <option value="js-participants-tab-1"><?php echo lang('trainers_present') . '- ' . '<span>' . count($presentClients) . '</span>'; ?></option>
            <option value="js-participants-tab-2"><?php echo lang('missing_trainers') . '- ' . '<span>' . count($missingClients) . '</span>'; ?></option>
            <option value="js-participants-tab-3"><?php echo lang('late_cancellation') . '- ' . count($lateCancelClients); ?></option>
            <option value="js-participants-tab-4"><?php echo lang('permanent_trainees') . '- ' . '<span>' . count($regularClients) . '</span>'; ?></option>
            <option value="js-participants-tab-5"><?php echo lang('trainers_class_log') ?></option>
        </select>        
    </div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active pt-30" id="js-participants-tab-section-1" role="tabpanel" >
            <table id="present-clients" class="table text-start w-100 js-conc-table">
                <thead class="d-none">
                    <tr>
                        <th>Column Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //נוכחים
                    foreach ($presentClients as $client) {
                        $client['clientId'] = $client['trueClientId'] != 0 ? $client['trueClientId'] : $client['clientId'];
                        require 'rowFormat.php';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade pt-30" id="js-participants-tab-section-2" role="tabpanel" >
            <table id="missing-clients" class="table text-start w-100 js-conc-table">
                <thead class="d-none">
                    <tr>
                        <th>Column Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //מבריזים
                    foreach ($missingClients as $client):
                        $client['clientId'] = $client['trueClientId'] != 0 ? $client['trueClientId'] : $client['clientId'];
                        require 'rowFormat.php';
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade pt-30" id="js-participants-tab-section-3" role="tabpanel" >
            <table id="late-cancel-clients" class="table text-start w-100 js-conc-table">
                <thead class="d-none">
                    <tr>
                        <th>Column Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($lateCancelClients as $client):
                        $client['clientId'] = $client['trueClientId'] != 0 ? $client['trueClientId'] : $client['clientId'];
                        $CheckUserApp = (new UserBoostappLogin())->findUserByClientIDCompanyNum($client['clientId'], Auth::user()->CompanyNum);
                    ?>

                        <tr id="conclusion-<?php echo $client['clientId'] ?>" data-client-id="<?php echo $client['clientId'] ?>" data-activity-id="<?php echo $client['activityId'] ?>" data-act-id="<?php echo $client['actId'] ?>">
                            <td>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center position-relative">
                                        <img src="<?php
                                        //display avatar.
                                        if (!empty($CheckUserApp->__get('UploadImage'))) {
                                            echo get_appboostapp_domain().'/camera/uploads/large/'.$CheckUserApp->__get('UploadImage');
                                        } else {
                                            echo 'https://ui-avatars.com/api/?length=1&name=' .$client['firstName'] . '&background=f3f3f4&color=000&font-size=0.5';
                                        }?>" class="w-40p h-40p rounded-circle mie-8">
                                        <div><?php echo $client['companyName']; ?></div>
                                        <a href="javascript:;"  class="stretched-link js-modal-user" data-client-id="<?php echo $client['clientId'] ?>" data-activity-id="<?php echo $client['activityId'] ?>" data-act-id="<?php echo $client['actId'] ?>" ></a>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-status="3" onclick="charPopup.removeClientLateCancel(this)">
                                            <?php echo lang('cancel_charge') ?>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade pt-30" id="js-participants-tab-section-4" role="tabpanel" >
            <table id="regular-assignment" class="table text-start w-100">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center"><?php echo lang("status_table")?></th>
                        <th class="text-center"><?php echo lang("customer_card_start_date")?></th>
                        <th class="text-center"><?php echo lang("customer_card_end_date")?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($regularClients as $regClient):
                        switch ($regClient->StatusType) {
                            case 9:
                                $statusTypeName = lang("class_waiting_list");
                                $colorStatusType = 'text-warning';
                                break;
                            case 12:
                                $statusTypeName = lang("permanent_single");
                                $colorStatusType = 'text-success';
                                break;
                            default:
                                $statusTypeName = lang("customer_card_gender");
                                $colorStatusType = 'text-secondary';
                        }
                        $endDate = $regClient->EndDate ? date('d/m/y', strtotime($regClient->EndDate)) : '--';
                        $client = $regClient->clientInfo;
                        $CheckUserApp = (new UserBoostappLogin())->findUserByClientIDCompanyNum($client->id, Auth::user()->CompanyNum);
                        ?>
                        <tr id="regular-client-<?php echo $client->id ?>">
                            <td>
                                <div class="d-flex">
                                    <div class="d-flex align-items-center position-relative">
                                        <img src="<?php
                                        //display avatar.
                                        if (!empty($CheckUserApp->__get('UploadImage'))) {
                                            echo get_appboostapp_domain().'/camera/uploads/large/' . $CheckUserApp->__get('UploadImage');
                                        } else {
                                            echo 'https://ui-avatars.com/api/?length=1&name=' . $client->FirstName . '&background=f3f3f4&color=000&font-size=0.5';
                                        }
                                        ?>" class="w-40p h-40p rounded-circle mie-8">
                                        <div><?php echo $client->CompanyName; ?></div>
                                        <a href="javascript:;"  class="stretched-link js-modal-user" data-client-id="<?php echo $client->id ?>" data-activity-id="<?php echo $regClient->actInfo->ClientActivitiesId ?? "" ?>" data-act-id="<?php echo $regClient->actInfo->id ?? ""  ?>" ></a>
                                    </div>
                                </div>
                            </td>
                            <td><div class="p-10 text-center <?php echo $colorStatusType;?>"><?php echo $statusTypeName; ?></div></td>
                            <td><div class="p-10 text-center"><?php echo date('d/m/y', strtotime($regClient->StartDate));  ?></div></td>
                            <td><div class="p-10 text-center"><?php echo $endDate; ?></div></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade pt-30" id="js-participants-tab-section-5" role="tabpanel" >
            <table id='log-table' class="table text-start w-100 bsapp-fs-14 table-responsive">
                <thead  class="d-none">
                    <tr>
                        <th>Column 1</th>
                        <th>Column 2</th>
                        <th>Column 3</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($classInfo['classLog'] as $row):
                        $clientObj = new Client($row->ClientId);
                        $CheckUserApp = (new UserBoostappLogin())->findUserByClientIDCompanyNum($row->ClientId, Auth::user()->CompanyNum);
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <div class="d-flex align-items-center position-relative">
                                        <img src="<?php
                                        if (!empty($CheckUserApp->__get('UploadImage'))) {
                                            echo get_appboostapp_domain().'/camera/uploads/large/' . $CheckUserApp->__get('UploadImage');
                                        } else {
                                            echo 'https://ui-avatars.com/api/?length=1&name=' .$clientObj->__get('FirstName') . '&background=f3f3f4&color=000&font-size=0.5';
                                        }
                                        ?>" class="w-40p h-40p rounded-circle mie-8">
                                        <div><?php echo $clientObj->__get('CompanyName'); ?></div>
                                        <a href="javascript:;"  class="stretched-link js-modal-user" data-client-id="<?php echo $clientObj->id ?>" data-activity-id="<?php echo $row->actInfo->ClientActivitiesId ?? "" ?>" data-act-id="<?php echo $row->actInfo->id ?? "" ?>" ></a>
                                    </div>
                                </div>
                            </td>
                            <td><div class="p-10"><?php echo transDbVal(trim($row->Status)) ?></div></td>
                            <td><div class="p-10"><?php echo isset($row->userInfo) ? $row->userInfo->display_name : '' ?></div></td>
                            <td><div class="p-10"><?php echo date('d/m/Y H:i', strtotime($row->Dates)) ?></div></td>
                            <td><div class="js-log-reg-client p-10  d-flex align-items-center"><?php echo $row->numOfClients . '/' . $classInfo['class']->__get('MaxClient') ?></div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
