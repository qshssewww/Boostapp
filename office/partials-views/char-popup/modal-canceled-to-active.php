<?php
    require_once '../../../app/init.php';
    if (!isset($_POST['traineesCount']))
        return json_encode(['Message' => 'traineesCount is required']);

    $traineesCount = $_POST['traineesCount'];
?>

<div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
    <div class="d-none" id="js-cancel-popup-data">

    </div>
    <div class="d-flex justify-content-end w-100">
        <a class="text-dark close-modal px-15"><i class="fal fa-times h4"></i></a>
    </div>

    <div class="d-flex flex-column text-center my-30">
        <div class="w-100 "><?php echo lang('there_are_trainees') ?> <?php echo $traineesCount; ?> <?php echo lang('trainess_booked_to_class') ?></div>
        <div class="font-weight-bold w-100"><?php echo lang('book_to_active_modal') ?></div>
    </div>

    <div class="d-flex justify-content-around w-100 px-15">
        <a class="btn btn-primary flex-fill mie-15 js-force-cancel-to-active" data-return-trainees="1"><?php echo lang('yes') ?></a>
        <a class="btn btn-light flex-fill js-force-cancel-to-active" data-return-trainees="0"><?php echo lang('no') ?></a>
    </div>
</div> 
