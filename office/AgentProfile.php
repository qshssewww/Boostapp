<?php
require_once '../app/init.php';
$pageTitle = lang('user_card_agentprofile');
require_once '../app/views/headernew.php';
require_once '../app/helpers/PasswordHelper.php';

?>
<?php if (Auth::check()): ?>
    <?php if (Auth::userCan('4')): ?>


        <?php
        $user = Auth::user();
        $CompanyNum = $user->CompanyNum;
        $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
        $BrandsMain = $SettingsInfo->BrandsMain;
        if ($BrandsMain == '0') {
            $Supplier = DB::table('users')->where('CompanyNum', $CompanyNum)->where('id', $_GET['u'])->first();
        } else {
            $Supplier = DB::table('users')->where('BrandsMain', $BrandsMain)->where('id', $_GET['u'])->first();
        }

        if (empty($_GET['u']) || empty($Supplier) || $_GET['u'] == '1') {

            ErrorPage(
                lang('error_oops_something_went_wrong')
                , lang('user_error_agentprofile')
            );
        } else {

            $datetime = date('Y-m-d');

            $AgentId = @$Supplier->id;
            $AgentRules = DB::table('roles')->where('id', '=', $Supplier->role_id)->first();


            $LogContent =  lang('log_in_agentprofile').' <a href="AgentProfile.php?u='.$Supplier->id.'" target="_blank">'.$Supplier->display_name.'</a>';
            CreateLogMovement($LogContent, '0');

        ?>


            <link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

            <link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
            <link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
            <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">

            <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
            <script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

            <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
            <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
            <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

            <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
            <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--            <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
            <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

            <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
            <script src="../assets/office/js/list.js"></script>

            <link href="assets/css/fixstyle.css" rel="stylesheet">
            <!-- include summernote css/js -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
            <link href="assets/css/fixstyle.css" rel="stylesheet">


            <div class="row">

                <div class="col-md-3 col-sm-12 order-md-3">

                    <div class="card spacebottom">
                        <a data-toggle="collapse" href="#ClientInfoCard" aria-expanded="true"
                           aria-controls="ClientInfoCard" style="color: black;">
                            <div class="card-header text-right">
                                <strong><i class="fas fa-user fa-fw"></i> <?php echo $Supplier->display_name; ?>
                                    :: <?php echo $Supplier->id; ?></strong>
                            </div>
                        </a>

                        <div class="collapse show" id="ClientInfoCard">
                            <div class="card-body">
                                <div class="text-center">
                                    <a href="javascript:void(0);"
                                       onclick="$('.nav-tabs a[href=\'#user-imageprofile\']').click();">
                                        <?php if (empty($Supplier->UploadImage)) { ?>
                                            <img class="rounded-circle img-fluid profileimage align-baseline"
                                                 alt="<?php echo $Supplier->display_name; ?>" width="85" height="85"
                                                 src="<?php echo 'https://ui-avatars.com/api/?name=' . $Supplier->LastName . '+' . $Supplier->FirstName . '&background=' . hexcode($Supplier->display_name) . '&color=ffffff&font-size=0.5'; ?>">
                                        <?php } else { ?>
                                            <img class="rounded-circle img-fluid profileimage align-baseline"
                                                 alt="<?php echo $Supplier->display_name; ?>" width="85" height="85"
                                                 src="../camera/uploads/large/<?php echo $Supplier->UploadImage ?>">
                                        <?php } ?></a></div>


                                <div class="row pt-3">

                                    <table class="table" dir="rtl">
                                        <tbody>
                                        <tr>
                                            <td style="text-align:right; font-weight: bold;">סטטוס:</td>
                                            <td dir="ltr"
                                                style="text-align: right;"><?php if (@$Supplier->status == '1') {
                                                    echo '<strong class="text-primary">פעיל</span>';
                                                } else if (@$Supplier->status == '0') {
                                                    echo '<strong class="text-danger">מוקפא</span>';
                                                } ?></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right; font-weight: bold;">הרשאה:</td>
                                            <td dir="ltr"
                                                style="text-align: right;"><?php echo $AgentRules->Title ?? '--'; ?></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right; font-weight: bold;">טלפון:</td>
                                            <td dir="ltr" style="text-align: right;"><span
                                                        class="changeme"><?php echo $Supplier->ContactMobile; ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td dir="ltr" style="text-align: left; word-break: break-all;" colspan="2">
                                                <span class="changeme"><?php echo $Supplier->email; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right; font-weight: bold;">ת.הצטרפות:</td>
                                            <td dir="ltr"
                                                style="text-align: right;"><?php echo with(new DateTime(@$Supplier->joined))->format('d/m/Y'); ?></td>
                                        </tr>
                                        </tbody>

                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-md-2 col-sm-12 order-md-1" id="menusidebarborder">

                    <div class="card spacebottom">
                        <a data-toggle="collapse" href="#MenuCard" aria-expanded="true" aria-controls="MenuCard"
                           style="color: black;">
                            <div class="card-header text-right">
                                <strong><i class="fas fa-bars fa-fw"></i> <?php echo lang('rep_menu_agentprofile') ?></strong>
                            </div>
                        </a>

                        <div class="collapse show" id="MenuCard">

                            <div class="card-body">

                                <div class="nav nav-tabs flex-column nav-pills text-right" id="v-pills-tab"
                                     role="tablist" aria-orientation="vertical">
                                    <a class="nav-link text-dark active" data-toggle="pill" href="#user-overview"
                                       role="tab" aria-controls="v-pills-overview" aria-selected="true"><i
                                                class="fas fa-th fa-fw"></i> <?php echo lang('control_panel') ?></a>

                                    <?php if (@$Supplier->Coach == '1') { ?>
                                        <a class="nav-link text-dark" data-toggle="pill"
                                           data-target="#user-ClassHistory" href="#user-ClassHistory" role="tab"
                                           aria-controls="v-pills-task" aria-selected="false"><i
                                                    class="fas fa-history fa-fw"></i> <?php echo lang('customer_card_classes') ?></a>
                                    <?php } ?>


                                    <a class="nav-link text-dark" data-toggle="pill" data-target="#user-task"
                                       href="#user-task" role="tab" aria-controls="v-pills-task"
                                       aria-selected="false"><i class="fas fa-calendar-check fa-fw"></i> <?php echo lang('activities_agentprofile') ?></a>

                                    <div class="group">
                                        <a class="nav-link text-dark dropdown-toggle" id="btnGroupDrop1"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                           style="cursor: pointer;"><i class="fas fa-comments fa-fw"></i> <?php echo lang('messages') ?></a>
                                        <div class="dropdown-menu text-right dropdown-menu-right"
                                             aria-labelledby="btnGroupDrop1">
                                            <a class="dropdown-item" data-toggle="pill" href="#user-sendit" role="tab"
                                               aria-controls="v-pills-sendit" aria-selected="false"><i
                                                        class="fas fa-share-square fa-fw"></i> <?php echo lang('send_message') ?></a>
                                            <a class="dropdown-item" data-toggle="pill" href="#user-ArchiveMessage"
                                               role="tab" aria-controls="v-pills-archivsms" aria-selected="false"><i
                                                        class="fas fa-comments fa-fw"></i> <?php echo lang('archive_msg') ?></a>
                                        </div>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <a class="nav-link text-dark dropdown-toggle" id="btnGroupDrop1"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                           style="cursor: pointer;"><i class="fas fa-cogs fa-fw"></i> <?php echo lang('path_settings') ?> </a>
                                        <div class="dropdown-menu text-right dropdown-menu-right"
                                             aria-labelledby="btnGroupDrop1">
                                            <?php if ($CompanyNum != '100') { ?>
                                                <a class="dropdown-item" data-toggle="pill" href="#user-settings"
                                                   role="tab" aria-selected="false"><i class="far fa-edit fa-fw"></i>
                                                    <?php echo lang('edit_user_agentprofile') ?></a>
                                            <?php } ?>

                                            <a class="dropdown-item" data-toggle="pill" href="#user-Salary" role="tab"
                                               aria-selected="false"><i class="far fa-edit fa-fw"></i> <?php echo lang('wage_agentprofile') ?>
                                                </a>

                                            <a class="dropdown-item" data-toggle="pill" href="#user-imageprofile"
                                               role="tab" aria-selected="false"><i class="far fa-user-circle fa-fw"></i>
                                                <?php echo lang('upload_profile_image_app') ?></a>

                                            <a class="dropdown-item" data-toggle="pill" href="#user-files" role="tab"
                                               aria-selected="false" style="display: none;"><i
                                                        class="far fa-shekel-sign fa-fw"></i> <?php echo lang('file') ?></a>

                                            <a class="dropdown-item" data-toggle="pill" href="#user-log" role="tab"
                                               aria-selected="false"><i class="fas fa-bars fa-fw"></i> <?php echo lang('log_single') ?></a>
                                        </div>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>

                    <script>
                        $(document).click(function (event) {
                            if (!$(event.target).closest('.dropdown-toggle').length) {
                                $(".dropdown-item").removeClass("active");
                                $(".dropdown-item").removeClass("show");
                            }
                        });
                    </script>


                </div>
                <div class="col-md-7 col-sm-12 order-md-2">

                    <div class="tab-content">
                        <div class="tab-pane fade show active text-right" role="tabpanel" id="user-overview">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="fas fa-th"></i> <b><?php echo lang('control_panel') ?></b>
                                </div>
                                <div class="card-body">


                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade  text-right" role="tabpanel" id="user-Salary">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="fas fa-shekel-sign"></i> <b><?php echo lang('wage_agentprofile') ?></b>
                                </div>
                                <div class="card-body">

                                    <div class="row text-right" style="padding-bottom: 15px;padding-right: 30px;"
                                         align="right">
                                        <a href="#" data-ip-modal="#AddSalaryPopup" name="AddSalary"
                                           class="btn btn-primary text-white"><?php echo lang('wage_opt_agentprofile') ?></a>
                                    </div>


                                    <div class="row" style="padding-right: 15px;padding-left: 15px;">

                                        <table class="table dt-responsive text-right wrap ActivityTable" cellspacing="0"
                                               width="100%" id="ActivityTable">
                                            <thead class="thead-dark">
                                            <tr>
                                                <th align="right" style="text-align:right;">#</th>
                                                <th align="right" style="text-align:right;"><?php echo lang('type') ?></th>
                                                <th align="right" style="text-align:right;"><?php echo lang('start_date') ?></th>
                                                <th align="right" style="text-align:right;"><?php echo lang('classes') ?></th>
                                                <th align="right" style="text-align:right;"><?php echo lang('number_of_trainee_agentprofile') ?></th>
                                                <th align="right" style="text-align:right;"><?php echo lang('summary') ?></th>
                                                <th align="right" style="text-align:right;"><?php echo lang('status_table') ?></th>
                                                <th align="right" style="text-align:right;"><?php echo lang('actions') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            <?php
                                            $i = '1';
                                            $AgentSalarys = DB::table('coach_paymentstep')->where('CoachId', '=', $Supplier->id)->where('CompanyNum', '=', $CompanyNum)->orderBy('Status', 'ASC')->orderBy('id', 'ASC')->get();
                                            foreach ($AgentSalarys as $AgentSalary) {

                                                if ($AgentSalary->Type == '1') {
                                                    $AgentSalaryType = lang('time_clock');
                                                    $StatusNumClient = '0';
                                                    $StatusClassType = '0';
                                                } else if ($AgentSalary->Type == '2') {
                                                    $AgentSalaryType = lang('class_hours_agentprofile');
                                                    $StatusNumClient = '0';
                                                    $StatusClassType = '1';
                                                } else if ($AgentSalary->Type == '3') {
                                                    $AgentSalaryType = lang('number_of_trainee_agentprofile');
                                                    $StatusNumClient = '1';
                                                    $StatusClassType = '1';
                                                } else if ($AgentSalary->Type == '4') {
                                                    $AgentSalaryType = lang('reports_fixed_payroll');
                                                    $StatusNumClient = '0';
                                                    $StatusClassType = '1';
                                                }

                                                if ($AgentSalary->Status == '0') {
                                                    $AgentSalaryStatus = '<span class="text-primary">'.lang('active').'</span>';
                                                } else if ($AgentSalary->Status == '1') {
                                                    $AgentSalaryStatus = '<span class="text-danger">'.lang('not_active').'</span>';
                                                }


                                                if ($AgentSalary->ClassType == 'BA999') {
                                                    $SoftNames = lang('all_classes');
                                                } else {
                                                    $z = '1';
                                                    $myArray = explode(',', $AgentSalary->ClassType);
                                                    $SoftNames = '';
                                                    $SoftInfos = DB::table('class_type')->where('CompanyNum', '=', $CompanyNum)->whereIn('id', $myArray)->get();
                                                    $SoftCount = count($SoftInfos);

                                                    foreach ($SoftInfos as $SoftInfo) {

                                                        $SoftNames .= $SoftInfo->Type;

                                                        if ($SoftCount == $z) {
                                                        } else {
                                                            $SoftNames .= ', ';
                                                        }

                                                        ++$z;
                                                    }

                                                    $SoftNames = $SoftNames;

                                                }


                                                ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $AgentSalaryType; ?></td>
                                                    <td><?php echo with(new DateTime($AgentSalary->StartDate))->format('d/m/Y'); ?></td>
                                                    <td><?php if ($StatusClassType == '1') {
                                                            echo $SoftNames;
                                                        } else {
                                                        } ?></td>
                                                    <td><?php if ($StatusNumClient == '1') {
                                                            echo $AgentSalary->NumClient;
                                                        } else {
                                                        } ?></td>
                                                    <td><?php echo $AgentSalary->Amount; ?></td>
                                                    <td><?php echo $AgentSalaryStatus; ?></td>
                                                    <td><a class="btn btn-success btn-sm"
                                                           style="color: #FFFFFF !important;"
                                                           href='javascript:UpdateSalary("<?php echo $AgentSalary->id; ?>");'><?php echo lang('edit_wage_agentprofile') ?>
                                                            </a></td>
                                                </tr>
                                                <?php ++$i;
                                            } ?>

                                            </tbody>
                                        </table>

                                    </div>


                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade text-right" role="tabpanel" id="user-sendit">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="fas fa-share-square fa-fw"></i> <b><?php echo lang('send_message') ?></b>
                                </div>
                                <div class="card-body">

                                    <div class="alertb alert-info" style="font-size: 12px;">
                                        <strong><?php echo lang('option_to_use_params_inside_message') ?></strong><br>
                                        <strong>[[שם מלא]]</strong> <?php echo lang('changed_user_name_agentprofile') ?><br>
                                        <strong>[[שם פרטי]]</strong> <?php echo lang('change_first_name_agentprofile') ?><br>
                                        <strong>[[שם נציג מלא]]</strong> <?php echo lang('replace_full_name_agentprofile') ?><br>
                                        <strong>[[שם הנציג]]</strong> <?php echo lang('replace_send_user_agentprofile') ?><br>
                                        <strong>[[שם העסק]]</strong> <?php echo lang('replace_studio_name_agentprofile') ?>
                                    </div>


                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <form action="SendNotificationWorker" class="ajax-form clearfix"
                                                      autocomplete="off">
                                                    <input type="hidden" name="ClientId"
                                                           value="<?php echo $Supplier->id; ?>">
                                                    <input type="hidden" name="TypeSend" value="1">
                                                    <textarea name="Message" id="Message" class="form-control" rows="3"
                                                              required></textarea>


                                                    <div style="padding-top:10px;" align="right">
                                                        <button type="submit" name="submit" dir="rtl" id="SmsSubmit"
                                                                class="btn btn-dark text-white"><?php echo lang('sms_agentprofile') ?> <span
                                                                    dir="rtl" style="font-size: 12px;">(<span
                                                                        id="count"><?php echo lang('zero_characters') ?></span>)</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>

                                    </div>


                                    <hr>


                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">


                                            <form action="SendNotificationWorker" class="ajax-form clearfix">

                                                <input type="hidden" name="ClientId"
                                                       value="<?php echo $Supplier->id; ?>">
                                                <input type="hidden" name="TypeSend" value="2">


                                                <div class="form-group">
                                                    <input type="text" name="Subject" id="emailsubject"
                                                           placeholder="נושא" class="form-control">
                                                </div>

                                                <div class="form-group">
                                                    <textarea class="form-control summernote" id="emailmessage"
                                                              name="Message" placeholder="<?php echo lang('class_send_message') ?>"
                                                              rows="5"></textarea>
                                                </div>


                                                <button type="submit" class="btn btn-dark text-white"><?php echo lang('send_msg_email') ?>
                                                </button>
                                            </form>

                                        </div>

                                    </div>


                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" role="tabpanel" id="user-settings">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="far fa-edit fa-fw"></i> <strong><?php echo lang('edit_user_agentprofile') ?></strong>
                                </div>
                                <div class="card-body">


                                    <form action="edittech" class="ajax-form text-right" autocomplete="off">

                                        <input type="hidden" name="ClientId" value="<?php echo $_GET['u']; ?>">


                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><?php _e('main.FirstName') ?></label>
                                                    <input type="text" class="form-control" required name="FirstName"
                                                           value="<?php echo @$Supplier->FirstName; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><?php _e('main.LastName') ?></label>
                                                    <input type="text" class="form-control" required name="LastName"
                                                           value="<?php echo @$Supplier->LastName; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>טלפון סלולרי</label>
                                                    <input type="text" class="form-control" name="ContactMobile"
                                                           required pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" onkeypress='validate(event)'
                                                           <?php if (!$user->isOwner()) echo 'readonly'; ?>
                                                           value="<?php echo $Supplier->ContactMobile ?? ''; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>ת.ז.</label>
                                                    <input type="text" class="form-control" name="CompanyId"
                                                           onkeypress='validate(event)'
                                                           value="<?php echo $Supplier->CompanyId ?? ''; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>תאריך לידה</label>
                                                    <input name="Dob" type="date" class="form-control"
                                                           value="<?php echo @$Supplier->Dob; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>מין</label>
                                                    <select name="Gender" class="form-control">
                                                        <option value="1" <?php if (@$Supplier->Gender == '1') {
                                                            echo 'selected';
                                                        } ?>>זכר
                                                        </option>
                                                        <option value="2" <?php if (@$Supplier->Gender == '2') {
                                                            echo 'selected';
                                                        } ?>>נקבה
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>מאמן?</label>
                                                    <select name="Coach" class="form-control">
                                                        <option value="1" <?php if (@$Supplier->Coach == '1') {
                                                            echo 'selected';
                                                        } ?>>כן
                                                        </option>
                                                        <option value="0" <?php if (@$Supplier->Coach == '0') {
                                                            echo 'selected';
                                                        } ?>>לא
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <hr>


                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>טלפון במרכזיה</label>
                                                    <input name="AgentNumber" type="text" class="form-control"
                                                           onkeypress='validate(event)'
                                                           value="<?php echo @$Supplier->AgentNumber; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><?php echo lang('extension_number') ?></label>
                                                    <input name="AgentEXT" type="text" class="form-control"
                                                           onkeypress='validate(event)'
                                                           value="<?php echo @$Supplier->AgentEXT; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="form-group">
                                            <label><?php echo lang('status_table') ?></label>
                                            <select name="ActiveStatus" class="form-control">

                                                <option value="0" <?php echo @$Supplier->status == '0' ? 'selected' : '' ?> ><?= lang('freezed_status') ?>
                                                </option>
                                                <option value="1" <?php echo @$Supplier->status == '1' ? 'selected' : '' ?> ><?php echo lang('active') ?>
                                                </option>


                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <label><?php echo lang('email') ?></label>
                                            <input name="ContactEmail" type="text" class="form-control"
                                                   required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,4}$" id="ContactEmail" value="<?= $Supplier->email ?? '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo lang('reset_password') ?></label>
                                            <input name="Password" type="text" class="form-control" id="Password" 
                                                pattern="<?php echo PasswordHelper::PASSWORD_USER_REGEX; ?>" 
                                                title="<?php echo lang('password_requirement'); ?>">
                                        </div>

                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="pull-right" value="1" name="SendEmail">
                                                <?php echo lang('resend_login_agentprofile') ?>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label>הרשאה</label>
                                            <select name="role_id" class="form-control">
                                                <?php
                                                if ($Supplier->BrandsMain == '0') {
                                                    $AgentRules = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->orderBy('Title', 'ASC')->get();
                                                } else {
                                                    $AgentRules = DB::table('roles')->where('CompanyNum', '=', $Supplier->BrandsMain)->orderBy('Title', 'ASC')->get();
                                                }
                                                foreach ($AgentRules as $AgentRule) {
                                                    if (@$Supplier->role_id == $AgentRule->id) {
                                                        echo '<option value="' . $AgentRule->id . '" selected>' . $AgentRule->Title . '</option>';
                                                    } else {
                                                        echo '<option value="' . $AgentRule->id . '">' . $AgentRule->Title . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>


                                            <hr>

                                            <div class="form-group">
                                                <label><?php echo lang('user_info_agentprofile') ?></label>
                                                <textarea class="form-control summernote" name="About"
                                                          rows="5"><?php echo htmlentities($Supplier->About); ?></textarea>
                                            </div>


                                        </div>


                                        <div class="form-group">
                                            <button type="submit" name="submit"
                                                    class="btn btn-primary text-white btn-block"><?php _e('main.save_changes') ?></button>
                                        </div>
                                    </form>


                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" role="tabpanel" id="user-ArchiveMessage">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="fas fa-comments fa-fw"></i> ארכיון הודעות
                                </div>
                                <div class="card-body" id="SmsLogList">


                                    <?php
                                    $SmsLogList = DB::table('appnotification')
                                        ->where('ClientId', '=', $Supplier->id)
                                        ->where('CompanyNum', '=', $CompanyNum)
                                        ->where('System', '=', '1')
                                        ->where('Status', '=', '1')
                                        ->orderBy('Dates', 'DESC')
                                        ->limit(300)
                                        ->get();
                                    if (count($SmsLogList) == '0') {
                                        echo '<div dir="rtl" class="text-right">'.lang('no_archive_agentprofile').'</div>';
                                    } else {
                                        ?>
                                        <input style='position: relative;' class="form-control search" type="text"
                                               placeholder="<?php echo lang('search_button') ?>" dir="rtl">  <br>


                                        <ul class="timeline list">
                                            <?php


                                            $i = '1';
                                            foreach ($SmsLogList as $SmsLog) {
                                                @$UsersDB = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$SmsLog->UserId)->first();

                                                if ($SmsLog->Type == '0') {
                                                    $Iconsms = '<i class="fas fa-mobile-alt"></i>';
                                                } else if ($SmsLog->Type == '1') {
                                                    $Iconsms = '<i class="fas fa-phone"></i>';
                                                } else if ($SmsLog->Type == '2') {
                                                    $Iconsms = '<i class="fas fa-envelope-open"></i>';
                                                }
                                                ?>
                                                <li id="EmailLogLI<?php echo strip_tags(@$SmsLog->id); ?>">
                                                    <div class="timeline-panel" style="font-size: 12px;">
                                                        <div class="timeline-body" style="min-height: 60px;">
                                                            <div style="padding:10px;">
                                                                <div class="row">
                                                                    <div class="col-md-6 col-sm-12">
                                                                        <b><?php echo @$Iconsms; ?><?php echo @$SmsLog->Receiver; ?></b>
                                                                    </div>
                                                                    <div class="col-md-6 col-sm-12">
                                                                        <span class="float-left"><?php echo lang('serial_number') ?>: <?php echo @$SmsLog->id; ?>, <?php echo lang('counted_as') ?>-<?php echo @$SmsLog->Count; ?> <?php echo lang('messages') ?></span>
                                                                    </div>
                                                                </div>
                                                                <hr style="margin: 0;padding: 0;margin-top: 5px;margin-bottom: 5px;">
                                                                <?php echo @$SmsLog->Text; ?></div>
                                                        </div>

                                                        <div class="timeline-footer primary"
                                                             style="padding: 0;margin: 0;padding: 10px;">
                                                            <div class="row">
                                                                <div class="col-md-6 col-sm-12">
                                                                    <a class="pull-right"><?php echo @$UsersDB->display_name; ?></a>
                                                                </div>
                                                                <div class="col-md-6 col-sm-12">
                                                                    <a class="float-left"
                                                                       dir="ltr"><?php echo with(new DateTime($SmsLog->Date))->format('d/m/Y H:i'); ?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                                ++$i;
                                            }
                                            ?>


                                        </ul>
                                        <div dir="ltr">
                                            <nav>
                                                <ul class="pagination float-right">
                                                </ul>
                                            </nav>

                                        </div>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" role="tabpanel" id="user-log">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="fas fa-bars fa-fw"></i> <?php echo lang('log_single') ?>
                                </div>
                                <div class="card-body text-right" dir="rtl">

                                    <?php
                                    $SmsLogs = DB::table('log')
                                        ->where('UserId', '=', $Supplier->id)
                                        ->where('CompanyNum', '=', $CompanyNum)
                                        ->orderBy('Dates', 'DESC')
                                        ->limit(300)
                                        ->get();
                                    if (count($SmsLogs) == '0') {
                                        echo '<div dir="rtl" class="text-right">'.lang('no_log_data').'</div>';
                                    } else {
                                        ?>


                                        <table class="table table-bordered table-hover dt-responsive text-right wrap Log"
                                               dir="rtl" cellspacing="0" width="100%" id="AccountsTable">
                                            <thead class="thead-dark">
                                            <tr>
                                                <th style="text-align:right;">#</th>
                                                <th style="text-align:right;"><?php echo lang('contet_single') ?></th>
                                                <th style="text-align:right;"><?php echo lang('date') ?></th>
                                                <th style="text-align:right;"><?php echo lang('hour') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            <?php


                                            $i = '1';
                                            foreach ($SmsLogs as $SmsLog) {
                                                $UserNameLogs = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', $SmsLog->UserId)->first();

                                                ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo @$SmsLog->Text; ?></td>
                                                    <td dir="ltr"
                                                        style="text-align: right;"><?php echo with(new DateTime($SmsLog->Dates))->format('d/m/Y'); ?></td>
                                                    <td dir="ltr"
                                                        style="text-align: right;"><?php echo with(new DateTime($SmsLog->Dates))->format('H:i:s'); ?></td>
                                                </tr>
                                                <?php
                                                ++$i;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade show text-right" role="tabpanel" id="user-ClassHistory">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="fas fa-history"></i> <b> <?php echo lang('customer_card_classes') ?></b>
                                </div>
                                <div class="card-body" id="DivClassHistory">

                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade show text-right" role="tabpanel" id="user-imageprofile">
                            <div class="card spacebottom">
                                <div class="card-header text-right">
                                    <i class="fas fa-user-circle"></i> <b> <?php echo lang('profile_picture_main') ?></b>
                                </div>
                                <div class="card-body" id="DivClassHistory">


                                    <?php if ($Supplier->UploadImage == '') { ?>
                                        <img class="rounded-circle img-fluid profileimage"
                                             alt="<?php echo $Supplier->display_name; ?>" width="85" height="85"
                                             src="<?php echo 'https://ui-avatars.com/api/?name=' . $Supplier->LastName . '+' . $Supplier->FirstName . '&background=' . hexcode($Supplier->display_name) . '&color=ffffff&font-size=0.5'; ?>">
                                    <?php } else { ?>
                                        <img class="rounded-circle img-fluid profileimage"
                                             alt="<?php echo $Supplier->display_name; ?>" width="85" height="85"
                                             src="../camera/uploads/large/<?php echo $Supplier->UploadImage ?>">
                                    <?php } ?>

                                    <hr>

                                    <form action="../camera/uploadImage.php" id="FormuploadImage" method="post"
                                          autocomplete="off" enctype="multipart/form-data">
                                        <input type="hidden" name="recordsid" value="<?php echo $Supplier->id; ?>">
                                        <div class="page-login content-boxed content-boxed-padding no-top">
                                            <h4 class="vcard-title color-blue-dark"><?php echo lang('upload_profile_image_app') ?></h4>

                                            <label for="file-upload" class="custom-file-upload">
                                                <i class="fas fa-upload"></i> <?php echo lang('select_image_app') ?>
                                            </label>
                                            <input id="file-upload" required name="myfile" type="file"
                                                   accept="image/*"/>
                                            <button type="submit" id="SendFormUpload" class="btn btn-primary"
                                                    type="button" style="cursor: pointer;"><?php echo lang('save_changes_button') ?>
                                            </button>

                                        </div>
                                    </form>


                                </div>
                            </div>

                            <script>
                                $("#file-upload").change(function () {
                                    $("#FormuploadImage").submit();
                                    $('#SendFormUpload').trigger('click');

                                });

                            </script>


                        </div>


                    </div>

                </div>
            </div>


            </div></div>


            <!-- Add Contact -->
            <div class="ip-modal text-right" id="AddSalaryPopup" tabindex="-1">
                <div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
                    <div class="ip-modal-content">
                        <div class="ip-modal-header" dir="rtl">
                            <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true"
                               style="float:left;">&times;</a>
                            <h4 class="ip-modal-title"><?php echo lang('wage_set_agentprofile') ?></h4>

                        </div>
                        <div class="ip-modal-body" dir="rtl">
                            <form action="AddSalary" class="ajax-form clearfix">
                                <input type="hidden" name="CoachId" value="<?php echo $Supplier->id; ?>">


                                <div class="form-group" dir="rtl">
                                    <label>סוג חישוב</label>
                                    <select name="Salary" id="Salary" class="form-control">
                                        <option value="1"><?php echo lang('time_clock') ?></option>
                                        <option value="2"><?php echo lang('class_hours_agentprofile') ?></option>
                                        <option value="3" selected><?php echo lang('number_of_trainee_agentprofile') ?></option>
                                        <option value="4"><?php echo lang('reports_fixed_payroll') ?></option>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>תאריך תחילת החישוב <em><?php _e('main.required') ?></em></label>
                                    <input type="date" class="form-control" name="StartDate"
                                           min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
                                </div>

                                <div id="DivNumClient">
                                    <div class="form-group">
                                        <label><?php echo lang('starting_from_agentprofile') ?></label>
                                        <input type="number" min="1" class="form-control" name="NumClient" value="1">
                                    </div>
                                    <div class="alertb alert-info"><?php echo lang('step_participants_agentprofile') ?>
                                        <br>
                                        <?php echo lang('example_1_3_agentprofile') ?><br>
                                        <?php echo lang('plus_4_6_agentprofile') ?> <br>
                                    </div>
                                </div>

                                <div id="DivClassType">
                                    <div class="form-group">
                                        <label><?php echo lang('wage_type_class_agentprofile') ?></label>
                                        <select class="form-control js-example-basic-single select2multipleDesk text-right"
                                                name="ClassMemberType[]" id="ClassMemberType" dir="rtl"
                                                multiple="multiple" data-select2order="true" style="width: 100%;">
                                            <option value=""></option>
                                            <?php
                                            $SectionInfos = DB::table('class_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Type', 'ASC')->get();
                                            foreach ($SectionInfos as $SectionInfo) {
                                                ?>
                                                <option value="<?php echo $SectionInfo->id; ?>"><?php echo $SectionInfo->Type; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label><?php echo lang('define_amount_agentprofile') ?> <em><?php _e('main.required') ?></em></label>
                                    <input type="text" class="form-control" name="Amount" value="0"
                                           onkeypress="validate(event)">
                                </div>

                                <div class="form-group">
                                    <label><?php echo lang('wage_assistant_agentprofile') ?>
                                        <em><?php _e('main.required') ?></em></label>
                                    <input type="text" class="form-control" name="ExtraAmount" value="0"
                                           onkeypress="validate(event)">
                                </div>

                                <div id="DivLateCancel">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="pull-right" value="1" name="NoneShow"> <?php echo lang('count_charged_agentprofile') ?>
                                        </label>
                                    </div>

                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="pull-right" value="1" name="LateCancel"> <?php echo lang('count_late_cancel_agentprofile') ?>

                                        </label>
                                    </div>
                                </div>


                        </div>
                        <div class="ip-modal-footer">
                            <div class="ip-actions">
                                <button type="submit" name="submit"
                                        class="btn btn-success"><?php _e('main.save_changes') ?></button>
                            </div>

                            <button type="button" class="btn btn-default ip-close"
                                    data-dismiss="modal"><?php _e('main.close') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end Add Contact -->


            <div class="ip-modal" id="EditSalaryPopup" tabindex="-1">
                <div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
                    <div class="ip-modal-content text-right">
                        <div class="ip-modal-header" dir="rtl">
                            <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true"
                               style="float:left;">&times;</a>
                            <h4 class="ip-modal-title"><?php echo lang('edit_salary_agentprofile') ?></h4>

                        </div>
                        <div class="ip-modal-body" dir="rtl">
                            <form action="EditSalary" class="ajax-form clearfix">
                                <input type="hidden" name="ItemId">
                                <div id="result">


                                </div>

                        </div>
                        <div class="ip-modal-footer">
                            <div class="ip-actions">
                                <button type="submit" name="submit"
                                        class="btn btn-success"><?php _e('main.save_changes') ?></button>
                            </div>

                            <button type="button" class="btn btn-dark ip-close"
                                    data-dismiss="modal"><?php _e('main.close') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="ip-modal text-right" role="dialog" id="ViewDeskInfo" data-backdrop="static"
                 data-keyboard="false" aria-hidden="true">
                <div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
                    <div class="ip-modal-content">
                        <div class="ip-modal-header" <?php _e('main.rtl') ?>>
                            <a class="ip-close" title="Close" style="float:left;" data-dismiss="modal"
                               aria-label="Close">&times;</a>
                            <h4 class="ip-modal-title"></h4>

                        </div>
                        <div class="ip-modal-body">


                            <div id="DivViewDeskInfo">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <style>

                .select2-results__option[aria-selected=true] {
                    display: none;
                }


                input[type="file"] {
                    display: none;
                }

                .custom-file-upload {
                    width: 100%;
                    border: 1px solid #ccc;
                    display: inline-block;
                    padding: 6px 12px;
                    cursor: pointer;
                }
            </style>


            <script>
                $(document).ready(function () {

                    $('.summernote').summernote({
                        //    placeholder: 'הקלד תיאור לשיעור',
                        tabsize: 2,
                        height: 100,
                        toolbar: [
                            // [groupName, [list of button]]
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough']],
                            ['para', ['ul', 'ol']]
                        ]
                    });
                });


                $("#Salary").change(function () {

                    var Id = this.value;
                    if (Id == '1') {
                        DivNumClient.style.display = "none";
                        DivClassType.style.display = "none";
                        DivLateCancel.style.display = "none";
                    } else if (Id == '2') {
                        DivNumClient.style.display = "none";
                        DivClassType.style.display = "block";
                        DivLateCancel.style.display = "none";
                    } else if (Id == '3') {
                        DivNumClient.style.display = "block";
                        DivClassType.style.display = "block";
                        DivLateCancel.style.display = "block";
                    } else if (Id == '4') {
                        DivNumClient.style.display = "none";
                        DivClassType.style.display = "block";
                        DivLateCancel.style.display = "none";
                    }


                });


                $(function () {
                    var time = function () {
                        return '?' + new Date().getTime()
                    };

                    // Header setup
                    $('#AddSalaryPopup').imgPicker({});

                });

                $(".select2multipleDesk").select2({
                    theme: "bootstrap",
                    placeholder: lang('choose_class_type'),
                    'language': "he",
                    dir: "rtl"
                });

                $('#ClassMemberType').on('select2:select', function (e) {
                    var selected = $(this).val();

                    if (selected != null) {
                        if (selected.indexOf('BA999') >= 0) {
                            $(this).val('BA999').select2({
                                theme: "bootstrap",
                                placeholder: lang('choose_class_type'),
                                'language': "he",
                                dir: "rtl"
                            });
                        }
                    }

                });

                $('#ClassMemberType').on('select2:open', function () {
                    // get values of selected option
                    var values = $(this).val();
                    // get the pop up selection
                    var pop_up_selection = $('.select2-results__options');
                    if (values != null) {
                        // hide the selected values
                        pop_up_selection.find("li[aria-selected=true]").hide();

                    } else {
                        // show all the selection values
                        pop_up_selection.find("li[aria-selected=true]").show();
                    }

                });


                //שינוי עמוד בהתאם לטאב
                $('#newnavid a').click(function (e) {
                    e.preventDefault();
                    $(this).pill('show');
                    $('.tab-content > .tab-pane.active').jScrollPane();
                    scheduler.update_view();
                    $('html,body').scrollTop(0);
                });


                $("a").on("shown.bs.tab", function (e) {

                    var id = $(e.target).attr("href").substr(1);
                    window.location.hash = id;
//  scheduler.update_view();	
                    $('html,body').scrollTop(0);

                });


                // on load of the page: switch to the currently selected tab
                var hash = window.location.hash;
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
                //סיום שינוי עמוד בהתאם לטאב


                $("#Message").keyup(function () {
                    const LengthM = $(this).val().length;
                    const LengthT = Math.ceil(($(this).val().length) / <?php echo $SettingsInfo->SMSLimit; ?>);
                    $("#count").text(LengthM + lang('chars_divided_to') + ' ' + LengthT + ' ' + lang('messages'));
                });

                $(document).ready(function () {
                    $('.summernote').summernote({
                        placeholder: lang('type_message_content'),
                        tabsize: 2,
                        height: 153,
                        toolbar: [
                            // [groupName, [list of button]]
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough']],
                            ['para', ['ul', 'ol']]
                        ]
                    });


                    var ClassYear = '<?php echo date('Y'); ?>';
                    var ClassMonth = '<?php echo date('m'); ?>';
                    var ClientId = '<?php echo $Supplier->id; ?>';
                    var url = 'action/ClassHistoryCoach.php?ClassYear=' + ClassYear + '&ClassMonth=' + ClassMonth + '&ClientId=' + ClientId;
                    $('#DivClassHistory').empty();
                    $('#DivClassHistory').load(url, function () {
                    });


                });

                <?php if (count($SmsLogList) != '0') { ?>
                new List('SmsLogList', {
                    valueNames: ['timeline-panel'],
                    page: 10,
                    pagination: true
                });
                <?php } ?>

                $(document).ready(function () {

                    $('.Log').DataTable({
                        responsive: true,
                        "language": {
                            "processing": "מעבד...",
                            "lengthMenu": "הצג _MENU_ פריטים",
                            "zeroRecords": "לא נמצאו רשומות מתאימות",
                            "emptyTable": "לא נמצאו רשומות מתאימות",
                            "info": "_START_ עד _END_ מתוך _TOTAL_ רשומות",
                            "infoEmpty": "0 עד 0 מתוך 0 רשומות",
                            "infoFiltered": "(מסונן מסך _MAX_  רשומות)",
                            "infoPostFix": "",
                            "search": "חיפוש: ",
                            "url": "",
                            "paginate": {
                                "first": "ראשון",
                                "previous": "קודם",
                                "next": "הבא",
                                "last": "אחרון"
                            }
                        },

                        dom: "Bfrtip",
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
                                filename: lang('boostapp_user_log') + ` ` +  `<?php echo $Supplier->display_name; ?>`,
                                className: 'btn btn-dark'
                            },
                            {
                                extend: 'csvHtml5',
                                text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
                                filename: lang('boostapp_user_log') + ` ` + `<?php echo $Supplier->display_name; ?>`,
                                className: 'btn btn-dark'
                            },
                        ],
                    });


                });
            </script>


        <?php } ?>


    <?php else: ?>
        <?php redirect_to('index.php'); ?>
    <?php endif ?>

<?php endif ?>

<?php if (Auth::guest()): ?>

    <?php redirect_to('index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>