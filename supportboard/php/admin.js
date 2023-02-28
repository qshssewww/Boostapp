'use strict';

/*
* ===========================================================
* SUPPORT BOARD - ADMIN JAVASCRIPT - PHP ONLY
* ===========================================================
* 
* PHP support plugin admin side JavaScript
* Schiocco - Copyright (c)
*/

(function ($) {
    var current_user_id;
    var type = "profile";
    $(document).ready(function () {

        //UPLOAD ADMIN
        $('.sb-upload-php').change(function (data) {
            var file = data.currentTarget.files[0];
            if ($(this).attr("multiple")) type = "attachments";
            current_user_id = $(this).attr("data-user-id");
            if (type == "profile" && (file.name.indexOf(".jpg") > 0 || file.name.indexOf(".png") > 0)) {
                sb_upload_php(file, type);
                $("#sb-btn-save,#sb-btn-save-agent").addClass("disabled");
            }
            if (type == "attachments") {
                var files = data.currentTarget.files;
                for (var i = 0; i < files.length; i++) {
                    sb_upload_php(files[i], type);
                }
                $(".sb-editor .sb-loader").show();
                $(".sb-submit").addClass("disabled");
            }
        });

        //ADMIN SETTINGS
        $("body").on("click", "#sb-btn-install-db", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_plugin_url + '/php/core.php',
                data: {
                    action: 'sb_installation'
                }
            }).done(function (response) {
                alert("Success! You can now use Support Board!");
            });
        });
        $("body").on("click", ".init-step-2 > .button", function () {
            var user = $("#super-admin-username").val();
            var psw = $("#super-admin-psw").val();
            if (user != "" && psw != "") {
                if (psw == $("#super-admin-psw-2").val()) {
                    if (psw.indexOf("|") > -1 || user.indexOf("|") > -1) {
                        alert("Passwords and user can not contain the | char.");
                    } else {
                        jQuery.ajax({
                            method: "POST",
                            url: sb_plugin_url + '/php/core.php',
                            data: {
                                action: 'sb_installation',
                                type: 'super-admin',
                                user: user,
                                psw: psw,
                            }
                        }).done(function (response) {
                            location.reload();
                        });
                    }
                } else {
                    alert("Passwords not match!");
                }
            }
        });
        $("body").on("click", ".init-step-3 > .button", function () {
            var user = $("#agent-username").val();
            var email = $("#agent-email").val();
            var psw = $("#agent-psw").val();
            if (user != "" && email != "" && psw != "") {
                if (psw == $("#agent-psw-2").val()) {
                    var item = {
                        id: getRandomInt(9999999, 99999999),
                        img: sb_plugin_url + "/media/user-1.jpg",
                        username: user,
                        email: email,
                        psw: psw,
                        last_email: "-1"
                    };
                    var arr = [];
                    arr.push(item);
                    var json = JSON.stringify(arr);
                    jQuery.ajax({
                        method: "POST",
                        url: sb_ajax_url,
                        data: {
                            action: 'sb_ajax_save_option',
                            option_name: 'sb-agents-arr',
                            content: json,
                        }
                    }).done(function (response) {
                        location.reload();
                    });
                } else {
                    alert("Passwords not match!");
                }
            }
        });
        $("body").on("click", ".login-submit", function () {
            var user = $("#login-username").val();
            var psw = $("#login-psw").val();
            $(".sb-msg-error-login").hide();
            if (user != "" && psw != "") {
                jQuery.ajax({
                    method: "POST",
                    url: sb_ajax_url,
                    data: {
                        action: 'sb_agent_login',
                        user: user,
                        psw: psw,
                    }
                }).done(function (response) {
                    if (response == "success") {
                        location.reload();
                    } else {
                        $(".sb-msg-error-login").show();
                    }
                });
            }    
        });
        $("body").on("click", ".sb-php-logout", function () {
            jQuery.ajax({
                method: "POST",
                url: sb_ajax_url,
                data: {
                    action: 'sb_agent_logout'
                }
            }).done(function (response) {
                location.reload();
            });
        });
        $("body").on("click", "#sb-btn-save-super-admin", function () {
            $(".sb-msg-success-super-admin").hide();
            var user = $("#super-admin-edit-username").val();
            var psw = $("#super-admin-edit-password").val();
            if (user != "" && psw != "") {
                if (psw == $("#super-admin-edit-password-2").val()) {
                    if (psw.indexOf("|") > -1 || user.indexOf("|") > -1) {
                        alert("Passwords and user can not contain the | char.");
                    } else {
                        jQuery.ajax({
                            method: "POST",
                            url: sb_ajax_url,
                            data: {
                                action: 'sb_super_admin_update',
                                user: user,
                                psw: psw
                            }
                        }).done(function (response) {
                            if (response == "success") {
                                $(".sb-msg-success-super-admin").show();
                                setTimeout(function () {
                                    $(".sb-msg-success-super-admin").hide();
                                }, 2000);
                            }
                        });
                    }
                } else {
                    alert("Passwords not match!");
                }
            }
        });
    });

    //FUNCTIONS
    function sb_upload_php(file, type) {
        var form_data = new FormData();
        form_data.append('file', file);
        form_data.append('user_id', current_user_id);
        form_data.append('type', type);
        jQuery.ajax({
            url: sb_plugin_url + '/php/upload.php',
            dataType: 'image',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) { },
            xhr: function () {
                var myXhr = jQuery.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', function (e, x) { sb_profile_progress_php(e, file.name) }, false);
                }
                return myXhr;
            }
        });
    }
    function sb_profile_progress_php(e, filename) {
        if (e.lengthComputable) {
            var max = e.total;
            var current = e.loaded;
            var percentage = (current * 100) / max;
            if (percentage >= 100) {
                if (type == "profile") {
                    var t = $(".sb-user-id[value='" + current_user_id + "']").closest("tr");
                    setTimeout(function () {
                        $(t).find(".sb-user-img").attr("src", sb_plugin_url + "/php/uploads/" + current_user_id + "/" + current_user_id + ".jpg");
                    }, 300);
                    $("#sb-btn-save,#sb-btn-save-agent").removeClass("disabled");
                } else {
                    $(".sb-editor .sb-loader").hide();
                    $(".sb-submit").removeClass("disabled");
                    $(".sb-user-tickets .sb-attachments-list").append('<div class="sb-attachment-item" data-url="' + sb_plugin_url + "/php/uploads/" + current_user_id + "/" + filename + '">' + filename + '</div>');
                }
            }
        }
    }
    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
}(jQuery));





