<div class="modal fade" id="js-phone-valid-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content px-15 py-12 h-100">
            <div class="d-flex justify-content-end w-100">
                <a type="button" class="btn p-0" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times h4"></i>
                </a>
            </div>
            <div class="tab-content bsapp-max-w-300p bsapp-min-h-450p mx-auto h-100" id="myTabContent">
                <div class="tab-pane fade h-100" id="verifyFirstTab" role="tabpanel" aria-labelledby="first-tab">
                    <div class="d-flex flex-column align-items-center justify-content-between h-100 bsapp-min-h-450p">
                        <form class="h-100 d-flex flex-column justify-content-between" onsubmit="validationPopup.sendOtp(this, event)">
                            <div class="d-flex justify-content-center">
                                <h5 class="bsapp-fs-16 font-weight-bold text-center">
                                    <?= $caption[0] ?? lang('attention_soon_login') ?>
                                    <br>
                                    <?= $caption[1] ?? lang('wil_move_to_phone') ?>
                                </h5>
                            </div>
                            <div>
                                <div class="d-flex justify-content-center">
                                    <img class="bsapp-max-h-250p" src="/assets/img/svg/phone-signup.svg" alt="">
                                </div>
                                <div class="d-flex justify-content-center">
                                    <p class="text-center bsapp-fs-14" style="line-height: 1;">
                                        <?php if (!isset($caption)): ?>
                                            <?= lang('you_can_verify_now') ?>
                                            <br>
                                        <?php endif; ?>
                                        <?= lang('after_phone_approve_otp_sent') ?>
                                    </p>
                                </div>
                                <div class="d-flex justify-content-center" dir="ltr">
                                    <div class="input-group mb-3 border" style="border-collapse: unset;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-light text-muted bsapp-fs-14 pie-10" id="basic-addon1">+972</span>
                                        </div>
                                        <input type="tel" minlength="9" maxlength="10"
                                               pattern="0?[5]{1}[0-9]{8}$" required onkeyup="validationPopup.checkPhoneNumber(this)"
                                               class="font-weight-bold text-left bg-light border-light form-control bsapp-fs-14"
                                               aria-label="Phone" aria-describedby="basic-addon1"
                                               style="letter-spacing: 13px; box-shadow: none;">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center w-100 mt-30 mb-10">
                                <button class="btn btn-primary btn-block bsapp-fs-16">
                                    <?= lang('approval') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade h-100" id="verifySecondTab" role="tabpanel" aria-labelledby="second-tab">
                    <div class="d-flex flex-column align-items-center justify-content-between h-100 bsapp-min-h-450p">
                        <form class="h-100 d-flex flex-column justify-content-between" onsubmit="validationPopup.validateOtp(this, event)">
                            <div class="d-flex justify-content-center">
                                <h5 class="bsapp-fs-16 font-weight-bold text-center">
                                    <?= lang('we_sent_otp') ?>
                                    <br>
                                    <?= lang('type_otp_here') ?>
                                </h5>
                            </div>

                            <div>
                                <div class="d-flex justify-content-center">
                                    <img class="bsapp-max-h-250p" src="/assets/img/shield.gif" alt="">
                                </div>

                                <div class="d-flex justify-content-center mb-10" dir="rtl">
                                    <div class="input-group mb-3">
                                        <input min="100000" minlength="999999" required
                                               class="font-weight-bold text-center bg-light border-light form-control bsapp-fs-16 pl-30"
                                               type="number" aria-label="otp" onkeydown="$(this).removeClass('border-danger')" style="box-shadow: none; letter-spacing: 20px;"
                                               autocomplete="one-time-code">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <p class="text-center bsapp-fs-14" style="line-height: 1;">
                                        <?= lang('didnt_recieve_otp_question') ?>
                                        <a role="button" id="js-resend-otp"
                                           onclick="validationPopup.resendOtp()" class="text-secondary btn-link disabled"><u>שלח שוב</u></a>
                                    </p>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <p class="text-center bsapp-fs-14" style="line-height: 1;">
                                        <?= lang('send_new_code_in') ?>
                                        <span id="countdown">30</span>
                                        <?= lang('seconds_lower') ?>
                                    </p>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <p class="text-center bsapp-fs-14" style="line-height: 1;">
                                        <span id="js-phone-placeholder"></span>
                                        <a role="button" class="text-primary cursor-pointer" onclick="$('#verifyFirstTab').showTab()">
                                            <u><?= lang('edit_number') ?></u>
                                        </a>
                                    </p>
                                </div>

                            </div>

                            <div class="d-flex justify-content-center w-100 mt-30 mb-10">
                                <button class="btn btn-primary btn-block bsapp-fs-16">
                                    <?= lang('approval') ?>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="tab-pane fade h-100" id="verifyThirdTab" role="tabpanel" aria-labelledby="third-tab">
                    <div class="d-flex flex-column align-items-center justify-content-around h-100 pb-40 bsapp-min-h-450p">
                        <div>
                            <img class="bsapp-max-h-200p" src="/assets/img/shield-success.gif">
                            <div class="d-flex justify-content-center">
                                <h5 class="bsapp-fs-18 font-rubik-sans font-weight-bold text-center">
                                    <?= lang('thanks_exclam') ?>
                                    <br>
                                    <?= lang('verification_success') ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #js-phone-valid-modal input::-webkit-outer-spin-button,
    #js-phone-valid-modal input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    #js-phone-valid-modalv input[type=number] {
        -moz-appearance:textfield; /* Firefox */
    }
</style>

<script>
    let validationPopup = {
        intervalCounter: 0,
        currentInterval: null,
        postData: {
            url: '/office/ajax/login/otp.php',
            method: 'POST'
        },
        sendOtp: function (form, e) {
            e.preventDefault()
            let phone = $(form).find('input').val()
            $.ajax({
                ...this.postData,
                data: {
                    action: 'sendOtp',
                    phone: phone,
                },
                success: function (res) {
                    $('#verifySecondTab').showTab();
                    $('#js-phone-placeholder').text(phone)
                    validationPopup.startCountdown()
                }
            })
        },
        resendOtp: function () {
            this.startCountdown()
            $('#js-resend-otp').addClass('disabled text-secondary').removeClass('text-primary')
            let phone = $('#js-phone-placeholder').text()
            $.ajax({
                ...this.postData,
                data: {
                    action: 'sendOtp',
                    phone: phone,
                },
                success: function (res) {
                    if (res.status) {
                        $.notify(
                            {message: 'קוד אימות נשלח פעם נוספת'},
                            {
                                type: 'info',
                                z_index: '99999999'})
                    } else {
                        $.notify({message: res.message},
                            {
                                type: 'danger',
                                z_index: '99999999'})
                    }

                }
            })
        },
        validateOtp: function (form, e){
            e.preventDefault()
            let otpInput = $(form).find('input')
            $.ajax({
                ...this.postData,
                data: {
                    action: 'validateOtp',
                    otp: otpInput.val(),
                },
                success: function (res) {
                    console.log(res);
                    if (res.status) {
                        clearInterval(validationPopup.currentInterval)
                        if (res.data.phone)
                            $('.js-update-phone').val(res.data.phone)
                        $('#verifyThirdTab').showTab()
                    } else {
                        otpInput.removeClass('border-light').addClass('border-danger')
                        $.notify({
                            message: 'שגיאה בקוד אימות'
                        }, {
                            type: 'danger',
                            z_index: '99999999'
                        });
                    }
                }
            })
        },
        checkPhoneNumber: function (elem){
            if ($(elem).isValid())
                $(elem).closest('div').addClass('border-success')
            else
                $(elem).closest('div').removeClass('border-success')
        },
        startCountdown: function (){
            let timeLeft = 30;
            const $countdown = $('#countdown')
            $countdown.text(timeLeft)
            $countdown.closest('p').removeClass('invisible')
            clearInterval(this.currentInterval)
            this.currentInterval = setInterval(function(){
                if (!$('#verifySecondTab').hasClass('active') || !$('#js-phone-valid-modal').is(':visible'))
                    clearInterval(validationPopup.currentInterval)
                timeLeft--
                $countdown.text(timeLeft)
                if(timeLeft <= 0) {
                    clearInterval(validationPopup.currentInterval)
                    $countdown.closest('p').addClass('invisible')
                    $('#js-resend-otp').removeClass('disabled text-secondary').addClass('text-primary')
                }
            },1000);
        },
    }

    $.fn.showTab = function () {
        $(this).siblings().removeClass('show')
        $(this).siblings().removeClass('active');
        $(this).tab('show');
    }
    $.fn.isValid = function(){
        return this[0].checkValidity()
    }

    $(document).ready(function (){
        // $('#js-phone-valid-modal').modal('show');
        $('#verifyFirstTab').showTab()
    })
</script>
