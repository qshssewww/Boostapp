<?php
require_once "../../../app/init.php";
?>
<script>
    jQuery(function() {
        // default
        $("#js-ca-radio-1").prop("checked", true);
        $('[data-context="js-ca-radio-1"]').showFlex();
        $(".showOption").removeClass("d-block").addClass("d-none");

        // js-cancel-action-modal :: begin
        $("body").on("click", "[name='cancel_action_radio']", function () {
            var id = $(this).attr("id");
            $('[data-context="' + id + '"]').showFlex();
            if (id == "js-ca-radio-1") {
                $('[data-context="js-ca-radio-2"]').hideFlex();
            }
            if (id == "js-ca-radio-2") {
                $('[data-context="js-ca-radio-1"]').hideFlex();
            }
        })

        $("#js-series-dropdown").on("select2:selecting", function (e) {
            var val = e.params.args.data.id;
            console.log(val)
            $('[data-context="' + val + '"]').showFlex();
            if (val == "js-series-option-1") {
                $('[data-context="js-series-option-3"]').hideFlex();
                $('[data-context="js-series-option-2"]').hideFlex();
            }
            if (val == "js-series-option-2") {
                $('[data-context="js-series-option-3"]').hideFlex();
            }
            if (val == "js-series-option-3") {
                $('[data-context="js-series-option-2"]').hideFlex();
            }
        })
        // js-cancel-action-modal :: end

        $(".js-select-custom2").select2({
            minimumResultsForSearch: -1,
            theme: "bsapp-dropdown"
        });

        $(".js-select-message2").select2({
            minimumResultsForSearch: -1,
            theme: "bsapp-dropdown"
        });

        $(".chooseOption").on('change', function () {
            if ($(this).val() == "2") {
                $(".showOption").addClass("d-block");
                $(".showOption").removeClass("d-none");
            } else {
                $(".showOption").removeClass("d-block");
                $(".showOption").addClass("d-none");
            }
        });
    });
</script>
<div class="d-flex justify-content-between w-100 mb-20">
    <h5 class="bsapp-fs-20 p-15"><?php echo lang('cancel_lesson') ?> </h5>
    <a href="javascript:;" class="text-dark p-15" data-dismiss="modal"><i class="fal fa-times h4"></i></a>
</div>
<div class=" mb-15 px-15 ">
    <?php echo lang('class_cancel_notice') ?>
</div>
<div class="px-15" style="height: calc( 100% - 120px );">
    <div class="d-flex flex-column justify-content-between h-100 ">
        <div class="bsapp-max-h-300p bsapp-min-h-300p bsapp-scroll overflow-auto mb-15">
            <div class="form-group mb-30">
                <label class="mb-15"><?php echo lang('desk_how_delete') ?> </label>
                <div class="pis-15">
                    <div class="custom-control custom-radio mb-15">
                        <input type="radio" id="js-ca-radio-1" name="cancel_action_radio" class="custom-control-input" data-freq="single">
                        <label class="custom-control-label" for="js-ca-radio-1"><?php echo lang('one_time_payment') ?> </label>
                    </div>
                    <div class="custom-control custom-radio mb-15">
                        <input type="radio" id="js-ca-radio-2" name="cancel_action_radio" class="custom-control-input" data-item-event="radio" data-item-class="" data-item-show="" data-freq="multi">
                        <label class="custom-control-label" for="js-ca-radio-2"><?php echo lang('delete_class_series') ?> </label>
                    </div>
                </div>
            </div>
            <div class="form-group flex-column d-none" data-context="js-ca-radio-1">
<!--                <label class="mb-15">--><?php //echo lang('view_as_canceled') ?><!--</label>-->
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="show-in-cal-switch" checked="true">
                    <label class="custom-control-label" for="show-in-cal-switch"> <?php echo lang('view_as_canceled') ?> </label>
                </div>
            </div>

            <div class="form-group  flex-column   d-none" data-context="js-ca-radio-2">
                <label class="mb-15"><?php echo lang('series_delete_options') ?></label>
                <div class="mb-15 bsapp-max-w-200p">
                    <select class="js-select-custom2" id="js-series-dropdown">
                        <option data-amount="all" value="js-series-option-1"><?php echo lang('all_classes') ?></option>
                        <option data-amount="dates" value="js-series-option-2"><?php echo lang('in_date_range') ?></option>
                        <option data-amount="quantity" value="js-series-option-3"><?php echo lang('number_of_shows') ?></option>
                    </select>
                </div>
                <div class="form-group  align-items-center mb-15  d-none" data-context='js-series-option-2'>
                    <span class="mie-8"> <?php echo lang('coupon_from') ?></span> <input id="js-cancel-date-since" type="date" class="form-control js-datepicker  bg-light  bsapp-max-w-150p  border-light  mie-8">
                    <span class="mie-8"> <?php echo lang('coupon_till') ?></span> <input id="js-cancel-date-until" type="date" class="form-control bsapp-max-w-150p mie-8  bg-light border-light js-datepicker ">
                </div>

                <div class="form-group   align-items-center mb-15  d-none" data-context='js-series-option-3'>
                    <span class="mie-8"> <?php echo lang('canel_of_desk') ?> </span> <input id="js-cancel-quantity" type="text" class="form-control bg-light  bsapp-max-w-60p  border-light  mie-8 px-6"><span class="mie-8"> <?php echo lang('future_shows_desk') ?> </span>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end align-items-end h-100 form-group mb-20">
            <a class="btn btn-outline-dark border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10 close-modal" href="javascript:;"><?php echo lang('action_cacnel') ?></a>
            <a class="btn btn-primary border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10" id="js-submit-cancel-class" href="javascript:;"><?php echo lang('save') ?></a>
        </div>
    </div>
</div>