<?php
require_once '../../app/initcron.php';
require_once __DIR__.'/../Classes/CompanyProductSettings.php';

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
$ClassSettingsInfo = DB::table('classsettings')->where('CompanyNum', '=', $CompanyNum)->first();
$companyProductSettings = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum);
$manageMemberships = $companyProductSettings->manageMemberships ?? 0;
//if (Auth::user()->BrandsMain == '0') {
//    $TrueCompanyNum = $CompanyNum;
//} else {
//    $TrueCompanyNum = Auth::user()->BrandsMain;
//}
$TrueCompanyNum = $CompanyNum;
?>
<link href="../../assets/css/smart_wizard.css?<?php echo date('YmdHis'); ?>" rel="stylesheet" type="text/css" />
<link href="../../assets/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
<script type="text/javascript" src="../../assets/js/jquery.smartWizard.js"></script>  

<link href="/office/assets/css/create-new-class.css" rel="stylesheet">

<?php
$GroupNumber = rand(1, 9999999);
$GroupNumber;
?>
<style>
    #liveClassLink:focus:invalid, #meetingNumber:focus:invalid {
        box-shadow: 0px 0px 4px 4px rgba(200, 0, 0, 0.41);
        border: 1px solid #ff000085;
    }
    .custom-radio .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #5cb85c;
    }
</style>

<div id="smartwizard">
    <ul class="MenuWizard">
        <li><a href="#step-1">שלב 1<br /><small>הגדרת שיעור</small></a></li>
        <li><a href="#step-2">שלב 2<br /><small>הגדרת תזמון</small></a></li>
        <li><a href="#step-3">שלב 3<br /><small>הגדרת רישום</small></a></li>
        <li><a href="#step-4">שלב 4<br /><small>הגדרת ביטולים ותזכורות</small></a></li>
        <li><a href="#step-5">שלב 5<br /><small>הגדרת תצוגה</small></a></li>
    </ul>

    <div>
        <div id="step-1" style="padding-top: 10px;">
            <h4><strong>הגדרת שיעור</strong></h4>

            <div id="form-step-0" role="form" data-toggle="validator">

                <div class="row">
                    <div class="col-md-4">	 
                        <div class="form-group">
                            <label>מיקום שיעור</label>
                            <select class="form-control js-example-basic-single text-right" id="ChooseFloorForTask" name="FloorId" data-placeholder="בחר מיקום לשיעור" style="width: 100%" >
                                <?php
                                $SectionInfos = DB::table('sections')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('id', 'ASC')->get();
                                foreach ($SectionInfos as $SectionInfo) {
                                    ?>  
                                    <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Title; ?></option>	  
                                    <?php
                                }
                                ?>  
                            </select> 
                        </div> 
                    </div> 

                    <div class="col-md-4">	 
                        <div class="form-group">
                            <label>סוג שיעור</label>
                            <select class="form-control js-example-basic-single select2Desk text-right" name="ClassNameType" id="ClassNameTypeNew"  data-placeholder="בחר סוג שיעור" style="width:100%;" required>
                                <option value=""></option>    
                                <?php
                                $SectionInfos = DB::table('class_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('EventType','=','0')->orderBy('Type', 'ASC')->get();
                                foreach ($SectionInfos as $SectionInfo) {
                                    ?>  
                                    <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Type; ?></option>	  
                                    <?php
                                }
                                ?>  
                            </select> 
                            <div class="help-block with-errors"></div>       
                        </div> 
                    </div>  

                    <div class="col-md-4">	 
                        <div class="form-group">
                            <label>כותרת השיעור</label>
                            <input type="text" class="form-control" name="ClassName" id="ClassNameNew" required>
                            <div class="help-block with-errors"></div>      
                        </div>      

                    </div>       
                </div> 

                <div class="row">
                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>מדריך</label>
                            <select class="form-control js-example-basic-single select2Desk text-right" name="GuideId" id="GuideId"  data-placeholder="בחר מדריך לשיעור" required style="width:100%;" >
                                <option value=""></option>    
                                <?php
                                if (Auth::user()->BrandsMain == '0') {
                                    $SectionInfos = DB::table('users')->where('CompanyNum', '=', $TrueCompanyNum)->where('ActiveStatus', '=', '0')->where('Coach', '=', '1')->orderBy('display_name', 'ASC')->get();
                                } else {
                                    $SectionInfos = DB::table('users')->where('CompanyNum', '=', $TrueCompanyNum)->where('ActiveStatus', '=', '0')->where('Coach', '=', '1')->orderBy('display_name', 'ASC')->get();
                                }
                                foreach ($SectionInfos as $SectionInfo) {
                                    ?>  
                                    <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->display_name; ?></option>	  
                                    <?php
                                }
                                ?>  
                            </select> 
                            <div class="help-block with-errors"></div>       
                        </div>  
                    </div>  

                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>עוזר מדריך</label>
                            <select class="form-control js-example-basic-single select2Desk text-right" name="ExtraGuideId" id="ExtraGuideId" data-placeholder="בחר מדריך לשיעור" style="width:100%;" >
                                <option value=""></option>    
                                <?php
                                if (Auth::user()->BrandsMain == '0') {
                                    $SectionInfos = DB::table('users')->where('CompanyNum', '=', $TrueCompanyNum)->where('ActiveStatus', '=', '0')->where('Coach', '=', '1')->orderBy('display_name', 'ASC')->get();
                                } else {
                                    $SectionInfos = DB::table('users')->where('CompanyNum', '=', $TrueCompanyNum)->where('ActiveStatus', '=', '0')->where('Coach', '=', '1')->orderBy('display_name', 'ASC')->get();
                                }
                                foreach ($SectionInfos as $SectionInfo) {
                                    ?>  
                                    <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->display_name; ?></option>	  
                                    <?php
                                }
                                ?>  
                            </select> 
                        </div>  
                    </div>       


                    <div class="col-md-4">	 
                        <div class="form-group">
                            <label>מוצג באפליקציה?</label>
                            <select class="form-control js-example-basic-single text-right" name="ShowApp" id="ShowApp" >
                                <option value="1" selected>כן</option> 
                                <option value="2">לא</option>     
                            </select> 
                        </div> 
                    </div>
                </div>


                <div class="select-lesson-type">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group zoomRadioBtn">
                                <input type="radio" id="studioClass" checked name="liveClass" value="studio">
                                <label for="online">שיעור סטודיו</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group zoomRadioBtn">
                                <input type="radio" id="zoom" name="liveClass" value="zoom">
                                <label for="zoom">שיעור zoom</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group zoomRadioBtn">
                                <input type="radio" id="online" name="liveClass" value="online">
                                <label for="online">שיעור אונליין</label>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row" id="liveSection" style="display: none">
                    <div class="col-md-12">
                        <label>אופן שליחת הקישור</label>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 15px;">

                        <div class="custom-control custom-radio custom-control-inline">

                            <input type="radio" id="customRadioInline1" value="1" name="onlineSendType" class="custom-control-input" <?php if (!empty($GetClassInfo->onlineSendType) && $GetClassInfo->onlineSendType == 1) echo 'checked'; ?>>
                            <label class="custom-control-label" for="customRadioInline1">Sms</label>
                        </div>

                        <div class="custom-control custom-radio custom-control-inline">

                            <input type="radio" id="customRadioInline2" value="2" name="onlineSendType" <?php if (empty($GetClassInfo->onlineSendType) || $GetClassInfo->onlineSendType == 2) echo 'checked'; ?> class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline2">Email</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>אפשרות הרשמה</label>
                            <select class="form-control js-example-basic-single text-right" name="registerLimit" id="registerLimit">
                                <option value="1" selected>לפי הגבלת מנוי</option>
                                <option value="2">הרשמה חופשית (לכל סוגי המנויים)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group">
                            <label>לינק לשיעור אונליין</label>
                            <input type="url" class="form-control" name="liveClassLink" id="liveClassLink" dir="ltr">      
                        </div>
                    </div>
                </div>


                <div class="zoomSection row" id="zoomSection" style="display: none">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>מספר פגישה</label>
                            <input type="tel" class="form-control" placeholder="" name="meetingNumber" id="meetingNumber">
                        </div>
                    </div>

                    <!--    <div class="col-md-4">-->
                    <!--        <div class="repalce-check-box form-group">-->
                    <!--            <div style="display: flex"> -->
                    <!--              <input type="checkbox" id="AllowWatchOutsideApp" name="AllowWatchOutsideApp" > -->
                    <!--              <span class="checkmark"></span>-->
                    <!--              <label>אפשר צפייה מחוץ לאפליקציה</label> -->
                    <!--            </div>-->
                    <!--            <div style="display: flex"> <input type="text" class="form-control"  name="watchOutsideApp" id="watchOutsideApp" dir="rtl" disabled>  -->
                    <!--              <div class="copy-to" onclick="copyToClipboard('#watchOutsideApp')">-->
                    <!--                <i class="far fa-copy"></i>-->
                    <!--              </div>  -->
                    <!--            </div>-->
                    <!--            <div style="font-size: 10.3px">במקרה וקבעת עלות לשיעור המצטרפים יצטרכו לשלם</div>-->
                    <!--        </div>-->
                    <!--    </div>-->

                    <!--    <div class="col-md-4">-->
                    <!--        <div class="repalce-check-box form-group">-->
                    <!--            <div style="display: flex"> -->
                    <!--            <input type="checkbox" id="RecordAndStoreVideo" name="RecordAndStoreVideo" > -->
                    <!--            <span class="checkmark"></span>-->
                    <!--            <label>הקלט ואחסן בספריית וידאו</label> </div>-->
                    <!--            <input type="text" class="form-control"  placeholder="בחר תיקייה" name="RecordFile" id="RecordFile" dir="rtl">-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>סיסמה</label>
                            <input class="form-control" placeholder="" name="ZoomPassword" id="password">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>אופי השיעור</label>
                            <select class="form-control js-example-basic-single text-right" name="registerLimitZoom" id="registerLimitZoom">
                                <option value="1" selected>שיעור בעלות (לפי המינוי)</option>
                                <option value="2">הרשמה חופשית (לכל סוגי המנויים)</option>
                            </select>
                        </div>
                    </div>
                    <?php if ($SettingsInfo->YaadNumber != 0 || $SettingsInfo->MeshulamAPI != 0) { ?>
                        <div class="col-md-4 singleReg">
                            <div class="repalce-check-box form-group">
                                <div style="display: flex">
                                    <input type="checkbox" id="AllowSingleEntry" name="AllowSingleEntry" >
                                    <span class="checkmark"></span>
                                    <label>אפשר לרכוש כניסה בודדת לשיעור</label>
                                </div>
                                <input type="number" class="form-control" placeholder="סכום לחיוב (₪)" name="singleEntryRate" id="singleEntryRate">
                            </div>
                        </div>
                    <?php } ?>


                    <!--    <div class="col-md-4">-->
                    <!--        <div class="form-group">-->
                    <!--            <label>אפשר צ'ט במהלך שידור*</label>-->
                    <!--            <select class="form-control js-example-basic-single text-right" name="AllowChat" id="AllowChat" dir="rtl">-->
                    <!--                <option value="0" selected>אל תאפשר</option>-->
                    <!--                <option value="1">מארח בלבד</option>-->
                    <!--                <option value="2">אפשר עם כולם</option>-->
                    <!--            </select>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-4">-->
                    <!--        <div class="form-group">-->
                    <!--            <label>אפשר שיתוף וידאו*</label>-->
                    <!--            <select class="form-control js-example-basic-single text-right" name="AllowVideoShare" id="AllowVideoShare" dir="rtl">-->
                    <!--                <option value="0" selected>אל תאפשר</option>-->
                    <!--                <option value="1">מארח בלבד</option>-->
                    <!--                <option value="2">אפשר עם כולם</option>-->
                    <!--            </select>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-4">-->
                    <!--        <div class="form-group">-->
                    <!--            <label>אפשר שימוש בסאונד*</label>-->
                    <!--            <select class="form-control js-example-basic-single text-right" name="AllowSound" id="AllowSound" dir="rtl">-->
                    <!--                <option value="0" selected>אל תאפשר</option>-->
                    <!--                <option value="1">מארח בלבד</option>-->
                    <!--                <option value="2">אפשר עם כולם</option>-->
                    <!--            </select>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div style="font-size: 10.3px; margin-right: 2%;">*הגדרה ראשונית (ניתן לשינוי במהלך השידור)</div>-->

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>תמונת שיעור</label>
                            <div class="avatar-container">
                                <button type="button" class="btn btn-light edit-avatar" data-ip-modal="#itemModal" title="ערוך תמונה"><i class="fas fa-pencil-alt"></i></button>
                                <?php //if (!empty($Supplier->logoImg)) {  ?>
                                <!-- <img src="files/items/<?php //echo $Supplier->logoImg;       ?>" id="avatar"> -->
                                <?php //} else {  ?>
                                <img src="/office/assets/img/default.png" id="avatar">
                                <?php //}  ?>
                            </div>
                            <hr>
                        </div>
                        <input type="hidden" id="pageImgPath" name="pageImgPath" value=""/>
                    </div>
                </div>
                <?php include "../UploadModal.php" ?>




            </div>

        </div>
        <div id="step-2" style="padding-top: 10px;">
            <h4><strong>הגדרת תזמון</strong></h4>
            <div id="form-step-1" role="form" data-toggle="validator">

                <?php

                function blockMinutesRound($hour, $minutes = '5', $format = "H:i") {
                    $seconds = strtotime($hour);
                    $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
                    return date($format, $rounded);
                }
                ?>  

                <div class="row">
                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>תחילת שיבוץ השיעור</label>
                            <input name="SetDate" id="SetDate" type="date"  value="<?php echo date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime("+1 year")) ?>" class="form-control" required>
                            <div class="help-block with-errors"></div>       
                        </div>  
                    </div>

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>יום השיעור</label>
                            <select name="Day" id="DayNew" data-placeholder="בחר יום" class="form-control" style="width:100%;" required>
                                <option value="">בחר יום</option>  

                                <option value="0">ראשון</option>
                                <option value="1">שני</option>
                                <option value="2">שלישי</option>
                                <option value="3">רביעי</option>
                                <option value="4">חמישי</option>
                                <option value="5">שישי</option>
                                <option value="6">שבת</option>

                            </select>
                            <div class="help-block with-errors"></div> 
                        </div>  
                    </div>

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>שעת התחלה</label>
                            <input name="SetTime" id="SetTimeNew" type="time" step="300" value="<?php echo blockMinutesRound(date('H:i')); ?>" class="form-control" required> 
                            <div class="help-block with-errors"></div>     
                        </div> 
                    </div>

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>שעת סיום</label> 
                            <input name="SetToTime" id="SetToTimeNew" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes"))); ?>" value="<?php echo blockMinutesRound(date(('H:i'), strtotime("+" . $ClassSettingsInfo->EndClassTime . " minutes"))); ?>" class="form-control" required>  
                            <div class="help-block with-errors"></div>     
                        </div> 
                    </div>      


                </div>  

                <div class="row">
                    <div class="col-md-4">	        
                        <div class="form-group">
                            <label>אופי השיעור</label>
                            <select class="form-control text-right" name="ClassType" id="ClassTypeNew" >
                                <option value="1" selected>שיעור קבוע</option>
                                <option value="2">שיעור מוגבל בחזרות</option>
                                <option value="3">שיעור חד פעמי</option>
                                <option value="4" disabled>שיעור אחת ל-X שבועות</option>     

                            </select>  
                        </div>
                    </div>

                    <div id="DivClassTypeNew" class="col-md-3" style="display: none;">	        
                        <div class="form-group">
                            <label>מספר חזרות (בשבועות)</label>
                            <input type="number" class="form-control" name="ClassCount" id="ClassCountNew" value="" min="1" onkeypress='validate(event)'> 
                            <div class="help-block with-errors"></div>      
                        </div>
                    </div>  


                    <div id="DivClassTypeNew4" class="col-md-3" style="display: none;">	        
                        <div class="form-group">
                            <label>הגדר כל X שבועות</label>
                            <input type="number" class="form-control" name="ClassRepeat" id="ClassRepeat" value="" min="1" onkeypress='validate(event)'>
                            <div class="help-block with-errors"></div>      
                        </div>
                    </div>          


                </div> 

            </div>
        </div>


        <div id="step-3" style="padding-top: 10px;">
            <h4><strong>הגדרת רישום</strong></h4>
            <div id="form-step-2" role="form" data-toggle="validator">


                <div class="row">

                    <div class="col-md-6">	     
                        <div class="form-group">
                            <label>מקסימום משתתפים</label>
                            <input type="number" min="1" class="form-control" name="MaxClient" id="MaxClientNew" value="<?php echo $ClassSettingsInfo->MaxClient ?>" onkeypress='validate(event)' required>  
                            <div class="help-block with-errors"></div>    
                        </div> 
                    </div>
                    <?php if($manageMemberships == 1) { ?>
                    <div class="col-md-6">

                        <div class="form-group">
                            <label>הגבל הרשמה לפי סוג מנוי?</label>
                            <select class="form-control text-right" name="ClassLimitTypes" id="ClassLimitTypes">
                                <option value="0" selected>לא (מוצג לכולם)</option>
                                <option value="1">כן</option>
                            </select>
                        </div> 

                    </div>  
                    <?php } else { ?>
                        <input type="hidden" name="ClassLimitTypes" value="0">
                    <?php } ?>

                </div>   


                <div id="DivClassLimitTypes" style="display: none;">

                    <div id="GetGroupId">    

                    </div>     
                    <a class="btn btn-dark btn-sm" href="javascript:void(0);" onclick="addElementgroup();">הוסף מגבלת הרשמה חדשה +</a>   
                    <input type="hidden" value="0" id="theValueGroup" name="tGroups"/>         

                </div>                    




                <hr>             

                <div class="row">

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>הגדר מינימום בשיעור?</label>
                            <select class="form-control text-right" name="MinClass" id="MinClassNew" >
                                <option value="0" selected>לא</option>
                                <option value="1">כן</option>
                            </select>  
                        </div>  
                    </div>

                    <div class="col-md-3 DivMinClassNumNew" style="display: none;">	     
                        <div class="form-group">
                            <label>מינימום משתתפים</label>
                            <input type="number" class="form-control" name="MinClassNum" id="MinClassNumNew" value="<?php echo $ClassSettingsInfo->MinClient ?>" onkeypress='validate(event)'> 
                            <div class="help-block with-errors"></div>  
                        </div>  
                    </div>

                    <div class="col-md-3 DivMinClassNumNew" style="display: none;">	    
                        <div class="form-group">
                            <label>זמן בדיקה לפני השיעור</label>
                            <input type="text" class="form-control" name="ClassTimeCheck" id="ClassTimeCheckNew" value="<?php echo $ClassSettingsInfo->CheckMinClient ?>"> 
                            <div class="help-block with-errors"></div>    
                        </div>
                    </div>   
                    <div class="col-md-3 DivMinClassNumNew" style="display: none;">	   
                        <div class="form-group">
                            <label>אפשרות</label>
                            <select class="form-control text-right" name="ClassTimeTypeCheck" id="ClassTimeTypeCheckNew" >
                                <option value="1" <?php
                                if ($ClassSettingsInfo->CheckMinClientType == '1') {
                                    echo 'selected';
                                } else {
                                    
                                }
                                ?> >דקות</option>
                                <option value="2" <?php
                                if ($ClassSettingsInfo->CheckMinClientType == '2') {
                                    echo 'selected';
                                } else {
                                    
                                }
                                ?> >שעות</option>         
                            </select> 
                        </div> 
                    </div>     


                </div>                     


                <hr>    

                <div class="row">

                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>לאפשר רשימת המתנה?</label>
                            <select class="form-control text-right" name="ClassWating" id="ClassWatingNew" >
                                <option value="0" selected>כן</option>
                                <option value="1">לא</option>
                            </select>  

                        </div>  
                    </div>	  


                    <div class="col-md-4 WatingListDiv" style="display: block;">	     
                        <div class="form-group">
                            <label>הגבלת רשימת המתנה?</label>
                            <select class="form-control text-right" name="MaxWatingList" id="WatingListActNew" >
                                <option value="0">כן</option>
                                <option value="1" selected>לא</option>
                            </select>  

                        </div>  
                    </div>    

                    <div class="col-md-4 WatingListNumDiv"  style="display: none;">	     
                        <div class="form-group">
                            <label>מקסימום ממתינים?</label>
                            <input type="number" class="form-control" name="NumMaxWatingList" id="WatingListNumNew" value="" onkeypress='validate(event)'> 
                            <div class="help-block with-errors"></div>       
                        </div> 
                    </div>


                </div>     


                <hr>

                <div class="row">

                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>הגבלת רישום לפי דרגה?</label>
                            <select class="form-control js-example-basic-single select2LimitLevel text-right" data-placeholder="בחר דרגות"  name="LimitLevel[]" id="LimitLevel"   multiple="multiple" data-select2order="true" style="width: 100%;">
                                <option value="0" selected>כל הדרגות</option>
                                <?php
                                $ClinetLevels = DB::table('clientlevel')->where('CompanyNum', '=', $CompanyNum)->get();
                                foreach ($ClinetLevels as $ClinetLevel) {
                                    ?>  
                                    <option value="<?php echo $ClinetLevel->id; ?>" ><?php echo $ClinetLevel->Level; ?></option>	  
                                    <?php
                                }
                                ?>         
                            </select>  
                        </div>  
                    </div>                      
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>הגבלת רישום לפי מין?</label>
                            <select class="form-control text-right" name="GenderLimit" id="GenderLimit" >
                                <option value="0" selected>הכל</option>
                                <option value="1">גברים</option>
                                <option value="2">נשים</option>  
                            </select>  
                        </div>  
                    </div>  

                    <div class="col-md-4" style="display: none;">

                        <div class="form-group">
                            <label>שיעור חופשי ללא חיוב?</label>
                            <select class="form-control text-right" name="FreeClass" id="FreeClass">
                                <option value="0" selected>לא</option>
                                <option value="1">כן לבעלי מנוי בתוקף/יתרה</option>
                                <option value="2">כן לכלל הלקוחות</option>  
                            </select>  
                        </div> 

                    </div>         

                    <input type="hidden" name="FreeClass" value="0">        
                </div>                         

            </div>
        </div>

        <div id="step-4" style="padding-top: 10px;">
            <h4><strong>הגדרת ביטולים ותזכורות</strong></h4>

            <div id="form-step-3" role="form" data-toggle="validator">


                <div class="row">
                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>שלח תזכורת ללקוח?</label>
                            <select class="form-control text-right" name="SendReminder" id="SendReminderNew">
                                <option value="0" selected>כן</option>
                                <option value="1">לא</option>
                            </select>  
                        </div>  
                    </div>

                    <div class="col-md-4 SendReminderNew">	     
                        <div class="form-group">
                            <label>הגדר זמן לשליחת התזכורת</label>
                            <select class="form-control text-right" name="TypeReminder" id="TypeReminderNew">
                                <option value="1" selected>ביום השיעור</option>
                                <option value="2">יום לפני השיעור</option>
                            </select>  

                        </div>  
                    </div>

                    <div class="col-md-4 SendReminderNew">	     
                        <div class="form-group">
                            <label>הגדר שעת שליחת התזכורת</label>
                            <input type="time" class="form-control" name="TimeReminder" id="TimeReminderNew" step="300" value="" max="" min="" required>
                            <div class="help-block with-errors"></div>      
                        </div> 
                    </div>


                </div>                        

                <hr>
                <div class="row">
                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>בחר חוק ביטולים</label>
                            <select class="form-control text-right" name="CancelLaw" id="CancelLawNew">
                                <option value="1" selected>ביום השיעור עד שעה</option>
                                <option value="2">ביום לפני השיעור עד שעה</option>
                                <option value="3">ביום לבחירה עד שעה</option>
                                <option value="4">לא ניתן לביטול באפליקציה</option>       
                                <option value="5">ביטול חופשי</option>       
                            </select>  
                        </div>  
                    </div>

                    <div id="DivCancelLawNew3" class="col-md-4" style="display: none;">	     
                        <div class="form-group">
                            <label>בחר יום לפני יום השיעור</label>
                            <select name="CancelDay" id="CancelDayNew" data-placeholder="בחר יום" class="form-control" style="width:100%;">
                                <option value="">בחר יום</option>  


                            </select>
                            <div class="help-block with-errors"></div>       
                        </div>  
                    </div>   


                    <div id="DivCancelLawNew" class="col-md-4">	     
                        <div class="form-group">
                            <label>הגדר עד שעה לביטול</label>
                            <input name="CancelTillTime" id="CancelTillTimeNew" type="time" step="300" min="" value="" class="form-control" required> 
                            <div class="help-block with-errors"></div>       
                        </div> 
                    </div>

                </div>   


                <div id="DivCancelLawNew6" class="alertb alert-warning" style="display: none;">שים לב! יש לבחור <u>יום</u> לפני יום השיעור שנקבע.<br>
                    לדוגמא: שיעורי יום ראשון בשעה 09:00 בבוקר ניתן לבטל עד שישי בשעה 12:00.</div> 


                <div id="DivCancelLawNew4" class="alertb alert-warning" style="display: none;">שים לב! באפשרות זו, ללקוח לא יופיע כפתור ביטול באפליקציה לאחר הזמנת שיעור זה.</div>   

                <div id="DivCancelLawNew5" class="alertb alert-warning" style="display: none;">שים לב! הלקוח יוכל לבטל את השיעור בכל שלב וללא חיוב.</div>                          
                <hr>

                <div class="row">
                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>חסימת כפתור ביטול?</label>
                            <select class="form-control text-right" name="StopCancel" id="StopCancel">
                                <option value="0">כן</option>
                                <option value="1" selected>לא</option>
                            </select>  
                        </div>  
                    </div>

                    <div class="col-md-4 StopCancel" style="display: none;">	     
                        <div class="form-group">
                            <label>הגדר זמן לפני השיעור</label>
                            <input type="number" class="form-control" name="StopCancelTime" id="StopCancelTime" value="10" onkeypress='validate(event)'>        
                            <div class="help-block with-errors"></div>        
                        </div> 
                    </div>

                    <div class="col-md-4 StopCancel" style="display: none;">	     
                        <div class="form-group">
                            <label>אפשרות</label>
                            <select class="form-control text-right" name="StopCancelType" id="StopCancelType">
                                <option value="1" selected>דקות</option>
                                <option value="2">שעות</option>
                            </select>  

                        </div> 
                    </div>
                </div>   

                <div class="alertb alert-warning StopCancel" style="display: none;">שים לב! הלקוח לא יוכל לבטל את השיעור מעבר לזמן שצויין.</div>                          




            </div>
        </div>


        <div id="step-5" style="padding-top: 10px;" class="">
            <h4><strong>הגדרת תצוגה</strong></h4>

            <div id="form-step-4" role="form" data-toggle="validator">
                <div class="row">

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>להציג כמות משתתפים?</label>
                            <select class="form-control text-right" name="ShowClientNum" id="ShowClientNum">
                                <option value="0">כן</option>
                                <option value="1" selected>לא</option>
                            </select>    
                        </div> 
                    </div>

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>להציג שמות משתתפים?</label>
                            <select class="form-control text-right" name="ShowClientName" id="ShowClientName" >
                                <option value="0">כן</option>
                                <option value="1" selected>לא</option>
                            </select>
                        </div> 
                    </div>   

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>להציג סדר ר.המתנה?</label>
                            <select class="form-control text-right" name="WatingListOrederShow" id="WatingListOrederShow" >
                                <option value="0">כן</option>
                                <option value="1" selected>לא</option>
                            </select>  
                        </div> 
                    </div> 

                    <div class="col-md-3">	     
                        <div class="form-group">
                            <label>דרגת השיעור</label>
                            <select class="form-control text-right" name="ClassLevel">
                                <option value="0" selected>ללא דרגת שיעור</option>
                                <option value="1">שיעור למתחילים</option>
                                <option value="2">שיעור בקצב דינאמי</option>
                                <option value="3">שיעור ברמה מתקדמת</option>
                            </select>  
                        </div>  
                    </div>             


                </div>  

                <div class="row">
                    <div class="col-md-12">	                        
                        <div class="form-group">
                            <label>הצג בחירת מכשירים</label>
                            <select class="form-control js-example-basic-single select2Desk text-right" name="ClassDevice" id="ClassDevice" data-placeholder="בחר טבלת מכשירים"  style="width: 100%;" >
                                <option value=""></option>    
                                <?php
                                $SectionInfos = DB::table('numbers')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Name', 'ASC')->get();
                                foreach ($SectionInfos as $SectionInfo) {
                                    ?>  
                                    <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Name; ?></option>	  
                                    <?php
                                }
                                ?>  
                            </select>
                        </div>
                    </div>                       
                </div>                       

                <hr>


                <div class="row">
                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>אפשרת הזמנת שיעור בזמן מוגדר</label>
                            <select class="form-control text-right" name="OpenOrder" id="OpenOrder" >
                                <option value="0">כן, הגדר זמן</option>
                                <option value="1" selected>לא, אפשר בכל זמן</option>
                            </select>  
                        </div>  
                    </div>

                    <div class="col-md-4 OpenOrder" style="display: none;">	     
                        <div class="form-group">
                            <label>הגדר זמן לפני השיעור</label>
                            <input type="number" class="form-control" name="OpenOrderTime" id="OpenOrderTime" value="10" onkeypress='validate(event)'>        
                            <div class="help-block with-errors"></div>        
                        </div> 
                    </div>

                    <div class="col-md-4 OpenOrder" style="display: none;">	     
                        <div class="form-group">
                            <label>אפשרות</label>
                            <select class="form-control text-right" name="OpenOrderType" id="OpenOrderType">
                                <option value="1" selected>דקות</option>
                                <option value="2">שעות</option>
                            </select>  

                        </div> 
                    </div>
                </div>   

                <div class="alertb alert-warning OpenOrder" style="display: none;">שים לב! הלקוח לא יוכל להזמין את השיעור לפני הזמן שצויין.</div>                          


                <div class="row">
                    <div class="col-md-4">	     
                        <div class="form-group">
                            <label>חסימת הזמנת שיעור בזמן מוגדר</label>
                            <select class="form-control text-right" name="CloseOrder" id="CloseOrder">
                                <option value="0">כן, הגדר זמן</option>
                                <option value="1" selected>לא, אפשר בכל זמן</option>
                            </select>  
                        </div>  
                    </div>

                    <div class="col-md-4 CloseOrder" style="display: none;">	     
                        <div class="form-group">
                            <label>הגדר זמן לפני השיעור</label>
                            <input type="number" class="form-control" name="CloseOrderTime" id="CloseOrderTime" value="10" onkeypress='validate(event)'>        
                            <div class="help-block with-errors"></div>        
                        </div> 
                    </div>

                    <div class="col-md-4 CloseOrder" style="display: none;">	     
                        <div class="form-group">
                            <label>אפשרות</label>
                            <select class="form-control text-right" name="CloseOrderType" id="CloseOrderType">
                                <option value="1" selected>דקות</option>
                                <option value="2">שעות</option>
                            </select>  

                        </div> 
                    </div>
                </div>   

                <div class="alertb alert-warning CloseOrder" style="display: none;">שים לב! הלקוח לא יוכל להזמין את השיעור מעבר לזמן שצויין, כולל רישום לרשימת המתנה.</div>                  


            </div>
            <input type="hidden" name="CalendarId" value="">
            <input type="hidden" name="FixGroupNumber" value="<?php echo $GroupNumber; ?>">
        </div>

    </div>


    <style>

        .select2-results__option[aria-selected=true] {
            display: none;
        }

        hr.hrclass {
            height: 1px;
            border: 0;
            color: #48AD42;
            background-color: #48AD42;
        }    

    </style>


    <?php
    if ($ClassSettingsInfo->ReminderTimeType == '1') {
        $ReminderTimeType = 'minutes';
    } else {
        $ReminderTimeType = 'hours';
    }

    if ($ClassSettingsInfo->CancelTimeType == '1') {
        $CancelTimeType = 'minutes';
    } else {
        $CancelTimeType = 'hours';
    }
    ?>       


    <script>
        $(document).ready(function () {
            $(".selectAddItem").select2({theme: "bootstrap", placeholder: "בחר"});
            $(".select2Desk").select2({theme: "bootstrap", placeholder: "בחר", allowClear: "true"});
            $(".select2LimitLevel").select2({theme: "bootstrap", placeholder: "בחר"});
        });



        $("#DayNew").change(function () {

            var Id = this.value;
            if (Id == '0') {
                /// ראשון    
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0" disabled>ראשון</option>');
            } else if (Id == '1') {
                /// שני    
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1" disabled>שני</option>');
            } else if (Id == '2') {
                /// שלישי    
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2" disabled>שלישי</option>');
            } else if (Id == '3') {
                /// רביעי    
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3" disabled>רביעי</option>');
            } else if (Id == '4') {
                /// חמישי    
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4" disabled>חמישי</option>');
            } else if (Id == '5') {
                /// שישי    
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5" disabled>שישי</option>');
            } else if (Id == '6') {
                /// שבת    
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6" disabled>שבת</option>');
            } else {
                $('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option>');
            }



            //.val('whatever')    

        });


        $('#LimitLevel').on('select2:selecting', function (e) {
            var selected = $(this).val();

            if (selected != null)
            {
                if (selected.indexOf('0') >= 0) {
                    $(this).val('0').select2({theme: "bootstrap", placeholder: "בחר סוג שיעור", });
                }
            }

        });

        $('#LimitLevel').on('select2:open', function () {
            // get values of selected option
            var values = $(this).val();
            // get the pop up selection
            var pop_up_selection = $('.select2-results__options');
            if (values != null) {
                // hide the selected values
                pop_up_selection.find("li[aria-selected=true]").hide();
            } else {
                // show all the selection values
                pop_up_selection.find("li[aria-selected=true]").show();
            }
        });




        $("#ClassNameTypeNew").change(function () {

            var ClassName = $('#ClassNameTypeNew').select2('data');
            $('#ClassName').val(ClassName[0].text);

            if ($('#ClassNameTypeNew option:selected').length > 0) {
                $('#ClassNameNew').val(ClassName[0].text);
            } else {
                $('#ClassNameNew').val('');
            }

        });


        $("#ClassTypeNew").change(function () {

            var Id = this.value;
            if (Id == '1') {
                DivClassTypeNew.style.display = "none";
                DivClassTypeNew4.style.display = "none";
                $('#ClassCountNew').val('999');
                $('#ClassRepeat').val('');
                $("#ClassCountNew").prop('required', false);
                $("#ClassRepeat").prop('required', false);
            } else if (Id == '2') {
                DivClassTypeNew.style.display = "block";
                DivClassTypeNew4.style.display = "none";
                $('#ClassCountNew').val('');
                $('#ClassRepeat').val('');
                $("#ClassCountNew").prop('required', true);
                $("#ClassRepeat").prop('required', false);
            } else if (Id == '4') {
                DivClassTypeNew.style.display = "none";
                DivClassTypeNew4.style.display = "block";
                $('#ClassRepeat').val('');
                $("#ClassCountNew").prop('required', false);
                $("#ClassRepeat").prop('required', true);
            } else {
                $('#ClassCountNew').val('1');
                DivClassTypeNew.style.display = "none";
                DivClassTypeNew4.style.display = "none";
                $('#ClassCountNew').val('999');
                $('#ClassRepeat').val('');
                $("#ClassCountNew").prop('required', false);
                $("#ClassRepeat").prop('required', false);
            }
        });



        $("#ClassLimitTypes").change(function () {

            var Id = this.value;
            if (Id == '1') {
                DivClassLimitTypes.style.display = "block";
                $('#theValueGroup').val('0');
                $('#GetGroupId').html('');
            } else {
                DivClassLimitTypes.style.display = "none";
                $('#theValueGroup').val('0');
                $('#GetGroupId').html('');
            }

        });


        $("#MinClassNew").change(function () {

            var Id = this.value;
            if (Id == '1') {
                $('.DivMinClassNumNew').css("display", "block");
                $("#MinClassNumNew").prop('required', true);
                $("#ClassTimeCheckNew").prop('required', true);
                var MaxClient = $('#MaxClientNew').val();
                $('#MinClassNumNew').prop('max', MaxClient);
                $('#MinClassNumNew').prop('min', '1');
                $('.MaxClientMemberShip').prop('max', MaxClient);

            } else {
                $('.DivMinClassNumNew').css("display", "none");
                $("#MinClassNumNew").prop('required', false);
                $("#ClassTimeCheckNew").prop('required', false);
            }

        });


        $("#ClassWatingNew").change(function () {

            var Id = this.value;
            if (Id == '0') {
                $('.WatingListDiv').css("display", "block");
            } else {
                $('.WatingListDiv').css("display", "none");
                $('.WatingListNumDiv').css("display", "none");
                $("#WatingListNumNew").prop('required', false);
                $('#WatingListActNew').val('1');

            }

        });


        $("#WatingListActNew").change(function () {

            var Id = this.value;
            if (Id == '0') {
                $('.WatingListNumDiv').css("display", "block");
                $("#WatingListNumNew").prop('required', true);
                var MaxClient = $('#MaxClientNew').val();
                $('#WatingListNumNew').prop('max', MaxClient);
                $('#WatingListNumNew').prop('min', '1');
                $('.MaxClientMemberShip').prop('max', MaxClient);
            } else {
                $('.WatingListNumDiv').css("display", "none");
                $("#WatingListNumNew").prop('required', false);

            }

        });


        $("#MaxClientNew").change(function () {

            var MaxClient = this.value;
            $('.MaxClientMemberShip').prop('max', MaxClient);


        });


        $("#SendReminderNew").change(function () {

            var Id = this.value;
            if (Id == '0') {
                $('.SendReminderNew').css("display", "block");
                $("#TimeReminderNew").prop('required', true);
            } else {
                $('.SendReminderNew').css("display", "none");
                $("#TimeReminderNew").prop('required', false);
                $('#TimeReminderNew').prop('max', '');
                $('#TimeReminderNew').prop('min', '');
            }

        });



        $("#StopCancel").change(function () {

            var Id = this.value;
            if (Id == '0') {
                $('.StopCancel').css("display", "block");
                $("#StopCancelTime").prop('required', true);
            } else {
                $('.StopCancel').css("display", "none");
                $("#StopCancelTime").prop('required', false);
            }

        });


        $("#OpenOrder").change(function () {

            var Id = this.value;
            if (Id == '0') {
                $('.OpenOrder').css("display", "block");
                $("#OpenOrderTime").prop('required', true);
            } else {
                $('.OpenOrder').css("display", "none");
                $("#OpenOrderTime").prop('required', false);
            }

        });

        $("#CloseOrder").change(function () {

            var Id = this.value;
            if (Id == '0') {
                $('.CloseOrder').css("display", "block");
                $("#CloseOrderTime").prop('required', true);
            } else {
                $('.CloseOrder').css("display", "none");
                $("#CloseOrderTime").prop('required', false);
            }

        });







        $('#SetTimeNew').on('change', function () {


            /// שנה גלילה לפי שעה	


            var SetTime = $('#SetTimeNew').val();
            var FixToTime = moment(SetTime, 'HH:mm:ss').add(<?php echo @$ClassSettingsInfo->EndClassTime; ?>, 'minutes').format('HH:mm:ss');
            var FixToTimes = moment(SetTime, 'HH:mm:ss').add(5, 'minutes').format('HH:mm:ss');
            var FixToTimeCancel = moment(SetTime, 'HH:mm:ss').add(-2, 'hours').format('HH:mm:ss');

            $('#SetToTimeNew').val(FixToTime);
            $('#SetToTimeNew').prop('min', FixToTimes);
            $('#CancelTillTimeNew').prop('max', SetTime);
            $('#CancelTillTimeNew').val(FixToTimeCancel);



            var TypeReminder = $('#TypeReminderNew').val();
            var SendReminderNew = $('#SendReminderNew').val();

            if (TypeReminder == '1' && SendReminderNew == '0') {

                var TimeReminderVal = moment(SetTime, 'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>, '<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
                var TimeReminderMax = moment(SetTime, 'HH:mm:ss').add(-10, 'minutes').format('HH:mm:ss');
                var TimeReminderMin = moment(SetTime, 'HH:mm:ss').add(-10, 'hours').format('HH:mm:ss');

                $('#TimeReminderNew').prop('max', TimeReminderMax);
                $('#TimeReminderNew').prop('min', '');
                $('#TimeReminderNew').val(TimeReminderVal);

            } else if (TypeReminder == '2' || SendReminderNew == '1') {

                $('#TimeReminderNew').prop('max', '');
                $('#TimeReminderNew').prop('min', '');
                $('#TimeReminderNew').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');


            }


            var CancelLaw = $('#CancelLawNew').val();
            if (CancelLaw == '1') {

                var CancelLawVal = moment(SetTime, 'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>, '<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
                var CancelLawMax = moment(SetTime, 'HH:mm:ss').add(-10, 'minutes').format('HH:mm:ss');
                var CancelLawMin = moment(SetTime, 'HH:mm:ss').add(-10, 'hours').format('HH:mm:ss');

                $('#CancelTillTimeNew').prop('max', CancelLawMax);
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val(CancelLawVal);

            } else if (CancelLaw == '2' || CancelLaw == '3') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            } else if (CancelLaw == '4' || CancelLaw == '5') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            }

        });

        $('#SetToTimeNew').on('change', function () {


            var SetTime = $('#SetTimeNew').val();
            var SetToTime = $('#SetToTimeNew').val();


            var TypeReminder = $('#TypeReminderNew').val();
            var SendReminderNew = $('#SendReminderNew').val();
            if (TypeReminder == '1' && SendReminderNew == '0') {

                var TimeReminderVal = moment(SetTime, 'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>, '<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
                var TimeReminderMax = moment(SetTime, 'HH:mm:ss').add(-10, 'minutes').format('HH:mm:ss');
                var TimeReminderMin = moment(SetTime, 'HH:mm:ss').add(-10, 'hours').format('HH:mm:ss');

                $('#TimeReminderNew').prop('max', TimeReminderMax);
                $('#TimeReminderNew').prop('min', '');
                $('#TimeReminderNew').val(TimeReminderVal);

            } else if (TypeReminder == '2' || SendReminderNew == '1') {

                $('#TimeReminderNew').prop('max', '');
                $('#TimeReminderNew').prop('min', '');
                $('#TimeReminderNew').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');


            }


            var CancelLaw = $('#CancelLawNew').val();
            if (CancelLaw == '1') {

                var CancelLawVal = moment(SetTime, 'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>, '<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
                var CancelLawMax = moment(SetTime, 'HH:mm:ss').add(-10, 'minutes').format('HH:mm:ss');
                var CancelLawMin = moment(SetTime, 'HH:mm:ss').add(-10, 'hours').format('HH:mm:ss');

                $('#CancelTillTimeNew').prop('max', CancelLawMax);
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val(CancelLawVal);

            } else if (CancelLaw == '2' || CancelLaw == '3') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            } else if (CancelLaw == '4' || CancelLaw == '5') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            }

        });


        $("#TypeReminderNew").change(function () {

            var SetTime = $('#SetTimeNew').val();
            var SetToTime = $('#SetToTimeNew').val();


            var TypeReminder = $('#TypeReminderNew').val();
            var SendReminderNew = $('#SendReminderNew').val();
            if (TypeReminder == '1' && SendReminderNew == '0') {

                var TimeReminderVal = moment(SetTime, 'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>, '<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
                var TimeReminderMax = moment(SetTime, 'HH:mm:ss').add(-10, 'minutes').format('HH:mm:ss');
                var TimeReminderMin = moment(SetTime, 'HH:mm:ss').add(-10, 'hours').format('HH:mm:ss');

                $('#TimeReminderNew').prop('max', TimeReminderMax);
                $('#TimeReminderNew').prop('min', '');
                $('#TimeReminderNew').val(TimeReminderVal);

            } else if (TypeReminder == '2' || SendReminderNew == '1') {

                $('#TimeReminderNew').prop('max', '');
                $('#TimeReminderNew').prop('min', '');
                $('#TimeReminderNew').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');


            }


            var CancelLaw = $('#CancelLawNew').val();
            if (CancelLaw == '1') {

                var CancelLawVal = moment(SetTime, 'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>, '<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
                var CancelLawMax = moment(SetTime, 'HH:mm:ss').add(-10, 'minutes').format('HH:mm:ss');
                var CancelLawMin = moment(SetTime, 'HH:mm:ss').add(-10, 'hours').format('HH:mm:ss');

                $('#CancelTillTimeNew').prop('max', CancelLawMax);
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val(CancelLawVal);

            } else if (CancelLaw == '2' || CancelLaw == '3') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            } else if (CancelLaw == '4' || CancelLaw == '5') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            }

        });

        $("#CancelLawNew").change(function () {

            var Id = this.value;
            if (Id == '1') {
                DivCancelLawNew.style.display = "block";
                DivCancelLawNew3.style.display = "none";
                DivCancelLawNew4.style.display = "none";
                DivCancelLawNew5.style.display = "none";
                DivCancelLawNew6.style.display = "none";
                $("#CancelTillTimeNew").prop('required', true);
                $("#CancelDayNew").prop('required', false);
            } else if (Id == '2') {
                DivCancelLawNew.style.display = "block";
                DivCancelLawNew3.style.display = "none";
                DivCancelLawNew4.style.display = "none";
                DivCancelLawNew5.style.display = "none";
                DivCancelLawNew6.style.display = "none";
                $("#CancelTillTimeNew").prop('required', true);
                $("#CancelDayNew").prop('required', false);
            } else if (Id == '3') {
                DivCancelLawNew.style.display = "block";
                DivCancelLawNew3.style.display = "block";
                DivCancelLawNew4.style.display = "none";
                DivCancelLawNew5.style.display = "none";
                DivCancelLawNew6.style.display = "block";
                $("#CancelTillTimeNew").prop('required', true);
                $("#CancelDayNew").prop('required', true);
            } else if (Id == '4') {
                DivCancelLawNew.style.display = "none";
                DivCancelLawNew3.style.display = "none";
                DivCancelLawNew4.style.display = "block";
                DivCancelLawNew5.style.display = "none";
                DivCancelLawNew6.style.display = "none";
                $("#CancelTillTimeNew").prop('required', false);
                $("#CancelDayNew").prop('required', false);
                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
            } else if (Id == '5') {
                DivCancelLawNew.style.display = "none";
                DivCancelLawNew3.style.display = "none";
                DivCancelLawNew4.style.display = "none";
                DivCancelLawNew5.style.display = "block";
                $("#CancelTillTimeNew").prop('required', false);
                $("#CancelDayNew").prop('required', false);
                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
            } else {
                DivCancelLawNew.style.display = "block";
                DivCancelLawNew3.style.display = "none";
                DivCancelLawNew4.style.display = "none";
                DivCancelLawNew5.style.display = "none";
                DivCancelLawNew6.style.display = "none";
                $("#CancelTillTimeNew").prop('required', true);
                $("#CancelDayNew").prop('required', false);
            }


            var SetTime = $('#SetTimeNew').val();
            var SetToTime = $('#SetToTimeNew').val();



            var CancelLaw = $('#CancelLawNew').val();
            if (CancelLaw == '1') {

                var CancelLawVal = moment(SetTime, 'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>, '<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
                var CancelLawMax = moment(SetTime, 'HH:mm:ss').add(-10, 'minutes').format('HH:mm:ss');
                var CancelLawMin = moment(SetTime, 'HH:mm:ss').add(-10, 'hours').format('HH:mm:ss');

                $('#CancelTillTimeNew').prop('max', CancelLawMax);
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val(CancelLawVal);

            } else if (CancelLaw == '2' || CancelLaw == '3') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            } else if (CancelLaw == '4' || CancelLaw == '5') {

                $('#CancelTillTimeNew').prop('max', '');
                $('#CancelTillTimeNew').prop('min', '');
                $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');


            }
        });

        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).val()).select();
            document.execCommand("copy");
            $temp.remove();
        }


        $(document).ready(function () {
            let priceCheck = $("#singleEntryRate").prop("checked");
            let price = $("#singleEntryRate");
            if (priceCheck === true) {
                price.attr("disabled", false);
            } else if (priceCheck === false) {
                price.attr("disabled", true);
            }

            $("#meetingNumber").change(function () {
                var meeting_id = $("#meetingNumber").val();
                if (meeting_id.length > 0) {
                    $("#watchOutsideApp").val('https://app.boostapp.co.il/ClassPage.php?m_id=' + meeting_id);

                } else {
                    $("#watchOutsideApp").val('');

                }
            });

            $('#AllowSingleEntry').click(function () {
                var checked_status = this.checked;
                if (checked_status == true) {
                    $("#singleEntryRate").prop('required', true);
                } else {
                    $("#singleEntryRate").prop('required', false);
                }
            });

            $('#AllowWatchOutsideApp').click(function () {
                var checked_status = this.checked;
                if (checked_status == true) {
                    $("#watchOutsideApp").prop('required', true);
                } else {
                    $("#watchOutsideApp").prop('required', false);
                }
            });

            $('#RecordAndStoreVideo').click(function () {
                var checked_status = this.checked;
                if (checked_status == true) {
                    $("#RecordFile").prop('required', true);
                } else {
                    $("#RecordFile").prop('required', false);
                }
            });


            // Toolbar extra buttons
            var btnFinish = $('<button></button>').text('סיום')
                    .addClass('btn btn-success')
                    .on('click', function () {
                        if (!$(this).hasClass('disabled')) {
                            var elmForm = $("#AddClassNewPop");
                            if (elmForm) {
                                elmForm.validator('validate');
                                var elmErr = elmForm.find('.has-error');
                                if (elmErr && elmErr.length > 0) {
                                    alert('יש למלא את השדות חובה לפני שמירה');
                                    return false;
                                } else {
                                    //alert('מוכן לשליחה');
                                    elmForm.submit();
                                    // SubmitPayment();
                                    return false;
                                }
                            }
                        }
                    });
            var btnCancel = $('<button type="button" class="BtnClassWizs"></button>').text('בטל')
                    .addClass('btn btn-danger')
                    .on('click', function () {
                        var modal = $('#AddNewClass');
                        modal.modal('hide');
                        location.hash = "";
                        $('#ResultAddNewClass').html("");
                    });



            // Smart Wizard
            $('#smartwizard').smartWizard({
                selected: 0,
                theme: 'arrows',
                transitionEffect: 'fade',
                toolbarSettings: {toolbarPosition: 'bottom',
                    toolbarExtraButtons: [btnFinish],
                    toolbarExtraCancelButtons: [btnCancel],
                },
                anchorSettings: {
                    markDoneStep: true, // add done css
                    markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                    removeDoneStepOnNavigateBack: true, // While navigate back done step after active step will be cleared
                    enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
                }
            });

            $("#smartwizard").on("leaveStep", function (e, anchorObject, stepNumber, stepDirection) {
                var elmForm = $("#form-step-" + stepNumber);
                // stepDirection === 'forward' :- this condition allows to do the form validation
                // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
                if (stepDirection === 'forward' && elmForm) {
                    //     $('#ClassMemberType1').parent().removeClass('has-error');   
                    elmForm.validator('validate');
                    var elmErr = elmForm.find('.has-error');
                    //    var CheckClassMemberType1 = $('#CheckClassMemberType1').val();
                    var MembershipNew = $('#MembershipNew').val();
                    if (elmErr && elmErr.length > 0) {
                        // Form validation failed
                        return false;
                    }
                }

                return true;
            });

            $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection) {
                // Enable finish button only on last step
                if (stepNumber == 5) {
                    $('.btn-finish').removeClass('disabled');
                } else {
                    $('.btn-finish').addClass('disabled');
                }
            });


            $('input[name="liveClass"]').change(function () {
                debugger;
                let classType = $("#ClassTypeNew");
                let ShowClientNum = $("#ShowClientNum");
                let ShowClientName = $("#ShowClientName");
                $('#ShowClientNum option[value="0"]').prop('selected', true);
                var checked = $('input[name="liveClass"]:checked').val();
                if (checked == 'online') {
                    classType.val('3');
                    $('#ShowClientNum option[value="0"]').prop('selected', true);
                    $('#ShowClientName option[value="0"]').prop('selected', true);
                    document.getElementById("liveSection").style.display = "flex";
                    $("#liveClassLink").prop('required', true);
                    $("#meetingNumber").prop('required', false);
                    $("#ZoomPassword").prop('required', false);
                    document.getElementById("zoomSection").style.display = "none";
                } else if (checked == 'zoom') {
                    classType.val('3');
                    $('#ShowClientNum option[value="0"]').prop('selected', true);
                    $('#ShowClientName option[value="0"]').prop('selected', true);
                    document.getElementById("liveSection").style.display = "none";
                    $("#liveClassLink").prop('required', false);
                    $("#meetingNumber").prop('required', true);
                    $("#ZoomPassword").prop('required', true);
                    document.getElementById("zoomSection").style.display = "flex";
                } else if (checked == "studio") {
                    classType.val('1');
                    $('#ShowClientNum option[value="1"]').prop('selected', true);
                    $('#ShowClientName option[value="1"]').prop('selected', true);
                    document.getElementById("liveSection").style.display = "none";
                    $("#liveClassLink").prop('required', false);
                    $("#meetingNumber").prop('required', false);
                    $("#ZoomPassword").prop('required', false);
                    document.getElementById("zoomSection").style.display = "none";
                }
            });
            $("#registerLimitZoom").change(function () {
                let zoomReg = $("#registerLimitZoom").val();
                let singleReg = $(".singleReg");
                if (singleReg.length > 0) {
                    if (zoomReg == "1") {
                        singleReg.show();
                    } else if (zoomReg == "2") {
                        singleReg.hide();
                    }
                }
            })
            $('input[name="AllowSingleEntry"]').change(function () {
                let priceCheck = $(this).prop("checked");
                let price = $("#singleEntryRate");
                let classType = $("#ClassLimitTypes");
                if (priceCheck === true) {
                    price.attr("disabled", false);
                    classType.attr("disabled", true);
                    $('#ClassLimitTypes option[value="0"]').prop('selected', true);
                    if (classType.val() == '1') {
                        $("#DivClassLimitTypes").show();
                    } else {
                        $("#DivClassLimitTypes").hide();
                    }
                } else if (priceCheck === false) {
                    price.attr("disabled", true);
                    classType.attr("disabled", false);
                    $('input[name="ClassLimitTypes"]').val(classType.val());
                    if (classType.val() == '1') {
                        $("#DivClassLimitTypes").show();
                    } else {
                        $("#DivClassLimitTypes").hide();
                    }
                }
            });
            $("#meetingNumber").focusout(function () {
                let meetnum = $("#meetingNumber");
                let meetval = meetnum.val();
                meetval = meetval.replace(/-/g, "");
                meetnum.val(meetval);
            })
            // $("#liveClass").on('change', function() {
            //     debugger;
            //   if(this.checked) {
            //     document.getElementById("liveSection").style.display = "flex";
            //     $("#liveClassLink").prop('required',true);
            //   } else {
            //     document.getElementById("liveSection").style.display = "none";
            //     $("#liveClassLink").prop('required', false);
            //   }
            // });

        });


        //// שכפול קבוצה      
        function addElementgroup()
        {
            var ni = document.getElementById('GetGroupId');
            var numi = document.getElementById('theValueGroup');
            var num = (document.getElementById('theValueGroup').value - 0) + 1;
            numi.value = num;
            var newdiv = document.createElement('div');
            var divIdName = 'Group' + num + 'Div';
            newdiv.setAttribute('id', divIdName);
            newdiv.innerHTML = ' <div id="GroupId"><div class="row"><div class="col-6"><div class="form-group"  ><label>בחר סוג מנוי</label> <a id="ClickSelectAll" class="ClickSelectAll" data-num="' + num + '" href="javascript:void(0)" style="float:left;display: none;">סמן הכל</a> <select class="form-control js-example-basic-single select2multipleDesk newid' + num + ' text-right" name="ClassMemberType' + num + '[]" id="ClassMemberType' + num + '" multiple="multiple"   data-select2order="true" style="width: 100%;"></select><input type="hidden" id="CheckClassMemberType' + num + '" value=""><div class="help-block with-errors"></div></div></div><div class="col-3"><div class="form-group" ><label>מקסימום משתתפים</label><input type="number" min="1" name="MaxClientMemberShip' + num + '" id="MaxClientMemberShip' + num + '" class="form-control MaxClientMemberShip" value="1"></div></div><div class="col-md-3" style="padding-top: 35px;" ><a href="javascript:;" class="btn btn-danger btn-sm" onclick=\'removeElementgroup(\"' + divIdName + '\",\"' + num + '\")\' title="הסר">הסר מגבלה <i class="fas fa-trash-alt"></i></a></div></div><hr class="hrclass"></div>  ';
            ni.appendChild(newdiv);
            $(".select2multipleDesk").select2({theme: "bootstrap", placeholder: "בחר סוג מנוי", ajax: {
                    url: 'action/SelectMembership.php?GroupNumber=<?php echo @$GroupNumber; ?>',
                    dataType: 'json'
                }});
            removeselectionclass(num);

            var MaxClient = $('#MaxClientNew').val();
            $('#MaxClientMemberShip' + num).prop('max', MaxClient);
            $('#MaxClientMemberShip' + num).val(MaxClient);

        }

        function removeElementgroup(divNum, num)
        {
            var d = document.getElementById('GetGroupId');
            var olddiv = document.getElementById(divNum);
            var numis = document.getElementById('theValueGroup');
            var nums = (document.getElementById('theValueGroup').value);
            numis.value = nums;
            d.removeChild(olddiv);

            $.ajax({
                url: 'action/TempMemberDel.php?GroupNumber=<?php echo @$GroupNumber; ?>&GroupNum=' + num,
                type: 'POST',
                success: function (data) {}
            });


        }


        function removeselectionclass(num)
        {


            $("#ClassMemberType" + num).on("select2:selecting select2:unselecting", function (e) {

                //this returns all the selected item
                var items = $(this).val();
                var Oldarray = $('#ChangeMe').val();
                var array = $('#ChangeMe').val(items);
                $('#CheckClassMemberType' + num).val(items);
                //// עדכון טבלה זמנית

                $.ajax({
                    url: 'action/TempMember.php?GroupNumber=<?php echo @$GroupNumber; ?>&Clases=' + items + '&GroupNum=' + num,
                    type: 'POST',
                    success: function (data) {}
                });

                //Gets the last selected item
                var lastSelectedItem = e.params.args.data.id;

            });


        }

        $(".select2multipleDesk").select2({theme: "bootstrap", placeholder: "בחר סוג מנוי", ajax: {
                url: 'action/SelectMembership.php?GroupNumber=<?php echo @$GroupNumber; ?>',
                dataType: 'json'
            }});


        $("#ClassMemberType1").on("select2:selecting select2:unselecting", function (e) {

            //this returns all the selected item
            var items = $(this).val();
            var Oldarray = $('#ChangeMe').val();
            var array = $('#ChangeMe').val(items);
            $('#CheckClassMemberType1').val(items);


            //// עדכון טבלה זמנית

            $.ajax({
                url: 'action/TempMember.php?GroupNumber=<?php echo @$GroupNumber; ?>&Clases=' + items + '&GroupNum=1',
                type: 'POST',
                success: function (data) {}
            });

            //Gets the last selected item
            var lastSelectedItem = e.params.args.data.id;

        });


    </script>
