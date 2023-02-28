<?php
require_once '../../../app/init.php';
require_once '../../Classes/ClassesType.php';

$items = ClassesType::getMemberships(Auth::user()->CompanyNum);

foreach ($items as $key=>$item):
    ?>
    <div class="d-flex align-items-center mb-10">
        <input type="checkbox" value="<?= $item->ItemId ?>">
        <label class="bsapp-custom-checkbox text-right mis-5 font-weight-bolder text-gray-500 mb-0 bsapp-fs-14">
            <?= $item->ItemName ?></label>
    </div>
<?php endforeach; ?>
