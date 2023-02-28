<?php
ini_set("max_execution_time", 0);
require_once '../app/init.php';
require_once 'Classes/Utils.php';
require_once 'Classes/ClassStudioDate.php';

if(Auth::guest()) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => 'user is not connected', 'code' => 500)));
}
//get class id.
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => 'the class was not found', 'code' => 500)));
}
//Create new instance of ClassStudioDate and get the studio data.
$Class = new ClassStudioDate($id);

if (!$Class->id) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => 'the class was not found', 'code' => 500)));
}

$classInfo = $Class->getEmbeddedTrainersData();
$GuideInfo = $classInfo['guide'];
$extraGuide = $classInfo['extraGuide'];
$display_name = $GuideInfo->display_name ?? '';
if($extraGuide) {
    $display_name .= ', '.$extraGuide->display_name;
}

ob_start();
?>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        charPopup.class_info = $('#js-class-data').data();

        let classStatus = $('.js-class-status');
        classStatus.children().addClass('d-none');
        $('.js-class-status-dropdown').children().addClass('d-none');
        switch (charPopup.class_info.classStatus) {
            case 0:
                classStatus.children('.class-active').removeClass('d-none');
                $('.js-mark-class-completed').removeClass('d-none');
                $('.js-mark-class-canceled').removeClass('d-none');
                $('a[data-target="#js-modal-add-user"]').removeClass('disabled');
                break;
            case 1:
                classStatus.children('.class-completed').removeClass('d-none');
                $('.js-mark-class-active').removeClass('d-none');
                $('.js-mark-class-canceled').removeClass('d-none');
                $('a[data-target="#js-modal-add-user"]').removeClass('disabled');
                break;
            case 2:
                classStatus.children('.class-canceled').removeClass('d-none');
                $('.js-mark-class-active').removeClass('d-none');
                $('table[data-bottom-bar="js-participant-tab-1"]').children('tbody').find('tr').addClass('disabled');
                $('a[data-target="#js-modal-add-user"]').addClass('disabled');
                break;
        }
    })
</script>


<div class="d-none" id="js-class-data" 
     data-classid="<?php echo $classInfo['class']->id; ?>" 
     data-class-status="<?php echo $classInfo['class']->Status; ?>"
     data-class-date="<?php echo $classInfo['class']->start_date ?>"
     data-group-number="<?php echo $classInfo['class']->GroupNumber ?>"
     data-class-type-id="<?php echo $classInfo['class']->ClassNameType ?>"
     ></div>
<div class="d-none" id="js-class-remarks">
    <?php echo $classInfo['class']->Remarks ?>
</div>
<!-- top section :: begin -->
<div class="d-flex flex-column border-light border">
    <div class="d-flex justify-content-between w-100">
        <h5 class="d-flex p-15 align-items-center text-black">
            <div class="bsapp-status-icon my-auto mie-10" style="background-color: <?php echo $classInfo['class']->color; ?>;height:24px !important;width:24px !important;"></div>
            <div><?php echo $classInfo['class']->ClassName; ?>
                <?php if($classInfo['class']->liveClassLink) { ?>
                    <span class="text-info mis-5" data-toggle="tooltip" data-placement="top" title="<?php echo lang('online_class') ?>"><i class="far fa-video"></i></span>
                <?php } else if($classInfo['class']->is_zoom_class == 1) { ?>
                    <span class="text-danger mis-5" data-toggle="tooltip" data-placement="top" title="<?php echo lang('zoom_class') ?>"><i class="far fa-play-circle"></i></span>
                <?php } ?>
            </div>
        </h5>
        <?php if (Auth::userCan('163')): ?>
            <a class="text-dark bsapp-fs-26 p-15" href="javascript:;" onclick="charPopup.close(this);" data-registrations="<?php echo $classInfo['class']->ClientRegister; ?>" data-total="<?php echo $classInfo['class']->MaxClient; ?>" data-waiting-list="<?php echo $classInfo['class']->WatingList; ?>"><i class="fal fa-times"></i></a>
        <?php else: ?>
            <a class="text-dark bsapp-fs-26 p-15" href="javascript:;" onclick="charPopup.close(this, true);" data-registrations="<?php echo $classInfo['class']->ClientRegister; ?>" data-total="<?php echo $classInfo['class']->MaxClient; ?>" data-waiting-list="<?php echo $classInfo['class']->WatingList; ?>"><i class="fal fa-times"></i></a>
        <?php endif;?>

    </div>
</div>
<!-- top section :: end  -->
<!-- mid section :: begin -->
<div class="d-flex flex-column overflow-auto bsapp-scroll pt-15" style="height: calc( 100% - 60px ) !important; ">
    <div class="d-flex align-items-center  px-15   text-gray-700 mb-10">
        <div style="width:24px;" class="mie-10 d-flex justify-content-center">
            <span class="fal fa-user-circle bsapp-fs-24"></span>
        </div>
        <div  class="bsapp-fs-18"><?php echo $display_name ?? ''; ?></div>
    </div>
    <div class="d-flex align-items-center  px-15  text-gray-700  mb-10">
        <div style="width:24px;" class="mie-10 d-flex justify-content-center">
            <span class="fal fa-calendar-day bsapp-fs-24"></span>
        </div>
        <div  class="bsapp-fs-18"><?php
            //Display date info in the correct format.
            echo $classInfo['dateStr']
            ?></div>
    </div>
    <div class="d-flex align-items-center  px-15  text-gray-700  mb-10">
        <div style="width:24px;" class="mie-10 d-flex justify-content-center">
            <span class="fal fa-map-marker-alt bsapp-fs-24"></span>
        </div>
        <div class="bsapp-fs-18"><?php echo $classInfo['locationStr']; ?></div>
    </div>
    <div class="flex-fill js-tabs-n-tables position-relative">
        <?php require_once 'partials-views/char-popup/tabs-n-tables.php'; ?>
    </div>

</div>
<?php
$js_char_popup_content = ob_get_contents();
ob_end_clean();
?>

<?php
echo json_encode([
    "js_class_data" => $classInfo,
    "js_char_popup_content" => $js_char_popup_content,
]);
exit;
?>
