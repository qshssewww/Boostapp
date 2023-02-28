<?php
require_once '../app/initcron.php';
require_once 'Classes/Item.php';

$CompanyNum = Auth::user()->CompanyNum;

$option = $_GET['option'] ?? 'client';
$MainDiv = 'js-client-popup';
?>

<!-- new client/lead modal :: begin -->
<form class="modal-body d-flex flex-column justify-content-between p-0 h-100" id="addClientPopupForm" method="post">
    <div class="js-subpage-home h-100">
        <!--    header    -->
        <div class="d-flex justify-content-between align-items-center  border-bottom border-light">
            <div class="w-150p px-15 py-15">
                <span class="bsapp-fs-18 font-weight-bold"><?= lang($option == 'client' ? 'new_client' : 'a_new_lead') ?></span>
            </div>

            <a href="javascript:;" class="text-dark bsapp-fs-20 p-15 font-weight-bold" data-dismiss="modal">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <div class="bsapp-scroll overflow-auto bsapp-newclient-middle-height">
            <!--    name line    -->
            <div class="d-flex px-15 mt-20">
                <div class="form-group flex-fill mb-10 mie-15">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('first_name') ?></label>
                    <div class="is-invalid-container">
                        <input name="FirstName" id="adult_first_name" maxlength="60"
                               placeholder="<?= lang('first_name') ?>"
                               class="form-control border-light" type="text" required autocomplete="off"
                               oninvalid="this.setCustomValidity('<?= lang('first_name_req_field') ?>')"
                               oninput="this.setCustomValidity('')">
                    </div>
                </div>
                <div class="form-group flex-fill mb-10">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('last_name') ?></label>
                    <div class="is-invalid-container">
                        <input name="LastName" id="adult_last_name" maxlength="60"
                               placeholder="<?= lang('last_name') ?>"
                               class="form-control border-light" type="text" required autocomplete="off"
                               oninvalid="this.setCustomValidity('<?= lang('last_name_req_field') ?>')"
                               oninput="this.setCustomValidity('')">
                    </div>
                </div>
            </div>
            <!--    phone line    -->
            <div class="d-flex px-15 align-items-end">
                <div class="form-group flex-fill mb-10 mie-15 w-100">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('mobile_number') ?></label>
                    <select class="form-control" id="adult_phone" required
                            pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$"
                            oninvalid="this.setCustomValidity('<?= lang('phone_req_field') ?>')"
                            oninput="this.setCustomValidity('')">
                        <!--    select2 search    -->
                    </select>
                </div>
                <div class="form-group flex-fill mb-10 w-125p">
                    <select name="areaCode" id="adult_phone_zone" class="form-control bg-light border-light" disabled>
                        <option value="+972" selected>&lrm;+972</option>
                        <option value="+91">&lrm;+91</option>
                        <option value="+1">&lrm;+1</option>
                        <option value="+44">&lrm;+44</option>
                    </select>
                </div>
            </div>
            <!--    email line    -->
            <div class="d-flex px-15">
                <div class="form-group flex-fill mb-10">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('email_table') ?></label>
                    <input name="Email" type="text" class="form-control border-light text-right"
                           placeholder="example@gmail.com"
                           id="email" lang="en" dir="ltr" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,4}$"
                           title="<?php echo lang('woring_email') ?>">
                </div>
            </div>
            <!--    branch line    -->
            <?php
            $b = '1';
            $BrandList = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('id', 'ASC')->get();
            ?>
            <div class="<?php echo(empty($BrandList) || sizeof($BrandList) == 1 ? "d-none" : "d-flex") ?> px-15">
                <div class="form-group flex-fill mb-10">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('branch') ?></label>
                    <select class="form-control border-light" name="Brands" id="brands">
                        <?php
                        if (!empty($BrandList)) {
                            foreach ($BrandList as $ClassType) { ?>
                                <option value="<?php echo $ClassType->id; ?>" <?php if ($b == '1') echo 'selected'; ?>>
                                    <?php echo $ClassType->BrandName ?></option>
                                <?php ++$b;
                            }
                        } else { ?>
                            <option value="0" selected><?= lang('primary_branch') ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <!--    membership line    -->
            <?php
            $Memberships4Client = (new Item())->getCompanyItemsByDepartmentArray($CompanyNum,
                $option == 'lead' ? [3] : [1, 2]); ?>

            <div class="d-<?= empty($Memberships4Client) ? 'none' : 'flex'; ?> px-15 align-items-end">
                <div class="form-group flex-fill mb-10 mie-15 w-100">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('customer_card_membership') ?>
                        <span class="text-minor d-none"
                              id="minorMembershipLabel"><?= lang('minor') ?></span></label>
                    <select name="selectMembership" id="select_membership" class="form-control text-start"
                            data-live-search="true">
                        <option value="-1" price="0.00" selected><?php echo lang('without') ?></option>
                        <?php foreach ($Memberships4Client as $item) { ?>
                            <option value="<?php echo $item->id; ?>"
                                    price="<?php echo $item->ItemPrice; ?>"><?php echo $item->ItemName; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group flex-fill mb-10 w-125p">
                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('price') ?></label>
                    <input name="MembershipPrice" type="number" dir="ltr" id="membership_price"
                           class="form-control bg-light border-light text-right" required value="0.00" disabled
                           oninvalid="this.setCustomValidity('<?= lang('membership_price_req_field') ?>')"
                           oninput="this.setCustomValidity('')">
                </div>
            </div>

            <hr>
            <!--    MINOR    -->
            <div class="d-flex px-15">
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" id="is_minor" name="minorCheckbox">
                    <label class="custom-control-label" for="is_minor"><?php echo lang('fill_for_minor') ?></label>
                </div>
            </div>
            <div id="minor-div" class="flex-column d-flex" style="display: none">
                <!--    minor name line    -->
                <div class="d-flex px-15 mt-20">
                    <div class="form-group flex-fill mb-10 mie-15">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('first_name') ?> <span
                                    class="text-minor"><?= lang('minor') ?></span></label>
                        <div class="is-invalid-container">
                            <input name="FirstName" id="first_name" maxlength="60"
                                   placeholder="<?= lang('first_name') ?>"
                                   class="form-control border-light" type="text" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group flex-fill mb-10">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('last_name') ?> <span
                                    class="text-minor"><?= lang('minor') ?></span></label>
                        <div class="is-invalid-container">
                            <input name="LastName" id="last_name" maxlength="60"
                                   placeholder="<?= lang('last_name') ?>"
                                   class="form-control border-light" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>
                <!--    minor phone line    -->
                <div class="d-flex px-15 align-items-end">
                    <div class="form-group flex-fill mb-10 mie-15 w-100">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('mobile_number') ?> <span
                                    class="text-minor"><?= lang('minor') ?></span></label>
                        <input name="ContactMobileMinor" type="tel" dir="ltr"
                               class="form-control text-right border-light"
                               id="minor_phone" minlength="9" maxlength="10"
                               pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$"
                               title="<?php echo lang('incorrect_mobile') ?>">
                    </div>
                    <div class="form-group flex-fill mb-10 w-125p">
                        <select name="areaCodeMinor" id="minor_phone_zone" class="form-control bg-light border-light"
                                disabled>
                            <option value="+972" selected>&lrm;+972</option>
                            <option value="+91">&lrm;+91</option>
                            <option value="+1">&lrm;+1</option>
                            <option value="+44">&lrm;+44</option>
                        </select>
                    </div>
                </div>
                <!--    minor birthday + gender line    -->
                <div class="d-flex px-15 align-items-end">
                    <div class="form-group flex-fill mb-10 mie-15">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('date_birthday') ?> <span
                                    class="text-minor"><?= lang('minor') ?></span></label>
                        <input name="minorDob" type="date" class="form-control border-light" id="minor_date_of_birth"
                               min="<?= date('Y-m-d', strtotime("-120 year")) ?>"
                               max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group flex-fill mb-10">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('gender') ?> <span
                                    class="text-minor"><?= lang('minor') ?></span></label>
                        <select name="minorGender" id="minor_gender" class="form-control">
                            <option value="0" selected><?php echo lang('gender_not_defined') ?></option>
                            <option value="1"><?php echo lang('male') ?></option>
                            <option value="2"><?php echo lang('female') ?></option>
                        </select>
                    </div>
                </div>
                <!--    minor ID line    -->
                <div class="d-flex px-15">
                    <div class="form-group flex-fill mb-10">
                        <label id="companyId-lbl"
                               class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('id_card') ?> <span
                                    class="text-minor"><?= lang('minor') ?></span></label>
                        <input type="text" minlength="9" maxlength="9" dir="ltr"
                               class="form-control border-light text-right"
                               pattern="^[0-9]{9}$"
                               name="minorId" placeholder="<?= lang('id_card') ?>" id="minor_CompanyId">
                    </div>
                </div>
                <!--    minor relationship line    -->
                <div class="d-flex px-15">
                    <div class="form-group flex-fill mb-10">
                        <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('minor_relationship') ?></label>
                        <select name="relationship" id="relationship" class="form-control">
                            <option value="1"><?php echo lang('father') ?></option>
                            <option value="2"><?php echo lang('mother') ?></option>
                            <option value="3"><?php echo lang('brother_or_sister') ?></option>
                            <option value="4"><?php echo lang('relative') ?></option>
                            <option value="5"><?php echo lang('other') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <hr class="mb-0">
            <!--    ADDITIONAL/LEAD INFO    -->
            <div class="card shadow-none bg-white border-white">
                <div class="card-header bg-white border-white p-0" id="headingOne">
                    <h1 class="mb-0">
                        <button id="additionalData"
                                class="btn btn-block text-start collapsed d-flex justify-content-between" type="button"
                                data-toggle="collapse"
                                data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <span><i class="fal fa-user-plus mie-8"></i><?php echo lang($option == 'client' ? 'more_details' : 'lead_info') ?></span>
                            <i class="far bsapp-fs-22 fa-chevron-down text-end"></i>
                        </button>
                    </h1>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne">
                    <div class="card-body p-0">
                        <?php if ($option == 'client'): ?>
                            <!--    CLIENT    -->
                            <!--    additional birthday + gender line    -->
                            <div class="d-flex px-15 align-items-end">
                                <div class="form-group flex-fill mb-10 mie-15">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('date_birthday') ?></label>
                                    <input name="Dob" type="date" class="form-control border-light" id="date_of_birth"
                                           min="<?= date('Y-m-d', strtotime("-120 year")) ?>"
                                           max="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group flex-fill mb-10">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('gender') ?></label>
                                    <select name="Gender" id="gender" class="form-control">
                                        <option value="0" selected><?php echo lang('gender_not_defined') ?></option>
                                        <option value="1"><?php echo lang('male') ?></option>
                                        <option value="2"><?php echo lang('female') ?></option>
                                    </select>
                                </div>
                            </div>
                            <!--    ID line    -->
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill mb-10">
                                    <label id="companyId-lbl"
                                           class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('id_card') ?></label>
                                    <input type="text" minlength="9" maxlength="9" dir="ltr" pattern="^[0-9]{9}$"
                                           class="form-control border-light text-right" name="CompanyId"
                                           id="CompanyId" placeholder="<?= lang('id_card') ?>">
                                </div>
                            </div>
                            <!--    city line    -->
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill mb-10">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('city') ?></label>
                                    <select class="CitiesSelect" name="City" id="city"></select>
                                </div>
                            </div>
                            <!--    street + house number line    -->
                            <div class="d-flex px-15 align-items-end">
                                <div class="form-group flex-fill mb-10 mie-15 w-100">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('street') ?></label>
                                    <select class="StreetSelect" name="Street" id="street"></select>
                                </div>
                                <div class="form-group flex-fill mb-10 w-125p">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('home_number') ?></label>
                                    <input name="Number" type="number" class="form-control border-light"
                                           id="house_number">
                                </div>
                            </div>
                            <!--    tags line    -->
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill">
                                    <div id='Editlevel2' class="EditQuick">
                                        <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('client_tags_settings') ?></label>
                                        <select class="form-control select2Rank2 text-start"
                                                data-placeholder="<?php echo lang('choose') ?>"
                                                name="ClassLevel[]" id="ClientRanks" multiple="multiple"
                                                data-select2order="true">
                                            <?php $ClassLevels = DB::table('clientlevel')->where('CompanyNum', '=', $CompanyNum)->get();
                                            foreach ($ClassLevels as $ClassLevel) { ?>
                                                <option value="<?php echo $ClassLevel->id; ?>"><?php echo $ClassLevel->Level; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--    remarks line    -->
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?php echo lang('customer_remark') ?></label>
                                    <textarea name="Remarks" id="Remarks" class="form-control border-light"
                                              maxlength="250"></textarea>
                                </div>
                            </div>
                        <?php else: ?>
                            <!--    LEAD    -->
                            <!--    pipeline line    -->
                            <?php $PipelineList = DB::table('pipeline_category')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('id', 'ASC')->get(); ?>
                            <div class="<?php echo(count($PipelineList) <= 1 ? "d-none" : "d-flex") ?> px-15">
                                <div class="form-group flex-fill mb-10">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('choose_pipeline') ?></label>
                                    <select class="form-control text-start" name="PipeLine" id="PipeLine">
                                        <?php foreach ($PipelineList as $ClassType) { ?>
                                            <option value="<?php echo $ClassType->id; ?>"><?php echo $ClassType->Title ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--    source line    -->
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill mb-10">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('incoming_source') ?></label>
                                    <select class="form-control" name="Source" id="Source">
                                        <option value="0" selected><?= lang('without') ?></option>
                                        <?php
                                        $PipeSources = DB::table('leadsource')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Title', 'ASC')->get();
                                        foreach ($PipeSources as $PipeSource) {
                                            ?>
                                            <option value="<?php echo $PipeSource->id; ?>"><?php echo $PipeSource->Title ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--    status line    -->
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill mb-10">
                                    <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('status') ?></label>
                                    <select class="form-control" name="Status" id="Status">
                                        <!--    filled by JS    -->
                                    </select>
                                </div>
                            </div>

                            <!--    agent line    -->
                            <?php if (Auth::userCan('141')): ?>
                                <div class="d-flex px-15">
                                    <div class="form-group flex-fill">
                                        <label class="custom-select-sm mb-0 font-weight-bold"><?= lang('representative') ?></label>
                                        <select class="form-control" name="Agents" id="Agents">
                                            <option value="0"><?= lang('without_representative') ?></option>
                                            <?php
                                            $AgentLoops = (new Users())->getAgent($CompanyNum);
                                            foreach ($AgentLoops as $Agent) { ?>
                                                <option value="<?php echo $Agent->id; ?>"><?php echo $Agent->display_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!--    footer    -->
        <div class="d-flex justify-content-end border-top border-light px-15 py-15">
            <button type="button" class="btn btn-outline-secondary mie-12 px-40"
                    data-dismiss='modal'><?php echo lang('cancel') ?></button>
            <button type="submit"
                    class="btn btn-dark px-40" onClick="if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;">
                <?php echo lang('save') ?></button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        // select2 init
        $('.CitiesSelect').select2({
            theme: "bsapp-dropdown",
            placeholder: '<?php echo lang('select_city') ?>',
            language: "he",
            allowClear: true,
            width: '100%',
            ajax: {
                url: 'action/CitiesSelect.php',
                dataType: 'json'
            },
            minimumInputLength: 3,
            dir: "rtl",
        });

        $('.StreetSelect').select2({
            theme: "bsapp-dropdown",
            placeholder: '<?php echo lang('select_address') ?>',
            language: "he",
            allowClear: true,
            width: '100%',
            ajax: {
                url: 'action/StreetsSelect.php',
                dataType: 'json',
                data: function (params) {
                    var CityId = $(".CitiesSelect").val();
                    var query = {
                        q: params.term,
                        CityId: CityId
                    }
                    return query;
                }
            },
            minimumInputLength: 3,
            dir: "rtl",
        });

        $(".select2Rank2").select2({
            theme: "bootstrap",
            placeholder: "<?php echo lang('choose') ?>",
            width: "100%",
            language: "he",
            dir: "rtl",
        });
        $("select#adult_phone_zone").select2({
            theme: "bsapp-dropdown bsapp-no-arrow",
            width: '100%',
        });
        <?php if (!empty($BrandList)): ?>
        $("select#brands").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });
        <?php endif; ?>
        $("select#select_membership").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });
        $("select#minor_phone_zone").select2({
            theme: "bsapp-dropdown bsapp-no-arrow",
            width: '100%',
        });
        $("select#minor_gender").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });
        $("select#relationship").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });
        $("select#gender").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });

        // lead part
        <?php if ($option == 'lead'): ?>
        $("select#PipeLine").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });

        $("select#Source").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });
        $("select#Status").select2({
            theme: "bsapp-dropdown",
            width: '100%',
        });

            <?php if (Auth::userCan('141')): ?>
            $("select#Agents").select2({
                theme: "bsapp-dropdown",
                width: '100%',
            });
            <?php endif; ?>

        $('#PipeLine').on('change', function () {
            const options = [];
            const $status = $("select#Status");

            <?php
            $PipeTitles = DB::table('leadstatus')->where('CompanyNum', '=', $CompanyNum)->where('Act', '=', '0')->where('Status', '=', '0')->orderBy('Sort', 'ASC')->get();
            foreach ($PipeTitles as $PipeTitle) { ?>
            options.push({
                pipeline: <?php echo $PipeTitle->PipeId; ?>,
                id: <?php echo $PipeTitle->id; ?>,
                value: '<?= htmlspecialchars($PipeTitle->Title, ENT_QUOTES); ?>'
            });
            <?php } ?>

            // destroy select2
            $status.select2('destroy');

            // fill select with proper values
            let content = '';
            for (let x of options) {
                if (x.pipeline == this.value) {
                    content += '<option value=\"' + x.id + "\">" + x.value + '</option>\n';
                }
            }

            $status[0].innerHTML = content;

            // init select2
            $status.select2({
                theme: "bsapp-dropdown",
                width: '100%',
            });
        });

        $('#PipeLine').change();

        <?php endif; ?>

        // minor checkbox toggle
        $('#is_minor').on('click', function () {
            const last_name = $('#adult_last_name').val();
            if ($(this).is(":checked")) {
                $("#first_name").prop('required', true);
                $("#last_name").prop('required', true);
                $("#last_name").val(last_name);

                $("#minorMembershipLabel")[0].classList.remove("d-none");

                $("#minor-div").show();
                $('#minor-div')[0].classList.add("h-100");
            } else {
                $("#first_name").prop('required', false);
                $("#last_name").prop('required', false);
                $("#last_name").val('');

                $("#minorMembershipLabel")[0].classList.add("d-none");

                $('#minor-div').height(0);
                $('#minor-div')[0].classList.remove("h-100");
                setTimeout(() => {
                    $("#minor-div").hide();
                }, 200);
            }
        });

        // toggle arrow
        $("#additionalData").on('click', function () {
            $("#additionalData").find('i.far').toggleClass('fa-chevron-down fa-chevron-up');
        });

        // membership price management
        $('#select_membership').on('change', function () {
            const price = $('#membership_price');

            if (this.value == -1) {
                // without membership - empty disabled
                price.val("0.00");
                price[0].disabled = true;
            } else {
                // set price and enable
                price.val(this.options[this.selectedIndex].getAttribute('price'));
                price[0].disabled = false;
            }
        });

        // form submit action
        $("#addClientPopupForm").on('submit', function (event) {
            event.preventDefault();
            event.cancelBubble = true;
            event.stopPropagation();

            const clientData = {};
            for (let i = 0; i < this.length; i++) {
                const field = this[i];
                const key = field.id;
                if (key == "") continue;
                switch (field.type) {
                    case "checkbox":
                        clientData[key] = field.checked;
                        break;
                    case "select-multiple":
                        // ClassLevel - comma separated array
                        let list = '';
                        for (let j = 0; j < field.length; j++) {
                            if (field[j].selected) {
                                list += "," + field[j].value;
                            }
                        }
                        // don't forget to fix first comma
                        clientData[key] = list.substring(list.length > 0 ? 1 : 0);
                        break;
                    case "search":
                    case "button":
                        // skip
                        break;
                    default:
                        clientData[key] = field.value;
                }
            }
            clientData['fun'] = 'addClient';

            const $parent = $('#<?php echo $MainDiv ?>')
            $parent.showModalLoader()

            $.ajax({
                url: 'ajax/Client.php',
                method: 'POST',
                data: clientData,
                success: function (res) {
                    $parent.find('.modal-content').find(".js-loader").remove();

                    if (res.Status == 'Success') {
                        $.notify({
                            icon: 'fas fa-times-circle',
                            message: res.Notify,
                        }, {
                            type: 'success',
                            z_index: '99999999',
                        });
                        // redirect to new client/minor page
                        let rUrl = window.location.origin + "/office/ClientProfile.php?u=" + res.Message.client_id;
                        if ($('#membership_price').val() > 0) {
                            rUrl += "#user-pay";
                        }
                        window.location.href = rUrl;
                    } else {
                        $.notify({
                            icon: 'fas fa-times-circle',
                            message: res.Message,
                        }, {
                            type: 'danger',
                            z_index: '99999999',
                        });
                    }
                },
                error: function (res) {
                    $('#<?php echo $MainDiv ?>').modal('hide')
                    $.notify({
                        icon: 'fas fa-times-circle',
                        message: lang('action_not_done'),
                    }, {
                        type: 'danger',
                        z_index: '99999999',
                    });
                }
            })
        });

        $("#adult_phone")[0].setAttribute('phone-exact-match', 'false');

        function formatState(state) {
            if (!state.name) {
                if (!state.loading) {
                    const isValidMobileRegx = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/
                    if (!state.text.match(isValidMobileRegx)) {
                        $("#adult_phone").val('');  // prevent auto-selection of invalid data
                        return '<?php echo lang('phone_format_incorrect_ajax') ?>';
                    } else if ($("#adult_phone")[0].getAttribute('phone-exact-match') === 'true') {
                        return '<?php echo lang('mobile_exists_ajax') ?>';
                    }

                    const $state = $(
                        '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center"><div class="mie-8 w-40p h-40p rounded-circle border  d-flex align-items-center justify-content-center bsapp-plus-icon"><i class="fal fa-plus bsapp-fs-20" ></i></div>' + state.text + '</div><div class="badge badge-info badge-pill">' + lang('create_new_cal') + '</div></div>'
                    );

                    return $state;
                } else {
                    return state.text;
                }
            }

            const $state = $(
                '<div class="d-flex justify-content-between align-items-center "><div class="d-flex align-items-center"> <div class="position-relative mie-8"><img src="' + state.img + '" class="w-40p h-40p rounded-circle " /> <div class="position-relative bsapp-status-icon mt_-12p ' + state.status + '"></div> </div> <div class="d-flex flex-column"><div class="d-flex"><span> ' + state.name + ' </span></div><span class="bsapp-fs-14" dir="ltr">' + state.phone + '</span><div></div></div>'
            );

            return $state;
        }

        function setInputFilter(textbox, inputFilter) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(function (event) {
                textbox.addEventListener(event, function () {
                    if (inputFilter(this.value)) {
                        this.oldValue = this.value;
                        this.oldSelectionStart = this.selectionStart;
                        this.oldSelectionEnd = this.selectionEnd;
                    } else if (Object.prototype.hasOwnProperty.call(this, 'oldValue')) {
                        this.value = this.oldValue;
                        if (this.oldSelectionStart !== null &&
                            this.oldSelectionEnd !== null) {
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        }
                    } else {
                        this.value = "";
                    }
                });
            });
        }

        setInputFilter(document.getElementById("minor_phone"), function (value) {
            return /^\d*$/.test(value); // Allow only digits, using a RegExp
        });
        setInputFilter(document.getElementById("minor_CompanyId"), function (value) {
            return /^\d*$/.test(value); // Allow only digits, using a RegExp
        });
        // client only
        <?php if ($option == 'client'): ?>
        setInputFilter(document.getElementById("CompanyId"), function (value) {
            return /^\d*$/.test(value); // Allow only digits, using a RegExp
        });
        <?php endif; ?>

        $("#adult_phone")
            .select2({
                tags: true,
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew: true
                    };
                },
                language: $("html").attr("dir") == 'rtl' ? "he" : "en",
                placeholder: "",
                allowClear: true,
                theme: "bsapp-dropdown bsapp-no-arrow phone-search",
                minimumInputLength: 2,
                ajax: {
                    url: '/office/action/getClientJsonFromPhone.php',
                    data: function (params) {
                        const query = {
                            query: params.term,
                            type: 'public'
                        }

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    },
                    processResults: function (data) {
                        const items = $.map($.parseJSON(data).results, user => ({
                                name: user.name,
                                id: user.id,
                                img: user.img,
                                phone: user.phone,
                                status: user.status
                            })
                        )

                        const sField = $(".select2-container.phone-search").find('.select2-search__field')[0].value;
                        const isValidMobileRegx = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/
                        if (sField.match(isValidMobileRegx) && items.length == 1) {
                            $("#adult_phone")[0].setAttribute('phone-exact-match', 'true');
                        } else {
                            $("#adult_phone")[0].setAttribute('phone-exact-match', 'false');
                        }

                        return {
                            results: items
                        };
                    },
                },
                templateResult: formatState,
                templateSelection: function (item) {
                    const isValidMobileRegx = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/

                    // check if selected option is valid
                    if (!item.text.match(isValidMobileRegx) || $("#adult_phone")[0].getAttribute('phone-exact-match') === 'true') {
                        $("#adult_phone").val('');
                        return '';
                    }

                    if (item.id == '') {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + lang('search_phone') + '</div><div> </div> </div>');
                    } else if (item.isNew) {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div>' + item.text + '</div><div><div class="badge badge-info badge-pill">' + lang('new') + '</div> </div> </div>');
                    } else {
                        $item = $('<div class="d-flex justify-content-between align-items-center"><div><img src="' + item.img + '" class="w-20p h-20p rounded-circle mie-5" /><span> ' + item.name + ' </span></div></div>');
                    }
                    return $item;
                }
            })
            .on("select2:selecting", function (e) {
                if (e.params.args.data.isNew) {
                    const selected = e.params.args.data;
                    const isValidMobileRegx = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/
                    if (selected.text.match(isValidMobileRegx) && $("#adult_phone")[0].getAttribute('phone-exact-match') === 'false') {
                        $("#adult_phone").val(selected.text);
                    }
                } else {
                    window.location.href = window.location.origin + "/office/ClientProfile.php?u=" + e.params.args.data.id;
                }
            })
            .on('select2:open', function () {
                this.setCustomValidity(''); // remove custom validation error
                const $searchfield = $(".select2-container.phone-search").find('.select2-search__field');

                setInputFilter($searchfield[0], function (value) {
                    return /^\d*$/.test(value); // Allow only digits, using a RegExp
                });

                $searchfield[0].dir = 'ltr';
            }).on('select2:close', function () {
                // fix for first selection - change event called manually
                $(this).trigger("change.select2");
            });
    });

</script>
