<?php
$faqs = [
    [
        "question" => 'למי מיועדת התוכנית?',
        "answer" => 'התוכנית אישית ומיועדת למנויי  BOOSTAPP- מאמנים / מדריכים המחזיקים בתעודת מאמן כושר / מדריך ספורט מוסמך וכן עבור סטודיו לחוגים עד 5 מדריכים ובקבוצות של עד 30 מתאמנים בעת ובעונה אחת.',
    ],
    [
        "question" => 'מה כולל הביטוח?',
        "answer" => 'כיסוי לאחריות החוקית שלכם כלפי מתאמנים ומאמנים אחרים במסגרת הפעילות שלכם כמאמני כושר ומדריכי ספורט.',
    ],
    [
        "question" => 'מי מכוסה?',
        "answer" => 'מאמן כושר גופני ,מאמן כושר במכון כושר, מאמן כושר אישי, מאמן ריצה והליכה, מאמן קפיצה בחבל, מאמן פיתוח גוף, מאמן העוסק באימון משקולות, מאמן גולף, מאמן סקווש,
מאמן ריקוד, מחול ותנועה, מאמן פילאטיס, מאמן פילאטיס מכשירים, מאמן קרוספיט, מאמן יוגה, מאמן טניס שולחן, מאמן התעמלות מכשירים, מאמן התעמלות אמנותית, מאמן אירובי, מאמן ,TRX מאמן כדור סל, מדריך שחיה. ',
    ],
    [
            "question" => 'מה לא ניתן לכיסוי?',
            "answer" => 'אקרובטיקה, אומנויות לחימה, אגרוף מכל סוג, קרב מגע, קיקבוקס, מאמן ג׳ודו, מאמן כדורגל, ספורט אתגרי, ספורט אקסטרים, ספורט פרקור, רולר בלייד, סקייטבורד, קפיצות באנג׳י, אומגה, אופני הרים, אתר הנינג׳ה, טיפוס קירות, גלישת גלים, רחיפה, סנפלינג, מוטור קרוס, ריקוד על עמוד, פוטבול, סקי מים, פעילות במסגרת ו/או עבור מכינה קדם צבאית, פעילות ספורט מקצועני ו/או במסגרת אגודת ספורט כלשהי, תחרויות ספורט מקצועני מכל סוג שהוא, ייעוץ ו/או המלצה לשימוש ו/או מכירה ו/או אספקה מכל סוג של חומרים ממריצים ו/או תוספי מזון מכל סוג שהוא.'
    ]
];
?>
<div class="d-flex flex-column justify-between p-0 h-100" data-context=''>
    <div class="d-flex justify-content-between">
        <a href="javascript:;" class="js-insurance-back py-15 px-16 px-md-30 bsapp-fs-20 text-black bsapp-lh-24" onclick="InsuranceWindow.$bsWizard.bootstrapWizard('previous');">
            <span data-context="js-step-1"><?php echo lang('partnership_migdal_title') ?></span>
            <span data-context="js-step-2"><i class="fal fa-angle-left"></i> <?php echo lang('back_single') ?></span>
            <span data-context="js-step-3"><i class="fal fa-angle-left"></i> <?php echo lang('back_single') ?></span>
            <span data-context="js-step-4"></span>                              
        </a>
        <a href="javascript:;" class="text-dark bsapp-fs-26 py-15 px-16 px-md-30" data-dismiss="modal" onclick="InsuranceWindow.resetForms()">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <!--div class="bsapp-scroll bsapp-overflow-y-auto "  style="height : calc( 100% - 135px ); "-->
    <div class="bsapp-insurance-mid-scroll bsapp-scroll bsapp-overflow-y-auto" style="height : calc( 100% - 135px ); ">
        <div class="pt-15 h-100">
            <div class="px-16 px-sm-30 px-md-85 h-100 bsapp-wizard bsapp-get-started">
                <form class="wizard-card border-0    h-100" data-color="red" id="js-wizard-insurance">
                    <input type="hidden" name="insurance[id]"  value="<?php echo $insurance_insert_id; ?>"
                           <div class="h-100" action="" method="">
                        <div class="h-100">
                            <div class="wizard-navigation position-relative w-300p mx-auto d-none  bsapp-z-99">                                                
                                <ul>
                                    <li>
                                        <a href="#js-step-1" data-toggle="tab">
                                            <div class="icon-circle">
                                            </div>
                                            1
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#js-step-2" data-toggle="tab">
                                            <div class="icon-circle">
                                            </div>
                                            2
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#js-step-3" data-toggle="tab">
                                            <div class="icon-circle">
                                            </div>
                                            3
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#js-step-4" data-toggle="tab">
                                            <div class="icon-circle">

                                            </div>
                                            4
                                        </a>
                                    </li>
                                </ul>
                            </div>                                            
                            <div class="tab-content p-0  h-100 " >
                                <div class="tab-pane h-100 " id="js-step-1">
                                    <div class="text-center">
                                        <div class="d-flex justify-content-around mb-20">
                                            <div><img src="/assets/img/tower-logo.svg"  /></div>
                                            <div class="mie-15"><img src="/assets/img/boostapp-black.png"  /></div>                                            
                                        </div>
                                        <h5 class="mb-15 bsapp-lh-28 text-black font-weight-bold">כמאמני כושר, אתם חשופים לפציעות ותביעות. בדיוק לשם כך פיתחנו עבורכם בשיתוף עם חברת מגדל, ביטוח ייחודי הנותן מענה לסיכונים ולצרכים שלכם.</h5>
                                        <div class="text-center bsapp-lh-19 text-black">
                                            <h6 class="font-weight-medium  mb-15">
                                                <?php echo lang('misdial_allowed_list') ?>
                                            </h6>
                                            <p>
                                            <ul class="text-start">
                                                <li> מדריך ריקודים - מורה לריקוד / מחול / תנועה.</li>
                                                <li> מדריך התעמלות אירובית / TRX / פילאטיס / מאמן כושר / אתלטיקה קלה / ריצה / משקולות / חוגים.</li>
                                                <li> משחקי כדור -  כדורסל / סקווש / פינג פונג.</li>
                                                <li>חוגי ספורט, חוגי שחיה / התעמלות במים.</li>
                                            </ul>
                                            </p>
                                            <h6>
                                                <?= lang('program_benefit') ?>:
                                            </h6>
                                            <p>
                                            <ul class="text-start">
                                                <li> <?= lang('flex_choosing_notice') ?></li>
                                                <li> <?= lang('attractive_premiums') ?></li>
                                                <li><?= lang('coverage_under_third_party_notice') ?></li>
                                            </ul>
                                            </p>
                                        </div>
                                        <div class="row my-20  ">
                                            <div class="col-md-12 text-start">
                                                <h5 class="mb-6 bsapp-fs-18 bsapp-lh-22 text-black font-weight-medium"><?= lang('faq_notice') ?></h5>
                                                <div class="accordion" id="js-accordion-faqs">
                                                    <?php foreach ($faqs as $f_key => $f_answer): ?>
                                                        <div class="mb-6 bsapp-rounded-8p">
                                                            <div class="border border-gray-707070 bsapp-rounded-8p py-14 px-16 position-relative bsapp-fs-16 bsapp-lh-19" id="js-acc-item-<?php echo $f_key; ?>" >

                                                                <a class="stretched-link text-gray-700 font-weight-medium" href="javascript:;" data-toggle="collapse" data-target="#js-acc-collapse-<?php echo $f_key; ?>" >
                                                                    <?php echo $f_answer['question']; ?>
                                                                </a>
                                                            </div>

                                                            <div id="js-acc-collapse-<?php echo $f_key; ?>" class="bsapp-rounded-8p border border-gray-707070 collapse text-gray-700" data-parent="#js-accordion-faqs" >
                                                                <div class="p-10">
                                                                    <?php echo $f_answer['answer']; ?>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    <?php endforeach; ?>
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane h-100 " id="js-step-2">
                                    <div class="text-center h-100 d-flex flex-column">
                                        <h5 class="text-center bsapp-fs-20 text-black ">
                                            <?= lang('integrated_proigram_notice') ?>
                                        </h5>
                                        <div class="row my-30 flex-fill">
                                            <div class="col-md-12 h-100 d-flex flex-column">
                                                <div class="form-group text-start">
                                                    <span class=" mb-12 text-black bsapp-fs-18 bsapp-lh-22"><i class="fal fa-bullseye"></i> <?= lang('total_insurance_period_notice') ?></span>
                                                    <div class="d-flex">
                                                        <label class="btn btn-light  bsapp-label-radios flex-even js-label-radios bsapp-fs-18 bsapp-lh-22 bsapp-rounded-10p py-12" >
                                                            <input  type="radio" name="coverage[warranty_limit]" class="d-none" checked value="500000" onclick="InsuranceWindow.validate(this)"/>
                                                            <span></span><span class="bsapp-option"><span class="font-weight-normal">₪</span>500,000</span>
                                                        </label> 
                                                        <div  style="width:24px;"></div>
                                                        <label class="btn btn-light bsapp-label-radios flex-even js-label-radios bsapp-fs-18 bsapp-lh-22 bsapp-rounded-10p py-12" >
                                                            <input  type="radio" name="coverage[warranty_limit]" class="d-none"  value="1000000" onclick="InsuranceWindow.validate(this)"/>
                                                            <span></span><span class="bsapp-option"><span class="font-weight-normal">₪</span>1,000,000</span>
                                                        </label> 
                                                    </div>
                                                </div>

                                                <div class="form-group text-start">
                                                    <span class=" mb-12 text-black bsapp-fs-18 bsapp-lh-22"><i class="fal fa-dumbbell"></i> <?= lang('crossfit_training_fee') ?></span>
                                                    <div class=" d-flex">
                                                        <label class="btn btn-light  bsapp-label-radios js-label-radios flex-even bsapp-fs-18 bsapp-lh-22 bsapp-rounded-10p py-12"  >
                                                            <input  type="radio" name="coverage[crossfit_training]" class="d-none" checked value="no" onclick="InsuranceWindow.validate(this)"/>
                                                            <span></span><span class="bsapp-option">לא</span>
                                                        </label>
                                                        <div  style="width:24px;"></div>
                                                        <label class="btn btn-light  bsapp-label-radios js-label-radios flex-even bsapp-fs-18 bsapp-lh-22 bsapp-rounded-10p py-12" >
                                                            <input  type="radio" name="coverage[crossfit_training]" class="d-none"  value="yes" onclick="InsuranceWindow.validate(this)" />
                                                            <span></span><span class="bsapp-option">כן</span>
                                                        </label>                                                          
                                                    </div>
                                                </div>
                                                <div class="form-group text-start " id="js-coaches-container">
                                                    <span class=" mb-12 text-black bsapp-fs-18 bsapp-lh-22"><i class="fal fa-users"></i> <?= lang('amount_of_coaches') ?></span>
                                                    <select class="font-weight-bold form-control js-select2-coaches  text-center bsapp-fs-18 bsapp-lh-22 bsapp-rounded-10p py-13" style="height:48px;background:#D8D8D8;border:1px solid #D8D8D8;" onchange="InsuranceWindow.submitInsuranceOfferForm()" name="coverage[no_of_coaches]" >
                                                        <?php for ($int = 1; $int < 6; $int++): ?>
                                                            <option class="bsapp-fs-18 bsapp-lh-22" value="<?php echo $int; ?>"><?php echo $int; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <span class="bsapp-fs-14 bsapp-lh-17 text-gray-700"><?= lang('limited_program_notice') ?></span>
                                                </div>

                                                <div class="d-flex flex-column flex-fill align-items-center justify-content-center">
                                                    <div class="form-group w-100 ">
                                                        <h6 class="mb-15 bsapp-fs-20 bsapp-lh-24 font-weight-bold" style="color:#04346F;"><?= lang('insurance_price') ?></h6>
                                                        <div class="d-none js-loading-spinning">
                                                            <div class="spinner-border font-weight-normal" role="status">
                                                                <span class="sr-only"><?= lang('loading_datatables') ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="py-18 px-18  bsapp-rounded-10p  d-flex align-items-center justify-content-center text-center js-insurance-proposal-amount" style="font: normal normal bold 32px/37px Rubik ;color:#04346F;border:2px solid #04346F;">

                                                        </div>
                                                        <div class="bsapp-fs-14 bsapp-lh-17 text-center text-gray-700 mt-5"><?= lang('insurance_offer_valid') ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane h-100" id="js-step-3">
                                    <div class="text-center">
                                        <h6 class="bsapp-fs-20  text-center text-black">
                                            <?= lang('interested_insurance_notice') ?>
                                        </h6>
                                        <div class="row my-30  ">
                                            <div class="col-md-6 mb-15">
                                                <div class="form-group text-start">
                                                    <span class=" mb-3 bsapp-fs-14 bsapp-lh-17 text-gray-700"><?= lang('business_name_validation') ?></span>
                                                    <div class=" d-flex">
                                                        <input class="form-control text-black border-light bg-light bsapp-lh-19 bsapp-h-48p bsapp-rounded-10p"  name="insurance[business_name]" required value="<?php echo $business_name; ?>"/>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-6 mb-15">
                                                <div class="form-group text-start">
                                                    <span class=" mb-3 bsapp-fs-14 bsapp-lh-17 text-gray-700"><?= lang('telephone') ?></span>
                                                    <div class=" d-flex flex-column">
                                                        <input class="form-control text-black border-light bg-light bsapp-lh-19 bsapp-h-48p bsapp-rounded-10p" name="insurance[phone]"  id="js-insurance-phone" value="<?php echo $phone_number ?? ''; ?>" required value="" />
                                                        <small class="text-danger"></small>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-6 mb-15">
                                                <div class="form-group text-start">
                                                    <span class=" mb-3 bsapp-fs-14 bsapp-lh-17 text-gray-700"><?= lang('business_number_or_id') ?></span>
                                                    <div class=" d-flex">
                                                        <input class="form-control text-black border-light bg-light bsapp-lh-19 bsapp-h-48p bsapp-rounded-10p"  name="insurance[business_number]"   required value="<?php echo $business_number ?? 0; ?>" />
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-6 mb-15">
                                                <div class="form-group text-start">
                                                    <span class=" mb-3 bsapp-fs-14 bsapp-lh-17 text-gray-700"><?= lang('email') ?></span>
                                                    <div class="flex-column d-flex">
                                                        <input class="form-control text-black  border-light bg-light bsapp-lh-19 bsapp-h-48p bsapp-rounded-10p" type="email" name="insurance[email]" required  value="<?php echo $email_id ?? ''; ?>" id="js-insurance-email" value=""  />
                                                        <small class="text-danger"></small>
                                                    </div>
                                                </div> 
                                            </div>
                                            <!--div class="col-md-6 mb-15">
                                                <div class="form-group text-start">
                                                    <span class=" mb-3 bsapp-fs-14 bsapp-lh-17 text-gray-700">Address</span>
                                                    <div class=" d-flex">
                                                        <input class="form-control text-black  border-light bg-light bsapp-lh-19 bsapp-h-48p bsapp-rounded-10p"  name="insurance[address]"  required  value="שדרות ירושליים 12, אילת"/>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-6 mb-20">
                                                <div class="form-group text-start">
                                                    <span class=" mb-3 bsapp-fs-14 bsapp-lh-17 text-gray-700">Start Date</span>
                                                    <div class=" d-flex">
                                                        <input class="form-control text-black  border-light bg-light bsapp-lh-19 bsapp-h-48p bsapp-rounded-10p" type="date" name="insurance[start_date]" required placeholder="Start Date" />
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-12 mb-16 mt-6">
                                                <div class="form-group text-start mb-0">
                                                    <div class="custom-control custom-checkbox bsapp-control-insurance-blue">
                                                        <input type="checkbox" class="custom-control-input" id="js-whether-refused" name="insurance[whether_refused]" checked>
                                                        <label class="custom-control-label text-gray-700 bsapp-lh-19" for="js-whether-refused">Than that I was not refused by another insurance company</label>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-12 mb-16">
                                                <div class="form-group text-start mb-0">
                                                    <div class="custom-control custom-checkbox bsapp-control-insurance-blue">
                                                        <input type="checkbox" class="custom-control-input" id="js-whether-existing" name="insurance[existing_client]">
                                                        <label class="custom-control-label text-gray-700 bsapp-lh-19" for="js-whether-existing">I'm an existing client of Migdal</label>
                                                    </div>
                                                </div> 
                                            </div-->
                                            <div class="col-md-12 mb-16">
                                                <div class="form-group text-start mb-0">
                                                    <div class="custom-control custom-checkbox bsapp-control-insurance-blue">
                                                        <input type="checkbox" class="custom-control-input" id="js-required-check" name="insurance[checkbox_required]">
                                                        <label class="custom-control-label text-gray-700 bsapp-lh-19" for="js-required-check"><?= lang('insurance_accept_terms_notice') ?></label>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="text-center d-none js-error-on-submit">
                                            <label class="text-danger"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane h-100" id="js-step-4">
                                    <div class="text-center h-100">
                                        <div class="px-15 px-sm-30 h-100 px-md-60 text-center d-flex justify-content-center flex-column">
                                            <span class="mb-30 d-flex justify-content-center">
                                                <lottie-player  id="js-checkmark-circle" data-src="js/lf30_editor_dbxsyruo.json" background="transparent"  speed="1.25"  style="width: 169px; height: 169px;" autoplay  ></lottie-player>
                                            </span>
                                            <h4 class="mb-18 bsapp-fs-24 bsapp-lh-28 text-gray-700 font-weight-bold"><?= lang('successfuly_submitted_notice') ?></h4>
                                            <p class="bsapp-fs-18 bsapp-lh-22 text-gray-700"><?= lang('get_back_notice') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>                                                
                        </div>
                    </div>
                </form>
            </div>
        </div> 

        <div class="d-flex justify-content-center flex-column  px-16 px-sm-30 px-md-85  bsapp-z-99 bg-white py-4" >                   
            <a href="javascript:;" class="btn  bsapp-insurance-btn-blue  js-insurance-next bsapp-rounded-10p bsapp-fs-16 bsapp-lh-19 pt-14 pb-13 mb-20 mb-md-30" onclick="InsuranceWindow.$bsWizard.bootstrapWizard('next');">
                <span data-context="js-step-1"><?= lang('move_to_offer') ?></span>
                <span data-context="js-step-2"><?= lang('continue_main') ?></span>
                <span data-context="js-step-3"><?= lang('send') ?></span>
            </a>
        </div>
    </div>
    <script>
        InsuranceWindow.initFormWizard();
    </script>