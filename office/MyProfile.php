<?php require_once '../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>

<?php
$pageTitle = lang('personal_settings');
require_once '../app/views/headernew.php';
require_once '../app/helpers/PasswordHelper.php';
?>


<?php if (Auth::check()): ?>

    <?php

    $AffID = Auth::user()->id;
    $AffName = Auth::user()->display_name;
    $Supplier = DB::table('users')->where('id', $AffID)->first();
    $user = User::find(Auth::user()->id);

    $LogUserId = Auth::user()->id;
    $LogUserName = Auth::user()->display_name;
    $LogDateTime = date('Y-m-d G:i:s');
    $LogContent = "<i class='fa fa-user' aria-hidden='true'></i> " . $LogUserName . " " . lang('entered_update_profile');
//    DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));

    $caption = [
        lang('attention_verify_phone'),
        lang('for_update')
    ];
    ?>

    <style>
        .card-header {
            cursor: pointer;
        }
    </style>
    <link href="<?php echo asset_url('css/vendor/imgpicker.css') ?>" rel="stylesheet">
    <link href="assets/css/fixstyle.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.5/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.5/quill.js"></script>


    <div class="row">

        <?php if (true == false) { ?>
        <div class="col-md-2 col-sm-12 order-md-1">
            <div class="card" style="margin-bottom: 20px;">
                <a data-toggle="collapse" href="#MenuSettings" aria-expanded="true" aria-controls="MenuSettings"
                   style="color: black;">
                    <div class="card-header text-start">
                        <strong><i class="fas fa-bars fa-fw"></i> <?= lang('settings_menu') ?></strong>
                    </div>
                </a>

                <div class="collapse show" id="MenuSettings">
                    <div class="card-body">
                        <div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist"
                             aria-orientation="vertical">
                            <a class="nav-link text-dark active" data-toggle="pill" href="#generalsettings" role="tab"
                               aria-controls="v-pills-generalsettings"
                               aria-selected="true"><?= lang('personal_details') ?></a>
                            <a class="nav-link text-dark" data-toggle="pill" href="#contactinfo" role="tab"
                               aria-controls="v-pills-contactinfo"
                               aria-selected="true"><?= lang('contact_details') ?></a>
                            <a class="nav-link text-dark" data-toggle="pill" href="#voicecenter" role="tab"
                               aria-controls="v-pills-voicecenter"
                               aria-selected="true"><?= lang('interfacing_center') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="col-md-12 col-sm-12 order-md-1">


            <div class="tab-content">


                <div class="tab-pane fade show active text-start" role="tabpanel" id="generalsettings">
                    <div class="card spacebottom">
                        <div class="card-header text-start"><strong><?= lang('personal_details') ?></strong></div>
                        <div class="card-body">


                            <form action="EditMyProfile" class="ajax-form clearfix" autocomplete="off"
                                  style="margin-bottom: 1px;">
                                <input type="hidden" name="AffId" value="<?php echo $AffID; ?>">
                                <input type="hidden" name="Type" value="1">

                                <div class="form-group">
                                    <label><?= lang('first_name') ?></label>
                                    <input type="text" class="form-control focus-me" name="FirstName" id="FirstName"
                                           value="<?php echo @$Supplier->FirstName ?>">
                                </div>

                                <div class="form-group">
                                    <label><?= lang('last_name') ?></label>
                                    <input type="text" class="form-control focus-me" name="LastName" id="LastName"
                                           value="<?php echo @$Supplier->LastName ?>">
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label><?= lang('phone') ?></label>
                                    <div class="d-flex w-100">
                                        <input name="ContactMobile" type="text"
                                               class="form-control mie-8 js-update-phone" id="ContactMobile"
                                               onkeypress='validate(event)' readonly
                                               data-original-value="<?php echo $Supplier->ContactMobile ?? 'Not Exist' ?>"
                                               value="<?php echo $Supplier->ContactMobile ?? '' ?>">
                                        <div class="input-group-prepend">
                                            <button onclick="$('#js-phone-valid-modal').modal('show')"
                                                    class="btn btn-outline-secondary" type="button">
                                                <?= lang('edit') ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><?= lang('email_table') ?></label>
                                    <input name="ContactEmail" type="text" class="form-control" id="ContactEmail"
                                           value="<?php echo @$Supplier->email ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label><?= lang('reset_password') ?></label>
                                    <input name="Password" type="text" class="form-control" id="Password"
                                        pattern="<?php echo PasswordHelper::PASSWORD_USER_REGEX; ?>" 
                                        title="<?php echo lang('password_requirement'); ?>">
                                </div>

                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="pull-right" value="1"
                                               name="SendEmail"> <?= lang('send_new_creds_to_mail') ?>

                                    </label>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <button type="submit"
                                            class="btn btn-primary text-white btn-lg"><?= lang('update') ?></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


                <div class="tab-pane fade text-start" role="tabpanel" id="contactinfo">
                    <div class="card spacebottom">
                        <div class="card-header text-start"><strong><?= lang('contact_details') ?></strong></div>
                        <div class="card-body">


                            <form action="EditMyProfile" class="ajax-form clearfix" autocomplete="off"
                                  style="margin-bottom: 1px;">
                                <input type="hidden" name="AffId" value="<?php echo $AffID; ?>">
                                <input type="hidden" name="Type" value="2">

                                <div class="form-group">
                                    <label><?= lang('phone_number_for_sms') ?></label>
                                    <input name="MobileSend" type="text" class="form-control" id="MobileSend"
                                           onkeypress='validate(event)' value="<?php echo @$Supplier->MobileSend ?>">
                                </div>

                                <div class="form-group">
                                    <label><?= lang('email_for_sending_mails') ?></label>
                                    <input name="EmailSend" type="text" class="form-control" id="EmailSend"
                                           value="<?php echo @$Supplier->EmailSend ?>">
                                </div>

                                <hr>
                                <div class="form-group">
                                    <button type="submit"
                                            class="btn btn-primary text-white btn-lg"><?= lang('update') ?></button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>


                <div class="tab-pane fade text-start" role="tabpanel" id="voicecenter">
                    <div class="card spacebottom">
                        <div class="card-header text-start"><strong><?= lang('interfacing_center') ?></strong></div>
                        <div class="card-body">


                            <form action="EditMyProfile" class="ajax-form clearfix" autocomplete="off"
                                  style="margin-bottom: 1px;">
                                <input type="hidden" name="AffId" value="<?php echo $AffID; ?>">
                                <input type="hidden" name="Type" value="3">

                                <div class="form-group">
                                    <label><?= lang('wired_phone_num_of_center') ?></label>
                                    <input name="AgentNumber" type="text" class="form-control" id="AgentNumber"
                                           onkeypress='validate(event)' value="<?php echo @$Supplier->AgentNumber ?>">
                                </div>

                                <div class="form-group">
                                    <label><?= lang('internal_branch_number') ?></label>
                                    <input name="AgentEXT" type="text" class="form-control" id="AgentEXT"
                                           value="<?php echo @$Supplier->AgentEXT ?>">
                                </div>

                                <hr>
                                <div class="form-group">
                                    <button type="submit"
                                            class="btn btn-primary text-white btn-lg"><?= lang('update') ?></button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <script>

        $('[data-toggle="tabajax"]').click(function (e) {
            var $this = $(this),
                loadurl = $this.attr('href'),
                targ = $this.attr('data-target');

            $.get(loadurl, function (data) {
                $(targ).html(data);
            });

            $this.tab('show');
            return false;
        });

        //שינוי עמוד בהתאם לטאב
        $('#newnavid a').click(function (e) {
            e.preventDefault();
            $(this).pill('show');
            $('.tab-content > .tab-pane.active').jScrollPane();
            $('html,body').scrollTop(0);
        });


        $("a").on("shown.bs.tab", function (e) {

            var id = $(e.target).attr("href").substr(1);
            window.location.hash = id;
            $('html,body').scrollTop(0);

        });


        // on load of the page: switch to the currently selected tab
        var hash = window.location.hash;
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
        //סיום שינוי עמוד בהתאם לטאב

        $(document).ready(function () {
            var windowWidth = $(window).width();
            if (windowWidth <= 1024) //for iPad & smaller devices
                $('#MenuSettings').removeClass('show');
            $('html,body').scrollTop(0);
        });

    </script>


<?php endif ?>


<?php require_once '../app/views/footernew.php'; ?>