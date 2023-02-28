<?php
require_once '../../app/initcron.php';
require_once '../../office/Classes/Company.php';
require_once '../../office/Classes/Notificationcontent.php';

$SettingsInfo = Company::getInstance();
$ItemId = $_POST['ItemId'];
$Items = Notificationcontent::find($ItemId);
$tagButtons = $Items->getBtnsNamesByType($Items->Content);
$content = Notificationcontent::generateBtnsFromContent($Items->Content);

?>

<div class="alertb alert-info mb-11 bsapp-fs-14">
    <strong><?= lang('use_in_notification_params') ?>:</strong><br>
    <?= lang('notification_template_instructions') ?>

</div>

<div class="form-group ">
    <label><?= lang('subject_notication') ?></label>
    <input type="text" name="Subject" class="form-control" placeholder="<?= lang('message_title') ?>"
           value="<?= htmlentities($Items->Subject) ?>">
</div>
<div class="form-group">
    <label><?= lang('contet_single') ?></label>
    <textarea name="Content" class="form-control summernote js-dont-disable"
              rows="10"><?= htmlentities($content) ?></textarea>
</div>
<input type="hidden" value="<?= htmlspecialchars(json_encode($tagButtons)) ?>" id="js-tag-buttons">

<script type="text/javascript">

    $(document).ready(function () {

        function makeButton(Code, Title, context) {
            let ui = $.summernote.ui;

            // create button
            let button = ui.button({
                contents: '<span class="btn btn-sm btn-rounded btn-light">'+Title+'</span>',
                click: function () {
                    context.invoke('editor.pasteHTML', Code);
                }
            });

            return button.render();   // return button as jquery object
        }

        const buttonNames = ['first_name', 'name_table', 'studio_name'];
        const tagButtons = JSON.parse($('#js-tag-buttons').val());
        const allBtns = buttonNames.concat(tagButtons);
        let default_mentions = [
            lang('first_name'),
            lang('name_table'),
            lang('studio_name'),
        ];
        const hintButtons = tagButtons.map(key => lang(key));
        const mentions = default_mentions.concat(hintButtons);

        let buttonsList = [
            'first_name',
            'name_table',
            'studio_name',
            'doc_type',
            'doc_number',
            'doc_link',
            'declined_reason',
            'click_here',
            'store_period',
            'days_number',
            'subscription_name',
            'username_single',
            'cal_new_class_type_name',
            'class_date_single',
            'time_of_a_class',
            'link',
            'membership_expire_date',
            'customer_card_table_membership',
            'representative_name',
            'full_representative_name',
        ];

        function addButton(name) {
            let btnName = lang(name);
            let btnCode = '<input type="button" class="btn btn-sm btn-rounded btn-light" value="'+ btnName +'">&nbsp;';

            let addCustomButton = (context) => {
                let ui = $.summernote.ui;

                // create button
                let button = ui.button({
                    contents: '<span class="btn btn-sm btn-rounded btn-light">' + btnName + '</span>',
                    click: function () {
                        context.invoke('editor.pasteHTML', btnCode);
                    }
                });

                return button.render();   // return button as jquery object
            };

            buttons[name] = addCustomButton;
        }

        let buttons = {};

        for (let i in buttonsList) {
            addButton(buttonsList[i]);
        }

        $('.summernote').summernote({
            tabsize: 2,
            height: 250,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol', 'highlight']],
                ['mybutton', allBtns],
            ],
            buttons: buttons,
            hint: {
                mentions: mentions,
                match: /\B@(\w*)$/,
                search: function (keyword, callback) {
                    callback($.grep(this.mentions, function (item) {
                        return item.indexOf(keyword) == 0;
                    }));
                },
                content: function (item) {
                    return $("<span>").html('<input type="button" class="btn btn-sm btn-rounded btn-light" value="'+item+'">&nbsp;')[0]
                }
            }
        });
    });

</script>

