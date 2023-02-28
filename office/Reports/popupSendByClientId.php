
<div class="ip-modal" id="SendClientPush" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-hidden="true" >
    <div class="ip-modal-dialog BigDialog" <?php // _e('main.rtl')  ?>>
        <div class="ip-modal-content text-start">
            <div class="ip-modal-header d-flex justify-content-between" >
                <h4 class="ip-modal-title"><?php echo lang('send_message_to_distribution_list') ?></h4>
                <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>

            </div>
            <div class="ip-modal-body" >
                <form action="SendClientPushReport"  class="ajax-form clearfix" autocomplete="off">
                    <input type="hidden" name="me" value="1">
                    <input type="hidden" name="_token" id="csrf-token" value="<?php echo Session::token() ?>" />

                    <div class="alertb alert-info" style="font-size: 12px;">
                        <strong><?php echo lang('option_to_use_params_inside_message') ?>:</strong><br>
                        <strong>[[<?php echo lang('name_table') ?>]]</strong> <?php echo lang('will_be_changed_in_client_full_name') ?><br>
                        <strong>[[<?php echo lang('first_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_private_name') ?><br>
                        <strong>[[<?php echo lang('full_representative_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_fullname') ?><br>
                        <strong>[[<?php echo lang('representative_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_firstname') ?><br>
                        <strong>[[<?php echo lang('studio_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_studio_name') ?>
                    </div>

                    <div class="form-group" >
                        <label><?php echo lang('sending_option') ?></label>
                        <select onchange="changeTextareaType(this)" class="form-control" name="Type">
                            <option value="0"><?php echo lang('free_push_message') ?></option>
                            <option value="1"><?php echo lang('sms_message_pay') ?></option>
                            <option value="2" selected><?php echo lang('email_free') ?></option>
                        </select>
                    </div>

                    <input type="hidden" name="clientsIds">
                    <div id="clientsNames"></div>

                    <div class="form-group">
                        <label><?php echo lang('type_subject') ?></label>
                        <input type="text" value="<?php echo!empty($Subject) ? $Subject : '' ?>" name="Subject" placeholder="<?php echo lang('subject') ?>" class="form-control" required >
                    </div>

                    <div class="form-group" >
                        <label><?php echo lang('class_send_message') ?> <span  style="font-size: 12px;">(<span "><?php echo lang('zero_chars_zero_messages') ?></span>)</span></label>
                        <textarea onkeyup="updateCharCount(this)" name="Content" class="form-control summernotes">
                            <?php echo!empty($Content) ? $Content : '' ?>
                        </textarea>

                    </div>

            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>
                <div class="ip-actions">
                    <button type="submit" name="submit" class="btn btn-primary text-white"><?php echo lang('send') ?></button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    span.badge a.remove{cursor: pointer;}
    #SendClientPush #clientsNames #clientList{max-height: 4.5em; overflow-y: scroll; overflow:auto;}
</style>
<script>
    function updateCharCount(elem) {
        const textToChange = $(elem).closest('div').find('label');
        let newText = textToChange.text();
        let existingNumbers = (newText).match(/\d+/g).map(Number);
        let newNumbers = [];
        let charLength = $(elem).val().length;


        newNumbers.push(charLength);
        newNumbers.push(parseInt(charLength / 200) + 1);

        existingNumbers.map(function (v, k) {
            newText = newText.replace(v, newNumbers[k]);
        });

        textToChange.text(newText);
    }

    function changeTextareaType(elem) {
        const summernoteTA = $(elem).closest('form').find('textarea');
        if ($(elem).val() == 1) {
            summernoteTA.summernote('destroy');
            summernoteTA.text('');
        } else if(summernoteTA.closest('div').find('div').length == 0) {
            summernoteTA.summernote({
                dialogsInBody: true,
                placeholder: '<?php echo lang('type_message_content') ?>',
                tabsize: 3,
                height: 200,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']]
                ]
            });
        }
    }

    (function ($) {
        // a hack to deselct all the checkboxes on ajax request callback
        // workaround fro boostapp
        $(document).on('xhr.dt', 'table', function () {
            var tbl = $(this);
            if (!tbl || !tbl.DataTable || !$(this).DataTable().column || !$(this).DataTable().column(0) || !$(this).DataTable().column(0).checkboxes)
                return;
            tbl.DataTable().column(0).checkboxes.deselectAll();
        });

        $(document).ready(function () {
            var SendClientPush = $('#SendClientPush');
            var clients = $('[name="clientsIds"]', SendClientPush);
            SendClientPush.on('shown.bs.modal', function () {
                var el = $(this);
                // wysiwyg textarea editor
                $('.summernotes').summernote({
                    dialogsInBody: true,
                    placeholder: '<?php echo lang('type_message_content') ?>',
                    tabsize: 3,
                    height: 200,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']]
                    ]
                });

                var ids = clients.val().split(",");
                var clientsArea = $('#clientsNames', el).html('');
                var html = '<div><label>'+ '<?php echo lang('clients') ?>' +' (<span id="clientCount">' + ids.length + '</span>)</label></div><div id="clientList">';



                ids.map(function (x) {
                    html += '<span class="badge badge-primary text-white mie-7"><span><i class="fas fa-spinner fa-spin"></i>&nbsp;&nbsp;</span><a data-id="' + x + '" class="text-white remove" title="<?php echo lang('remove_clinet_popupsend') ?>">x</a></span>'
                });


                $.get('<?php echo get_loginboostapp_domain() ?>/api/client/all?' + ids.map(function (x) {
                    return 'clientsId[]=' + x
                }).join('&'), function (data) {
                    var html = '<div><label>' + '<?php echo lang('clients') ?>' + '(<span id="clientCount">' + ids.length + '</span>)</label></div><div id="clientList">';
                    data.items.map(function (x) {
                        html += '<span class="badge badge-primary text-white mie-7" title="' + x.clientPhone + '"><span>' + x.clientFullName + '&nbsp;&nbsp;</span><a data-id="' + x.clientId + '" class="text-white remove" title="<?php echo lang('remove_clinet_popupsend') ?>">x</a></span>'
                    });
                    clientsArea.html(html + "</div>");
                })
                clientsArea.html(html + "</div>");

            }).on('click', 'span.badge a.remove', function () {
                var id = $(this).data('id');
                var countsClient = clients.val().split(",").filter(function (x) {
                    return x.toString() != id.toString()
                });
                clients.val(countsClient.join(","));
                $('span#clientCount', SendClientPush).html(countsClient.length);

                $(this).parent().remove();
                if (!clients.val()) {
                    SendClientPush.modal('toggle');
                    alert('<?php echo lang('select_customer_popupsend') ?>')
                }
            });


        })
    })(jQuery);



</script>

<script>
    (function ($) {
        $.ajaxSetup({
            beforeSend: function (xhr, settings) {
                if (settings && settings.url && settings.url.match(/api\.boostapp\.co\.il/)) {
                    for (var key in $.ajaxSettings.headers) {
                        xhr.setRequestHeader(key, null)
                    }
                    xhr.setRequestHeader('x-cookie', document.cookie)
                    xhr.setRequestHeader('Content-Type', 'application/json')
                }
            }
        });
    })(jQuery);
</script>
