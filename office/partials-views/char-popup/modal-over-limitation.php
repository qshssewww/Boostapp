<?php
    require_once '../../../app/init.php';
    if (!isset($_GET['text']))
        return json_encode(['Message' => 'text is required']);

    $text = $_GET['text'] ?? '';
    $isMeeting = $_GET['isMeeting'] ?? null;
    $isPayment = $_GET['isPayment'] ?? null;
?>
<div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p" dir="<?= $_COOKIE['boostapp_dir'] && $_COOKIE['boostapp_dir'] == 'rtl' ? 'ltr' : 'rtl' ?>">


    <div class="d-flex justify-content-between">
        <div><i class="text-dark modal-close close-modal fal fa-times h4" style="cursor: pointer"></i></div>
        <div><h4><?= lang('title_irregular_booking'); ?></h4></div>
    </div>

    <div class="d-flex flex-column text-center my-30 px-15" dir="<?= $_COOKIE['boostapp_dir'] ?>">
        <i class="text-danger fal fa-exclamation-circle" style="font-size: 100px;"></i>
        <br>
        <div class="w-100 "><?= lang('irregular_booking_description') ?></div>
        <div class="w-100 "><?php echo $text ?></div>
        <div class="font-weight-bold w-100"><?= lang((isset($isMeeting) || isset($isPayment)) ? 'meeting_exceed_membership_popup' : 'q_book_with_irregurlar') ?></div>
    </div>

    <div class="d-flex justify-content-around w-100 px-15">
        <?php if (isset($isMeeting)): ?>
            <a class="btn btn-light flex-fill mie-15 close-modal"><?php echo lang('yes') ?></a>
            <a onclick="fieldEvents.meetingActions.fixOverLimitationAssignment($(this))" class="btn btn-danger text-white flex-fill"><?php echo lang('no') ?></a>
        <?php elseif (isset($isPayment)): ?>
            <a onclick="meetingDetailsModule.chargedOnMeetingAction($('button.moria-class.btn-success'))" class="btn btn-primary text-white flex-fill mie-15"><?php echo lang('yes') ?></a>
            <a class="btn btn-light flex-fill close-modal"><?php echo lang('no') ?></a>
        <?php else: ?>
        <a onclick="modalOverLimitation.forceAssignment($(this))" class="btn btn-primary text-white flex-fill mie-15"><?php echo lang('yes') ?></a>
        <a class="btn btn-light flex-fill close-modal"><?php echo lang('no') ?></a>
        <?php endif; ?>
    </div>
</div> 