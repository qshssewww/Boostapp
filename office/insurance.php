<?php
ini_set("max_execution_time", 0);

require_once '../app/init.php';
require_once '../app/views/headernew.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet"/>
<script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/js/jquery.bootstrap.wizard.js?' . filemtime(__DIR__.'/../assets/js/jquery.bootstrap.wizard.js'); ?>"></script>
<!--<script src="https://unpkg.com/@lottiefiles/lottie-player@0.4.0/dist/lottie-player.js"></script>-->
<script src="assets/js/insurance/insurance.js?<?= filemtime('assets/js/insurance/insurance.js') ?>"></script>
<link href="assets/css/insurance/insurance.css?<?= filemtime('assets/css/insurance/insurance.css') ?>" rel="stylesheet">
<link href="assets/css/fixstyle.css?<?= filemtime('assets/css/fixstyle.css') ?>" rel="stylesheet">

<section class="d-flex align-items-start bsapp-section-insurance py-20">
    <div class="container">
        <div class="pb-30">
            <div class="text-center text-black" >
                <h4 class="mb-10 bsapp-fs-22 bsapp-lh-27 font-weight-medium"><?= lang('exclusive_insurance_title') ?></h4>
                <div class="bsapp-lh-19 bsapp-line-clip-4">
                    <?= lang('insurance_tab_explanation_notice') ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 d-flex justify-content-center " style="min-height:330px;">
                <div class="p-15 p-md-40 rounded d-flex flex-column" style="max-width:1000px;box-shadow:3px 10px 25px rgb(0, 0, 0, 0.16);">
                    <div class="mb-30 d-flex justify-content-start">
                        <label class="badge badge-white text-primary bsapp-fs-14 bsapp-lh-17 p-0 bsapp-badge-insurance-new" ><?= lang('new') ?></label>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center align-items-md-start justify-content-md-start ">
                        <div class="d-flex align-items-center justify-content-center w-md-300p mb-30 mb-md-auto" >
                            <span>
                                <img src="/assets/img/tower-logo.svg" style="width:135px;height:96px;" />
                            </span>
                        </div>  
                        <div class="text-center text-black text-md-start w-md-n300p" >
                            <h4 class="mb-10 bsapp-fs-22 bsapp-lh-27 font-weight-medium">תכנית משולבת לביטוח אחריות כלפי צד שלישי כולל ביטול חריג אחריות מקצועית למאמני / מדריכי ספורט</h4>
                            <div class="bsapp-lh-19 bsapp-line-clip-4">
                                כמאמני כושר, אתם חשופים לתביעות עקב פציעות מתאמנים. בדיוק לשם כך בנינו עבורכם יחד עם חברת מגדל, כחלק בלתי נפרד מניהול הפעילות העסקית והמקצועית, ביטוח ייחודי הנותן מענה לסיכונים ולצרכים אלו. תכנית ביטוח מאמני כושר ומדריכי ספורט זו, מעניקה כיסוי מקיף לאחריותכם.
                            </div>                        
                        </div>
                    </div>
                    <div class="mt-30 d-flex flex-column flex-md-row justify-content-center justify-content-md-end">
                        <a class="btn btn-primary bsapp-insurance-light-blue pt-14 pb-13 px-20 bsapp-rounded-10p bsapp-lh-19 bsapp-w-156p"  href="javascript:;" onclick="InsuranceWindow.init()">מידע נוסף</a>
                    </div>
                </div>                
            </div>       
<!--            <div class="col-md-12 d-flex justify-content-center  mt-30" style="min-height:330px;">-->
<!--                <div class="shadow p-15 p-md-40 rounded d-flex flex-column" style="max-width:1000px;box-shadow:3px 10px 25px rgb(0, 0, 0, 0.16);">-->
<!--                    <div class="mb-30 d-flex justify-content-start">-->
<!--                        <label class="badge badge-white text-primary bsapp-fs-14 bsapp-lh-17 p-0 font-weight-normal bsapp-badge-insurance-new" >New</label>-->
<!--                    </div>-->
<!--                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center align-items-md-start justify-content-md-start ">-->
<!--                        <div class="d-flex align-items-center justify-content-center w-md-300p mb-30 mb-md-auto" >-->
<!--                            <span>-->
<!--                                <img src="/assets/img/logo-shield.svg"  />-->
<!--                            </span>-->
<!--                        </div>  -->
<!--                        <div class="text-center text-black text-md-start w-md-n300p" >-->
<!--                            <h4 class="mb-10 bsapp-fs-22 bsapp-lh-27">Lorem ipsum dolor sit amet, consectetur adipiscing elit</h4>    -->
<!--                            <div class="bsapp-lh-19 bsapp-line-clip-4">-->
<!--                                Lorem ipsum dolor sit amet, consectetur adipiscing elit - de , Websites, etc. - instead of the final real text - until there is real text - until there is real text - until there is real text - until there is real text.-->
<!--                                Lorem ipsum dolor sit amet, consectetur adipiscing elit - de , Websites, etc. - instead of the final real text - until there is real text - until there is real text - until there is real text - until there is real text.-->
<!--                            </div>                        -->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="mt-30 d-flex flex-column flex-md-row justify-content-center justify-content-md-end">-->
<!--                        <a class="btn btn-primary  pt-14 pb-13 px-20 bsapp-rounded-10p bsapp-lh-19"  href="javascript:;" onclick="InsuranceWindow.init()">I want to join</a>                    </div>-->
<!--                </div>                -->
<!--            </div>     -->
        </div>  
    </div>  
</section>



<!-- new meeting modal :: begin -->
<div class="modal px-0 px-sm-auto  text-gray-700  text-start overflow-hidden" tabindex="-1" role="dialog" id="js-modal-insurance" data-backdrop="static" style="font-family:Rubik !important;">
    <div class="modal-dialog modal-lg modal-dialog-centered d-flex align-items-center" style="max-width:626px; height : calc( 100vh - 120px );">
        <div class="modal-content  h-100 bsapp-rounded-8p overflow-hidden bsapp-max-md-h-825p  border-0 "  >
            <div class="modal-body d-flex flex-column justify-content-between p-0 h-100">               


            </div>
        </div>
    </div>
</div>

<div class="d-none js-modal-insurance-loader">
    <div>
        <div class="d-flex justify-content-between">
            <div></div>
            <a href="javascript:;" class="text-dark bsapp-fs-26 p-15" data-dismiss="modal" onclick="InsuranceWindow.resetForms()">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <div class="bsapp-loading-shimmer p-15">
            <div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-50  h-30p">
                    <div class=" h-30p"></div>
                </div>
            </div>
        </div>
        <div class="bsapp-loading-shimmer p-15">
            <div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-50  h-30p">
                    <div class=" h-30p"></div>
                </div>
            </div>
        </div>
        <div class="bsapp-loading-shimmer p-15">
            <div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-100  h-30p">
                    <div class=" h-30p"></div>
                </div>
                <div class="mb-15 w-50  h-30p">
                    <div class=" h-30p"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../app/views/footernew.php';


