const InsuranceWindow = {
    $bsWizard: null,
    step: 0,
    completed: null,
    init: function () {
        this.getInsuranceData();
        /*  $("body").on("click", ".js-label-radios", function () {
         InsuranceWindow.submitInsuranceOfferForm();
         });
         */
    },
    validate: function (elem) {
        if ($(elem).prop("checked") == true) {
            if ($(".js-label-radios input:checked").length == 2) {
                $(".js-insurance-next").removeClass("disabled");
            }
        }
        InsuranceWindow.submitInsuranceOfferForm();
    },
    initFormWizard: function () {
        this.$bsWizard = $('.wizard-card').bootstrapWizard({
            'tabClass': 'nav nav-pills',
            'nextSelector': '.js-insurance-next',
            'previousSelector': '.js-insurance-back',
            onNext: function (tab, navigation, index) {

                InsuranceWindow.step = index + 1;
                if (index == 1) {
                    InsuranceWindow.submitInsuranceOfferForm();
                }
                if (index == 3) {
                    if ($("#js-required-check").prop("checked") == false) {
                        $("#js-required-check").parents(".custom-control").addClass("bsapp-js-required-check");
                        return false;
                    } else {
                        $("#js-required-check").parents(".custom-control").removeClass("bsapp-js-required-check");
                    }
                    if (InsuranceWindow.completed == null) {
                        InsuranceWindow.submitInsuranceOfferForm(true);
                        return false;
                    }
                    const player = document.getElementById("js-checkmark-circle");
                    player.load($(player).attr("data-src"))
                }
                InsuranceWindow.checkStatus(index);
            },
            onFirst: function (tab, navigation, index) {
                InsuranceWindow.checkStatus(index);
            },
            onPrevious: function (tab, navigation, index) {
                if (index < 0) {
                    return false;
                }
                InsuranceWindow.checkStatus(index);
            },
            onInit: function (tab, navigation, index) {
                InsuranceWindow.checkStatus(index);
                //check number of tabs and fill the entire row
                var $total = navigation.find('li').length;
                $width = 100 / $total;
                navigation.find('li').css('width', $width + '%');
            },
            onTabClick: function (tab, navigation, index) {
            },
            onTabShow: function (tab, navigation, index) {
                const $total = navigation.find('li').length;
                const $current = index + 1;
                const $wizard = navigation.closest('.wizard-card');
                //update progress
                let move_distance = 100 / $total;
                move_distance = move_distance * (index) + move_distance / 2;
                $wizard.find($('.progress-bar')).css({width: move_distance + '%'}); //e.relatedTarget // previous tab
                $('.wizard-card .nav-pills li a.active').parents("li").prevAll().addClass("bsapp-step-done");
                $('.wizard-card .nav-pills li a.active').parents("li").removeClass("bsapp-step-done").nextAll().removeClass("bsapp-step-done");
            }
        });
    },
    checkStatus: function (index) {
        $(".js-insurance-next").find('span[data-context]').hide();
        $(".js-insurance-back").find('span[data-context]').hide();
        $(".js-insurance-next").removeClass("disabled");
        if (index == 0) {
            $(".js-insurance-next").show();
            $(".js-insurance-next").find('span[data-context="js-step-1"]').show();
            $(".js-insurance-back").find('span[data-context="js-step-1"]').show();
        }
        if (index == 1) {
            if ($(".js-label-radios input:checked").length < 2) {
                $(".js-insurance-next").addClass("disabled");
            }
            $(".js-insurance-next").show();
            $(".js-insurance-next").find('span[data-context="js-step-2"]').show();
            $(".js-insurance-back").find('span[data-context="js-step-2"]').show();
        }
        if (index == 2) {
            $(".js-insurance-next").show();
            $(".js-insurance-next").find('span[data-context="js-step-3"]').show();
            $(".js-insurance-back").find('span[data-context="js-step-3"]').show();
        }
        if (index == 3) {
            $(".js-insurance-next").hide();
            $(".js-insurance-back").find('span[data-context="js-step-4"]').show();
        }
    },
    submitInsuranceOfferForm: function (submit = null) {
        const $loader = $(".js-loading-spinning").html();
        if(!submit) {
            $(".js-insurance-proposal-amount").html($loader);
        }
        const formdata = new FormData($("#js-wizard-insurance")[0]);
        formdata.append("form_step", InsuranceWindow.step);
        if (submit == true) {
            //const mobRegex = new RegExp(/^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}(-){0,1}[0-9]{7}$/);
            const mobRegex = new RegExp(/^(\+\d{1,3}[- ]?)?\d{10}$/);
            const emailRegex = new RegExp(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
            if (mobRegex.test($("#js-insurance-phone").val())) {
                $("#js-insurance-phone+small").html('');
            } else {
                $("#js-insurance-phone+small").html('Invalid Phone Number');
                return;
            }
            if (emailRegex.test($("#js-insurance-email").val())) {
                $("#js-insurance-email+small").html('');
            } else {
                $("#js-insurance-email+small").html('Invalid Email Address');
                return;
            }
            formdata.append("form_submit", "completed");
            // const $loader = $(".js-loading-spinning").html();
            $(".js-insurance-next").find('span[data-context="js-step-3"]').html($loader);
        }
        $("#js-insurance-phone+small").html('');
        $("#js-step-3 .js-error-on-submit").addClass('d-none');
        $.ajax({
            url: '/office/ajax/insurance/insurance-logic.php',
            data: formdata,
            type: "POST",
            contentType: false,
            processData: false,
            success: function (response) {
                const obj = $.parseJSON(response);
                if(typeof obj.data !== 'undefined') {
                    $(".js-insurance-proposal-amount").html(obj.data + '<span class="font-weight-normal">₪</span>');
                }
                if (obj.status == 0) {
                    if(obj.mobile && obj.mobile == 1) {
                        $("#js-insurance-phone+small").html(obj.msg);
                        InsuranceWindow.completed = null;
                    } else {
                        console.log(obj.msg);
                        $("#js-step-3 .js-error-on-submit label").text(lang('error_detected_cal')).removeClass('d-none');
                    }
                    $(".js-insurance-next").find('span[data-context="js-step-3"]').html(lang('send'));
                    return;
                }
                if (obj.msg == "COMPLETED") {
                    InsuranceWindow.completed = true;
                    InsuranceWindow.$bsWizard.bootstrapWizard('next');
                }
                $(".js-insurance-next").find('span[data-context="js-step-3"]').html(lang('send'));
            },
            error: function (response) {
                console.log(response.msg);
                $("#js-step-3 .js-error-on-submit label").text(lang('error_detected_cal')).removeClass('d-none');
                $(".js-insurance-next").find('span[data-context="js-step-3"]').html(lang('send'));
            }
        });
    },
    resetForms: function () {
        $("#js-wizard-insurance").trigger("reset");
    },
    getInsuranceData: function () {

        const $loader = $(".js-modal-insurance-loader").html();
        $("#js-modal-insurance .modal-body").html($loader);
        $("#js-modal-insurance").modal("show");
        const formdata = new FormData();
        formdata.append("load_view", "yes");
        formdata.append("form_step", 1);
        $.ajax({
            url: '/office/ajax/insurance/insurance-logic.php',
            data: formdata,
            type: "POST",
            processData: false,
            contentType: false,
            success: function (response) {
                $("#js-modal-insurance .modal-body").html(response);
                InsuranceWindow.step = 1;
                InsuranceWindow.completed = null
                // $('[name="insurance[start_date]"]').removeClass("border-light").addClass("border-success");
            },
            error: function (response) {
                console.log(response.msg);
                $("#js-step-3 .js-error-on-submit label").text(lang('error_detected_cal')).parent().removeClass('d-none');
            }
        });
    }
};
$(document).ready(function () {
    // InsuranceWindow.init();
});