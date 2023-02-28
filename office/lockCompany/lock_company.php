<?php
    require_once dirname(__FILE__, 2) . "/Classes/247SoftNew/SoftClient.php";
    require_once dirname(__FILE__, 2) . "/Classes/247SoftNew/SoftPayment.php";
    require_once dirname(__FILE__, 2) . "/Classes/Functions.php";


    $company = Company::getInstance(false);
    $softClient = SoftClient::getRow($company->__get("CompanyNum"),"FixCompanyNum");
    if($company->__get("lockDate") != null){
        $endDate = date("d/m/Y",strtotime($company->__get("lockDate")));
        $endDateNum = strtotime($company->__get("lockDate"));
        $weekday = date('w', $endDateNum);
        $now = strtotime("now");
        $func = new Functions();
        $dayName = $func->getDayName($weekday);
?>
    <script src="/office/lockCompany/js/modal.js"></script>
    <div class="modal px-0 px-sm-auto" tabindex="-1" role="dialog" id="js-lock-modal">
        <div class="modal-dialog modal-md modal-dialog-centered m-0 m-sm-auto">
            <div class="modal-content">
                <div class="modal-body h-100">
                    <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-700p">
                        <div class="d-flex justify-content-between w-100">
                            <h5><?php echo lang('update_payment_details_studio') ?></h5>
                            <a href="javascript:;"  class="text-dark" <?php echo $now >= $endDateNum ? "disabled" : 'data-dismiss="modal"' ?> ><i class="fal fa-times h4"></i></a>
                        </div>
                        <div class="d-flex  flex-column text-center">
                            <h1 class="text-primary"><i class="fal fa-credit-card"></i></h1>
                            <div class="w-100 ">.שמנו לב כי חיוב האשראי שלך על השימוש במערכת נכשל</div>
                            <div class="w-100 ">עליך לעדכן אמצעי תשלום חדש!</div>
                            <br>
                            <div class="w-100 ">במידה ולא תבצע פעולה זו עד ליום <?php echo $dayName ?> ה-<?php echo $endDate  ?></div>
                            <div class="w-100 ">המערכת תחסם לגישה!</div>
                        </div>
                        <div class="d-flex justify-content-around w-100">
                            <a  class="btn btn-primary flex-fill mie-15 js-credit-btn" href="javascript:;">עדכון כרטיס אשראי</a>
                            <a  class="btn btn-light flex-fill js-try-later" <?php echo $now >= $endDateNum ? "disabled" : 'data-dismiss="modal"' ?> href="javascript:;" >הבנתי, לא עכשיו</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal px-0 px-sm-auto" tabindex="-1" role="dialog" id="js-lock-credit">
        <div class="modal-dialog modal-md modal-dialog-centered m-0 m-sm-auto">
            <div class="modal-content">
                <div class="modal-body h-100">
                    <div class="d-flex flex-column  bsapp-min-h-700p h-100">
                        <div class="d-flex justify-content-between w-100">
                            <h5><?php echo lang('update_payment_details_studio') ?></h5>
                            <a href="javascript:;"  class="text-dark" <?php echo $now >= $endDateNum ? "disabled" : 'data-dismiss="modal"' ?> ><i class="fal fa-times h4"></i></a>
                        </div>
                        <div class="d-flex justify-content-center align-items-lg-center text-center h-100 position-relative">
                            <div class="spinner-border text-primary js-fa-spin position-absolute" role="status" style="top:50%;left:50%;margin-top:-16px;margin-left:-16px;">
                                <span class="sr-only">טוען..</span>
                            </div>
                            <iframe id="js-update-card" height="700" allowfullscreen="true" scrolling="no" style="width:100%;min-height: 100%;border:none;overflow: hidden;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
