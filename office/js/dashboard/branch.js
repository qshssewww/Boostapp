let autocompleteBranch;
let waitingPopup;

function handlerBranchSave(res) {
    if (res.Status === 1) {
        // Success handler
        waitingPopup.close();
        $.notify(
            {
                icon: 'fas fa-check-circle',
                message: lang('action_done_beepos'),

            }, {
                type: 'success',
                z_index: 2000,
            });

        $("#js-branch-popup").modal("hide");

        location.reload();
    } else {
        // Error
        waitingPopup.close();
        $.notify({
            message: lang('error_oops_something_went_wrong')
        }, {
            type: 'danger',
            z_index: 2000,
        });
    }
}

// new/edit branch popup set values and open
function OpenBranchPopup(opt = 'edit', ind = 1, data = '{}') {
    const $popup = $("#js-branch-popup");

    // set title and basic values
    if (opt == 'new') {
        $("#BranchPopupTitle").html(lang('add_new_branch_title'));

        document.getElementById('BranchId').value = '';
        document.getElementById('BranchName').value = '';
    } else {
        $("#BranchPopupTitle").html(lang('edit_branch'));

        document.getElementById('BranchId').value = data.branch.id;
        document.getElementById('BranchName').value = data.branch.BrandName;
    }

    // set status
    if (opt == 'new' || ind === 1) {
        $("#BranchStatusContainer").removeClass("d-flex");
        $("#BranchStatusContainer").addClass("d-none");
        $("#BranchStatus").val('0');
    } else {
        $("#BranchStatusContainer").addClass("d-flex");
        $("#BranchStatusContainer").removeClass("d-none");
        $("#BranchStatus").val(data.branch.Status);
    }
    $("#BranchStatus").trigger('change');

    // fill place or clear if no data
    if (data.address == 'null' || data.address == undefined) {
        document.getElementById('BranchPlaceString').value = '';
        document.getElementById('BranchPlaceLatLng').value = '';
        document.getElementById('BranchPlaceId').value = '';
        document.getElementById('BranchPlaceId').setAttribute('data-string', '');
        document.getElementById('BranchPlaceCity').value = '';
    } else {
        document.getElementById('BranchPlaceString').value = data.address.address;
        document.getElementById('BranchPlaceLatLng').value = data.address.lat_lng;
        document.getElementById('BranchPlaceId').value = data.address.place_id;
        document.getElementById('BranchPlaceId').setAttribute('data-string', data.address.address);
    }

    $popup.modal("show");
}

$(document).ready(function () {
    // prevent submitting form on Enter
    $("#js-branch-popup").find('input').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

    $("#js-newBranch").on("click", function () {
        OpenBranchPopup('new', 0, $(this).data('default'));
    });

    const options = {
        componentRestrictions: {country: "il"},
        fields: ["geometry", "place_id", "formatted_address", "address_components"],
        types: ["geocode", "establishment"],
    };
    autocompleteBranch = new google.maps.places.Autocomplete(document.getElementById('BranchPlaceString'), options);

    // autocomplete choose event
    autocompleteBranch.addListener("place_changed", function () {
        const place = autocompleteBranch.getPlace();

        if (place == undefined) {
            document.getElementById('BranchPlaceString').value = '';
            return;
        }
        console.log(place.address_components);
        document.getElementById('BranchPlaceLatLng').value = place.geometry.location.toUrlValue();
        document.getElementById('BranchPlaceId').value = place.place_id;
        document.getElementById('BranchPlaceId').setAttribute('data-string', place.formatted_address);
        document.getElementById('BranchPlaceString').value = place.formatted_address;
        for (let i = 0; i < place.address_components.length; i++) {
            if (place.address_components[i].types.includes('locality')) {
                document.getElementById('BranchPlaceCity').value = place.address_components[i].long_name;
            }
        }
    });

    // check for correct input
    document.getElementById('BranchPlaceString').addEventListener("change", function (e) {
        if ($('.pac-container:visible').length > 0) {
            // trigger to correctly handle click outside
            google.maps.event.trigger(autocompleteBranch, 'place_changed');
        }

        if (this.value == '') {
            // clear google object
            autocompleteBranch.set('place', undefined);
            // clear values
            document.getElementById('BranchPlaceLatLng').value = '';
            document.getElementById('BranchPlaceId').value = '';
            document.getElementById('BranchPlaceId').setAttribute('data-string', '');
            document.getElementById('BranchPlaceCity').value = '';
        } else {
            this.value = document.getElementById('BranchPlaceId').getAttribute('data-string');
        }
    });

    // form submit action
    $("#BranchPopupForm").on('submit', function (event) {
        event.preventDefault();

        // spinner
        waitingPopup = $.notify(
            {
                icon: 'fas fa-spinner fa-spin',
                message: lang('loading_beepos'),
            }, {
                type: 'warning',
                z_index: 2000,
            });

        // collect data
        const apiProps = {
            'fun': "saveBranchSettings",
            'BranchId': $("#BranchId").val(),
            'BranchName': $("#BranchName").val(),
            'BranchStatus': $("#BranchStatus").val(),
            'BranchPlaceId': $("#BranchPlaceId").val(),
            'BranchPlaceString': $("#BranchPlaceString").val(),
            'BranchPlaceLatLng': $("#BranchPlaceLatLng").val(),
            'BranchPlaceCity': $("#BranchPlaceCity").val(),
        };

        postApi("branchSettings", apiProps, 'handlerBranchSave', true);
    });

    $("#BranchStatus").select2({
        theme: "bsapp-dropdown",
        minimumResultsForSearch: -1,
        width: '100%',
    });
});
