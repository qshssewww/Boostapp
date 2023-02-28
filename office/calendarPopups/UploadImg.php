<?php

if (Auth::guest()){
    redirect_to('index.php');
}
?>

<div class="ip-modal" id="itemModal">
    <div class="ip-modal-dialog">
        <div class="ip-modal-content text-right">
            <div class="ip-modal-header" <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float:<?php _e('main.left') ?>;">&times;</a>
                <h4 class="ip-modal-title">תמונת שיעור</h4>
            </div>
            <div class="ip-modal-body" dir="rtl">

                <div class="alertb alert-info">מידות מומלצות להעלאת תמונה:<br>רוחב 350 פיקסל על 200 גובה פיקסל.</div>

                <div class="btn btn-primary ip-upload"><?php _e('main.upload') ?> <input type="file" name="file" class="ip-file"></div>
                <!-- <button class="btn btn-primary ip-webcam">Webcam</button> -->
                <button type="button" class="btn btn-info ip-edit">ערוך תמונה</button>
                <button type="button" class="btn btn-danger ip-delete">מחק תמונה</button>

                <div class="alert ip-alert"></div>
                <div class="ip-info"><?php _e('main.crop_info') ?></div>
                <div class="ip-preview"></div>
                <div class="ip-rotate">
                    <button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="icon-ccw"></i></button>
                    <button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="icon-cw"></i></button>
                </div>
                <div class="ip-progress">
                    <div class="text"><?php _e('main.uploading') ?></div>
                    <div class="progress progress-striped active"><div class="progress-bar"></div></div>
                </div>
            </div>
            <div class="ip-modal-footer">
                <div class="ip-actions">
                    <button type="button" class="btn btn-success ip-save"><?php _e('main.save_image') ?></button>
                    <button type="button" class="btn btn-primary ip-capture"><?php _e('main.capture') ?></button>
                    <button type="button" class="btn btn-default ip-cancel"><?php _e('main.cancel') ?></button>
                </div>
                <button type="button" class="btn btn-default ip-close"><?php _e('main.close') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    var time = function(){return'?'+new Date().getTime()};
    $('#).imgPicker({
        url: 'Server/upload_classes.php',
        aspectRatio: 20/13,
        setSelect: [350, 200, 0, 0],
        deleteComplete: function() {
            $('#avatar').attr('src', '/office/assets/img/default.png');
            this.modal('hide');
        },
        loadComplete: function(image) {
            // Set #avatar image src
            $('#avatar').attr('src', '/office/assets/img/default.png');
            // Set the image for re-crop
            this.setImage(image);
        },
        cropSuccess: function(image) {
            $('#avatar').attr('src', image.versions.pageImg.url + time());
            $('#pageImgPath').val(image.versions.pageImg.url);
            this.modal('hide');
            if($(".edit-avatar").hasClass("classImg")){
                imgUpload();
            }
        }
    });

</script>