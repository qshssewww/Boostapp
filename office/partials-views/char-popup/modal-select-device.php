<?php
    require_once "../../../app/init.php";
    require_once '../../Classes/Numbers.php';
    //add null handeling
    $actId = $_REQUEST['actId'];
    $deviceObj = new Numbers();
    $devicesData = $deviceObj->getDevicePopupInfo($actId);
?>


        <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
            <div class="d-flex justify-content-between w-100 mb-15">
                <h5 class="bsapp-fs-18 p-15">בחר מכשיר</h5>
                <a class="text-dark close-modal p-15"><i class="fal fa-times h4"></i></a>
            </div>
            <?php
            if (empty($devicesData['availableDevices']))
                echo '<div class="text-center mt-10 mb-15">אין מכשירים זמינים</div>';
            else {
            ?>
            <div class="d-flex flex-column px-15">
                <div data-actid="<?php echo $actId ?>" id="act-id"></div>
                <?php
                    foreach ($devicesData['availableDevices'] as $key => $device){
                ?>
                    <div class="custom-control custom-radio mb-15">
                        <input type="radio" id="deviceRadio<?php echo $key ?>" name="deviceRadios" class="custom-control-input" value="<?php echo $device->id ?>">
                        <label class="custom-control-label" for="deviceRadio<?php echo $key ?>"><?php echo $device->Name ?></label>
                    </div>
                <?php
                }
            ?>
            </div>
            <button class="btn btn-light js-save-device mx-15">שמור</button>
            <?php } ?>
        </div> 
