<?php
if (Auth::check() && Auth::userCan('1')):
    if (!preg_match("/BusinessSettings/i", $_SERVER['REQUEST_URI'])):
        require_once __DIR__.'/../../../office/Classes/247SoftNew/ClientGoogleAddress.php';
        $CompanyNum = Auth::user()->CompanyNum;
        $Address = ClientGoogleAddress::getBusinessAddress($CompanyNum);

        if (empty($Address)):
            if (!isset($_COOKIE['buisness_location']) || ($_COOKIE['buisness_location'] != $CompanyNum)): ?>
                <link rel="stylesheet"
                      href="../../../office/assets/css/businessLocationModal.css?<?= filemtime(__DIR__ . '/../../../office/assets/css/businessLocationModal.css') ?>">

                <div class="modal-dialog bs-modal-reposition fade" id="GoogleLocationAddressUpdateModal" tabindex="-1"
                     aria-labelledby="GoogleLocationAddressUpdateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title d-none"
                                    id="GoogleLocationAddressUpdateModalLabel"><?php echo lang('customer_card_adress'); ?></h5>
                                <button type="button" class="close position-absolute left-0 mx-0" data-dismiss="modal"
                                        aria-label="<?php echo lang('close'); ?>">
                                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="lottie-wrapper px-sm-30"></div>
                                <div class="form-wrapper position-relative d-flex align-items-center">
                                    <div class="form-sub-wrapper">
                                        <h2 class="heading font-weight-bolder h5 mb-8"><?php echo lang('business_address'); ?></h2>
                                        <p class="description px-10 px-sm-40"><?php echo lang('help_customers_find_your_business'); ?></p>
                                        <form id="updateGoogleAddressForm" class="mt-40 ">
                                            <div class="form-group position-relative px-sm-20">
                                                <label class="d-none" for="<?php echo lang('address'); ?>"></label>
                                                <input type="text" class="form-control untouched d-block pr-40"
                                                       name="address" id="BusinessPlaceString" required="required"
                                                       minlength="3"
                                                       placeholder="<?php echo lang('address_search'); ?>">
                                                <i class="far fa-search position-absolute start-0 top-0 mt-10 mx-10 mx-sm-30"></i>
                                                <input type="text" class="form-control d-none" name="PlaceId"
                                                       id="BusinessPlaceId" data-string="">
                                                <input type="text" class="form-control d-none" name="PlaceLatLng"
                                                       id="BusinessPlaceLatLng"
                                                       value="<?php echo($Address->lat_lng ?? '') ?>">
                                                <input type="text" class="form-control d-none" name="PlaceCity"
                                                       id="BusinessPlaceCity"
                                                       value="<?php echo($Address->city_id ?? '') ?>">
                                            </div>
                                            <div class="pickFromListError animate-opacity alert alert-danger mx-sm-20"><?php echo lang('location_from_list_required'); ?></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default d-none"
                                        data-dismiss="modal"><?php echo lang('close'); ?></button>
                                <button type="submit" form="updateGoogleAddressForm"
                                        class="btn btn-dark w-100"><?php echo lang('address_update'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?= ClientGoogleAddress::GOOGLE_API_KEY ?>&libraries=places&language=he"></script>
                <script>
                    $(document).ready(function () {

                        $('#GoogleLocationAddressUpdateModal').on('show.bs.modal', function (event) {
                            let animation = '<lottie-player class="d-block" src="/office/js/dlivery-map.json"  background="transparent"  speed="1" autoplay></lottie-player>';
                            $('.lottie-wrapper', '#GoogleLocationAddressUpdateModal').html(animation);

                            setTimeout(() => modalContentHeightAdjustment(), 175);
                        });

                        const input = document.getElementById('BusinessPlaceString');
                        const options = {
                            componentRestrictions: {country: "il"},
                            fields: ["geometry", "place_id", "formatted_address", "address_components"],
                            types: ["geocode", "establishment"],
                        };
                        autocomplete = new google.maps.places.Autocomplete(input, options);

                        // autocomplete choose event
                        autocomplete.addListener("place_changed", function () {
                            const place = autocomplete.getPlace();
                            if (place == undefined) {
                                document.getElementById('BusinessPlaceString').value = '';
                                return;
                            }

                            document.getElementById('BusinessPlaceLatLng').value = place.geometry.location.toUrlValue();
                            document.getElementById('BusinessPlaceId').value = place.place_id;
                            document.getElementById('BusinessPlaceId').setAttribute('data-string', place.formatted_address);
                            input.value = place.formatted_address;

                            const address_components = place.address_components;
                            for (let i = 0; i < address_components.length; i++) {
                                const component = address_components[i];
                                if (component.types.includes('locality')) document.getElementById('BusinessPlaceCity').value = component.long_name || '';
                            }
                        });

                        // check for correct input
                        input.addEventListener("change", function (e) {
                            if ($('.pac-container:visible').length > 0) {
                                // trigger to correctly handle click outside
                                google.maps.event.trigger(autocomplete, 'place_changed');
                            }

                            if (this.value == '') {
                                // clear google object
                                autocomplete.set('place', undefined);
                                // clear values
                                document.getElementById('BusinessPlaceLatLng').value = '';
                                document.getElementById('BusinessPlaceId').value = '';
                                document.getElementById('BusinessPlaceId').setAttribute('data-string', '');
                                document.getElementById('BusinessPlaceCity').value = '';
                            } else {
                                this.value = document.getElementById('BusinessPlaceId').getAttribute('data-string');
                            }

                            setTimeout(() => {
                                if (!this.value.length) {
                                    $('.pickFromListError', '#updateGoogleAddressForm').addClass('active');
                                }
                            }, 50);
                        });

                        //check for the 7 days cookie expire
                        if (!/buisness_location/mi.test(document.cookie)) {
                            document.cookie = `buisness_location=<?php echo $CompanyNum; ?>; expires=${new Date(moment().add(1, 'weeks')).toUTCString()}; path=/;`;
                        } else {
                            document.cookie.split('; ').forEach(cookie => {
                                if (/buisness_location/im.test(cookie)) {
                                    if (cookie.split("=")[1] != <?php echo $CompanyNum?>) {
                                        document.cookie = `buisness_location=<?php echo $CompanyNum; ?>; expires=${new Date(moment().add(1, 'weeks')).toUTCString()}; path=/;`;
                                    }
                                }
                            });
                        }

                        setTimeout(() => $('#GoogleLocationAddressUpdateModal').modal('show'), 1000);

                        $('#BusinessPlaceString', 'form#updateGoogleAddressForm').on('focus', function (event) {
                            $('.pickFromListError', '#updateGoogleAddressForm').removeClass('active');
                        });
                        $('#BusinessPlaceString', 'form#updateGoogleAddressForm').on('input', function (event) {
                            $(this).removeClass('untouched')
                        });

                        $("#updateGoogleAddressForm").on('submit', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            if ($('#BusinessPlaceId', this).val().length) {
                                if (this.checkValidity()) {
                                    $("button[type=submit]", "#GoogleLocationAddressUpdateModal").attr('disabled', true);

                                    $.ajax({
                                        method: "POST",
                                        url: "<?php echo '../../../office/ajax/GoogleAddress.php'?>",
                                        data: {
                                            action: 'insertNewGoogleAddress',
                                            place_id: $('#BusinessPlaceId', this).val(),
                                            address: $('#BusinessPlaceString', this).val(),
                                            lat_lng: $('#BusinessPlaceLatLng', this).val(),
                                            place_city: $('#BusinessPlaceCity', this).val(),
                                        }
                                    }).done(function (response) {
                                        response = /(status|success)/gi.test(response) ? response = JSON.parse(response) : null;
                                        if (response.success && response.status == 200) {
                                            $("#updateGoogleAddressForm")[0].reset()
                                            $('#GoogleLocationAddressUpdateModal').modal('hide');
                                            $("button[type=submit]", "#GoogleLocationAddressUpdateModal").attr('disabled', false);
                                            $.notify({
                                                icon: 'fas fa-check',
                                                message: "<?php echo lang('action_done'); ?>",
                                            }, {type: 'success', z_index: '99999999'});

                                        } else $.notify({
                                            icon: 'fas fa-times',
                                            message: "<?php echo lang('action_cancled');?>",
                                        }, {type: 'danger', z_index: '99999999'});
                                    });
                                }
                            } else {
                                $("button[type=submit]", "#GoogleLocationAddressUpdateModal").attr('disabled', false);
                                $.notify({
                                    icon: 'fas fa-exclamation',
                                    message: "<?php echo lang('location_from_list_required');?>"
                                }, {type: 'warning', z_index: '99999999'});
                            }
                        });

                        window.addEventListener('resize', () => modalContentHeightAdjustment());

                        function modalContentHeightAdjustment() {
                            const modal = document.getElementById('GoogleLocationAddressUpdateModal');
                            let contentHeight = modal.querySelector('.modal-body').offsetHeight - modal.querySelector('.lottie-wrapper').offsetHeight;
                            let paddingHeight = 32; //2rem
                            modal.querySelector('.form-wrapper').style.height = (contentHeight - paddingHeight) + "px";
                        }
                    });
                </script>
            <?php
            endif;
        endif;
    endif;
endif
?>