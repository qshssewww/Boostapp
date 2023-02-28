function handlerDisabledChange(res) {
    if (res.Status === 1) {
        // Success
        $("#card" + res.data.id).toggleClass("card-disabled");

        const sw = $("#customSwitch" + res.data.id);
        sw.prop("checked", !sw[0].checked);
    } else {
        // Error
        $.notify({
            message: lang('error_oops_something_went_wrong')
        }, {
            type: 'danger',
            z_index: 2000,
        });
    }
    hideLoader();
}

function loadClubMemberships() {
    $("#membership-container").html(`<div class="spinner-border text-primary" role="status"></div>`);

    $.ajax({
        url: '/office/ajax/storeItems.php',
        type: 'GET',
        success: function (response) {
            const $container = $("#membership-container");
            $container.html(response);

            // event handlers
            // toggle arrow of ClubMembership on open/close
            $container.find(".collapsed").on('click', function (e) {
                $(e.currentTarget).find(".arrow-toggle").toggleClass("fa-caret-down fa-caret-up");
                $($(e.currentTarget)[0].dataset.target).toggleClass("d-flex");
            });

            // open all active
            $("#membership-container div.card:not(.card-disabled)").find(".collapsed").click();

            $container.find(".rowBox-item").on('click', function (e) {
                e.stopPropagation()
                createClubMemberships.showEditClubMembershipsData(e.currentTarget);
            });

            $container.find(".custom-switch").on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const id = e.currentTarget.dataset.id;
                if (!id) {
                    $.notify({
                        message: lang('error_oops_something_went_wrong')
                    }, {
                        type: 'danger',
                        z_index: 2000,
                    });
                }

                const apiProps = {
                    'fun': "ChangeClubMembershipsDisabled",
                    'id': id,
                    'Status': $(this).find("input")[0].checked ? "2" : "1",    // invert state
                };

                showLoader();
                // disable ClubMembership and all sub-memberships
                postApi("ClubMemberships", apiProps, 'handlerDisabledChange', true);
            });

        },
        error: function () {
            $("#membership-container").html('<h6 class="text-center">' + lang('error_no_info') + '</h6>');

            $.notify({
                message: lang('error_oops_something_went_wrong')
            }, {
                type: 'danger',
                z_index: 2000,
            });
        }
    });
}

$(document).ready(loadClubMemberships);


function decodeUri (search) {
    return JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"').replace('+', ' ') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) });
}

$(document).ajaxSuccess(function (event, xhr, settings) {
    if (settings.data) {
        const data = decodeUri(settings.data);
        if (data.fun == 'toggleManageMemberships'
            || data.fun == 'disableMembershipType'
            || data.fun == 'deleteOrMoveMembershipType') {
            // $('.tableNumber1').find('table').DataTable().ajax.reload();
            // if (data.fun == 'deleteOrMoveMembershipType') {
            //     $('select[name=location]').find(`option[value=${data.id}]`).remove();
            //     if ($('#membershipType1').find('option').length <= 1) {
            //         showMembershipType(false);
            //     }
            // }
        } else if (data.fun == 'disableCategory'
            || data.fun == 'renameProductCategory'
            || data.fun == 'deleteOrMoveCategory'
            || data.fun == 'reorderCategories') {
            $('.tableNumber2').find('table').DataTable().ajax.reload();
            if (data.fun == 'deleteOrMoveCategory') {
                $('select[name=productCategory]').find(`option[value=${data.id}]`).remove();
                getItems("categories");
            }
        } else if (data.fun == 'insertNewMembershipType') {
            // if ($('#membershipField').is(':hidden')) {
            //     showMembershipType(true);
            // }
            // $('select[name=location]').append(`<option value="${xhr.responseJSON}">${data.name}</option>`);
        } else if (data.fun == 'insertNewCategory') {
            $('select[name=productCategory]').append(`<option value="${xhr.responseJSON}">${data.name}</option>`);
        } else if (data.fun == 'reorderItems') {
            // $('.tableNumber1').find('table').DataTable().ajax.reload();
            $('.tableNumber2').find('table').DataTable().ajax.reload();
        } else {
            hideLoader();
        }
    } else {
        hideLoader();
    }
});

$(document).ajaxError(function () {
    hideLoader();
})
