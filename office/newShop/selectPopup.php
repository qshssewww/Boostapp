<div class="popupWrapper" id="selectPopup">
    <div class="popupContainer smPopup scaleUp">
        <!-- modal header start -->
        <div class="generalPopupHeader mt-3 mb-5">
            <h4 class="generalPopupTitle" ><?php echo lang('choose_membership_type') ?></h4>
            <button type="button"  class="close newCalendarPopupCloseTimes toggleClosePopup" data-target="selectPopup" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- modal header end -->
        <!-- modal body start -->
        <div class="text-start container overflow-auto">        
            <select name="select-type" id="select-type" class="w-100" style="display:none;">
                <option value="0"><?php echo lang('choose_type') ?></option>
                <option value="1"><?php echo lang('new_subscription') ?></option>
                <option style="display: none" value="2"><?php echo lang('product') ?></option>
                <option style="display: none" value="3"><?php echo lang('payment_pages') ?></option>
                <option value="4"><?php echo lang('class_tabe_card') ?></option>
                <option value="5"><?php echo lang('a_trial') ?></option>
            </select>
            <div class="">
                <ul class="list-group list-group-flush px-0 my-20 cursor-pointer">
                    <li class="list-group-item d-flex justify-content-between px-0 hover-input" id="CreateMemberships"
                        onclick="createClubMemberships.openPopup()">
                        <div><i class="fal fa-receipt"></i> <?php echo lang('add_new_subscription') ?></div>
                        <div class="custom-control custom-radio">
                            <label class="badge badge-white text-primary bsapp-fs-14 bsapp-lh-17 p-0 mis-10" ><?= lang('new') ?></label>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 hover-input" id="CreateTrialMemberships"
                        onclick="createClubMemberships.openPopup(true)">
                        <div><i class="fal fa-receipt"></i> <?php echo lang('add_new_trial') ?></div>
                        <div class="custom-control custom-radio">
                            <label class="badge badge-white text-primary bsapp-fs-14 bsapp-lh-17 p-0 mis-10" ><?= lang('new') ?></label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- modal content end -->
    </div>

</div>

