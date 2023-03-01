<?php
    require_once '../../../app/init.php';
    if (!isset($_POST['actIdArr']))
        return json_encode(['Message' => 'actIdArr is required']);
    if (!isset($_POST['classId']))
        return json_encode(['Message' => 'classId is required']);
    if (!isset($_POST['overClients']))
        return json_encode(['Message' => 'overClients is required']);
    if (!isset($_POST['isSingle']))
        return json_encode(['Message' => 'isSingle is required']);

    $actIdArr = $_POST['actIdArr'];
    $classId = $_POST['classId'];
    $overClients = $_POST['overClients'];
    $isSingle = $_POST['isSingle'];
?>

<div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
    <div class="d-none" id="js-max-popup-data"
    data-act-id-arr='<?php echo json_encode($actIdArr); ?>' 
        data-class-id="<?php echo $classId; ?>"
    data-is-single="<?php echo $isSingle; ?>"></div>

    <a class="text-dark js-cancel-assignment"><i class="fal fa-times h4"></i></a>

    <div class="d-flex flex-column text-center my-30">
        <div class="w-100 ">ישנה חריגה של <?php echo $overClients; ?> מתאמנים מהמקסימום לשיעור זה</div>
        <div class="font-weight-bold w-100">האם ברצונך להמשיך?</div>
    </div>

    <div class="d-flex justify-content-around w-100">
        <a class="js-agree-max-client btn btn-primary flex-fill mie-15"><?php echo lang('yes') ?></a>
        <a class="btn btn-light flex-fill js-cancel-assignment"><?php echo lang('no') ?></a>
    </div>
</div> 
