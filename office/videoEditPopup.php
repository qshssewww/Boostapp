<div id="popup-wrapper" class="">
    <div id="closePopup">
        <i class="fa fa-times" aria-hidden="true"></i>
    </div>
    <input id="videoId" type="text" style="display:none;">
    <div class="container" id="videoEdit">
        <div class="row mb-4 mt-md-4 mt-3 ">
            <div class="titleLibrary new">
            <?php echo lang('vod_add_video') ?>
            </div>
            <div class="titleLibrary current">
            <?php echo lang('edit_existing_vod') ?>
            </div>
        </div>
        <div class="section">
            <!--            <div class="row mb-3">-->
            <!--                <div class="col-md-6 col-12">-->
            <!--                    <label class="checkbox-container link-label">-->
            <!--                        <div class='name'>העלאת קובץ</div>-->
            <!--                        <input type="checkbox" id="videoUpload">-->
            <!--                        <span class="checkmark"></span>-->
            <!--                    </label>-->
            <!--                </div>-->
            <!--            </div>-->
            <div class="row">
                <div class="col-md-6 col-12 field require">
                    <input type="text" onchange id="videoLink" placeholder="<?php echo lang('vod_video_link') ?>" onfocus="this.placeholder = ''"
                           onblur="this.placeholder = lang('vod_video_link')">
                    <label for="videoLink"><?php echo lang('vod_video_link') ?></label>
                </div>
                <div class="col-md-6 col-12 field require">
                    <input type="text" id="videoName" placeholder="<?php echo lang('vod_video_name') ?>" onfocus="this.placeholder = ''"
                           onblur="this.placeholder = lang('video_name_vod')">
                    <label for="videoName"><?php echo lang('vod_video_name') ?></label>
                </div>
                <div class="col-md-6 col-12 mt-3 hide">
                    <div id="durationBtn">
                    <?php echo lang('video_duration_vod') ?>
                    </div>
                    <div id='duration' style='display:none;'>
                        <input id='h' name='h' type='number' placeholder="00" min='0' max='24'>
                        <label for='h'><?php echo lang('hours') ?></label>
                        <input id='m' name='m' type='number' placeholder="00" min='0' max='59'>
                        <label for='m'><?php echo lang('minutes') ?></label>
                        <input id='s' name='s' type='number' placeholder="00" min='0' max='59'>
                        <label for='s'><?php echo lang('seconds_vod') ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="row">
                <div class="col-md-6 col-12 ui-widget inputcontainer">
                    <label for="videoFolder"><?php echo lang('storage_folder_vod') ?></label>
                    <br>
                    <select name="" id="videoFolder"></select>
                    <!-- <label for="videoFolder">בחירת תיקייה לאחסון</label>
                    <br>
                    <input type="text" id="videoFolder">
                    <div class="icon-container">
                        <i class="loader"></i>
                    </div> -->
                </div>
                <div class="col-md-6 col-12 ui-widget inputcontainer">
                    <label for="videoCoacher"><?php echo lang('associat_coach_vod') ?></label>
                    <br>
                    <select name="" id="videoCoacher"></select>
                    <!-- <label for="videoCoacher">בחירת תיקייה לאחסון</label>
                    <br>
                    <input type="text" id="videoCoacher"> -->
                </div>
            </div>
        </div>
        <div class="section">
            <div class="row">
                <div class="col-12 field-textarea require">
                    <textarea name="" id="videoDesc" placeholder="<?php echo lang('vod_description') ?>" onfocus="this.placeholder = ''"
                              onblur="this.placeholder = lang('vod_description')"></textarea>
                    <label for="videoDesc"><?php echo lang('vod_description') ?></label>
                </div>
            </div>

            <div style="display:none;">
                <label for="videoDesc"><?php echo lang('files_links_vod') ?></label>
                <div class="row grey-area">
                    <div class="col-md-6 col-12">
                        <label for="downloadLinkSelect"><?php echo lang('download_option_vod') ?></label>
                        <br>
                        <select name="" id="downloadLinkSelect">
                            <option>hello</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="downloadLinkInput"><?php echo lang('download_option_vod') ?></label>
                        <br>
                        <input type="text" name="" id="downloadLinkInput" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row message" style="display:none;">
            <div class="col-4">
                <div class="alert alert-warning d-flex justify-content-center align-items-center" role="alert">
                <?php echo lang('fill_fields_vod') ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div id="cancelBtn" class="btn btn-danger"><?php echo lang('action_cacnel') ?></div>
            </div>
            <div class="col-6">
                <div id="confirmBtn" class="btn btn-primary"><?php echo lang('save') ?></div>
            </div>
        </div>
    </div>
</div>