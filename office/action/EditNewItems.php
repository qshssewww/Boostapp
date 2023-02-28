<?php

require_once '../../app/initcron.php';



$CompanyNum = Auth::user()->CompanyNum;

$Id = $_REQUEST['Id'];



$GetItemInfo = DB::table('items')->where('id', '=', $Id)->where('CompanyNum', '=', $CompanyNum)->first();





?>

<link href="../../assets/css/smart_wizard.css" rel="stylesheet" type="text/css" />

<link href="../../assets/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />



<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>

<script type="text/javascript" src="../../assets/js/jquery.smartWizard.js"></script>



<?php

$GroupNumber = rand(1,9999999);

$GroupNumber;



?>



<div id="smartwizard" dir="rtl">

            <ul>

                <li><a href="#step-1">שלב 1<br /><small>הגדרות כלליות</small></a></li>

                <li><a href="#step-2">שלב 2<br /><small>הגדרת תוקף/הקפאה</small></a></li>

                <li><a href="#step-3">שלב 3<br /><small>הגדרת מגבלות פריט</small></a></li>

            </ul>



            <div>

                <div id="step-1" style="padding-top: 10px;">

                    <h4><strong>הגדרות כלליות</strong></h4>

                    <div id="form-step-0" role="form" data-toggle="validator">

                    <input type="hidden" name="Membership" value="<?php echo $GetItemInfo->Department; ?>">

                    <div class="form-group">

                    <label>מחלקה</label>

                   <select name="MembershipOld" id="MembershipNew" class="form-control selectAddItem" style="width:100%;"  data-placeholder="בחר מחלקה" required disabled >

                   <option value=""></option>

                   <?php

                   $Activities = DB::table('membership')->where('Status', '=', '0')->orderBy('id', 'ASC')->get();

                   foreach ($Activities as $Activitie) {

                   ?>

                   <option value="<?php echo $Activitie->id ?>" <?php if ($GetItemInfo->Department==$Activitie->id) { echo 'selected'; } else {} ?>  ><?php echo $Activitie->MemberShip; ?></option>

                   <?php } ?>



                   </select>

                    <div class="help-block with-errors"></div>

                    </div>





                   <div class="row">

               		<div class="col-md-4">

                <div class="form-group">

                 <label>סוג מנוי</label>

               <select name="membership_type" class="form-control selectAddItem" style="width:100%;"  data-placeholder="בחר מנוי"  required >

               <option value=""></option>

               <?php if ($GetItemInfo->Department=='4'){ ?>

               <option value="BA999" <?php if ($GetItemInfo->MemberShip=='BA999') { echo 'selected'; } else {} ?>>ללא מנוי</option>

                <?php } ?>

              <?php

	          $Activities = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Type', 'ASC')->get();

              foreach ($Activities as $Activitie) {

	          ?>

              <option value="<?php echo $Activitie->id ?>" <?php if ($GetItemInfo->MemberShip==$Activitie->id) { echo 'selected'; } else {} ?>  ><?php echo $Activitie->Type; ?></option>

              <?php } ?>



              </select>

             <div class="help-block with-errors"></div>

                </div>

               		</div>

               		<div class="col-md-4">

                <div class="form-group">

                 <label>שם פריט</label>

                <input type="text" name="ItemName" value="<?php echo $GetItemInfo->ItemName; ?>" class="form-control" placeholder="לדוגמא: כרטיסית 12 כניסות" required>

                <div class="help-block with-errors"></div>

                </div>

               		</div>



                <div class="col-md-2">

                <div class="form-group">

                <label>מחיר</label>

                <input type="text" name="ItemPrice" value="<?php echo $GetItemInfo->ItemPrice; ?>" class="form-control" onkeypress='validate(event)' data-error="שדה חובה" required>

                <div class="help-block with-errors"></div>

                </div>



                </div>



                <div class="col-md-2">

                <div class="form-group">

                <label>כולל מע"מ?</label>

               <select name="Vat" class="form-control" style="width:100%;"  data-placeholder="בחר" required>

               <option value="0" <?php if ($GetItemInfo->Vat=='0') { echo 'selected'; } else {} ?> >כן</option>

               <option value="1" <?php if ($GetItemInfo->Vat=='1') { echo 'selected'; } else {} ?> >לא</option>

               </select>

               <div class="help-block with-errors"></div>

                </div>

               </div>



               	</div>





                <div id="TypeNew2" style="display: none;">



                <div class="row">



                <div class="col-md-6">

                <div class="form-group" dir="rtl">

                <label>כמות שיעורים</label>

                <input type="text" name="BalanceClass" value="<?php echo $GetItemInfo->BalanceClass; ?>" id="BalanceClass" class="form-control" onkeypress='validate(event)'>

                 <div class="help-block with-errors"></div>

                </div>



               </div>



                <div class="col-md-6">

               <div class="form-group" dir="rtl">

               <label>האם לקזז מינוס מפעילות קודמת?</label>

               <select name="MinusCards" class="form-control" style="width:100%;"  data-placeholder="בחר">

               <option value="0" <?php if ($GetItemInfo->MinusCards=='0') { echo 'selected'; } else {} ?> >כן</option>

               <option value="1" <?php if ($GetItemInfo->MinusCards=='1') { echo 'selected'; } else {} ?> >לא</option>

               </select>

               </div>

               </div>





               </div><hr>





               </div>



                <div id="TypeNew3" style="display: none;">



                <div class="form-group" dir="rtl">

                <label>כמות שיעורים</label>

                <input type="number" max="5" name="BalanceClassTry" value="<?php if ($GetItemInfo->Department=='3') { echo $GetItemInfo->BalanceClass; } else {} ?>" id="BalanceClassTry" class="form-control" onkeypress='validate(event)'>

                <div class="help-block with-errors"></div>

                </div>



               </div>





                <div id="TypeNew4" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>מחיר עלות</label>

                <input type="text" name="CostPrice" value="<?php echo $GetItemInfo->CostPrice; ?>" id="CostPrice" value="0" class="form-control" onkeypress='validate(event)'>

                <div class="help-block with-errors"></div>

                </div>

                </div>



  <?php

  $BrandsSettings = DB::table('boostapp.brands')->where('FinalCompanynum', '=', $CompanyNum)->where('ShowBrand', '=', '1')->orderBy('id', 'ASC')->first();

  if (@$BrandsSettings->id!=''){

  ?>

  <div class="form-group">

  <label>הגבלת רכישה לפי סניף?</label>

  <select class="form-control js-example-basic-single select2BarndSelects text-right" data-placeholder="בחר סניפים"  name="BarndSelect[]" id="BarndSelects" dir="rtl"  multiple="multiple" data-select2order="true" style="width: 100%;">

  <option value="0" <?php if ($GetItemInfo->Brands=='BA999' || $GetItemInfo->Brands=='') { echo 'selected'; } else {} ?> >כל הסניפים</option>

  <?php

  $myArray = explode(',', $GetItemInfo->Brands);

  $ClinetLevels = DB::table('brands')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->get();

  foreach ($ClinetLevels as $ClinetLevel) {

  $selected = (in_array($ClinetLevel->id, $myArray)) ? ' selected="selected"' : '';

  ?>

  <option value="<?php echo $ClinetLevel->id; ?>" <?php echo @$selected; ?> ><?php echo $ClinetLevel->BrandName; ?></option>

  <?php

  }

  ?>

  </select>

  </div>

  <?php } else { ?>

  <input type="hidden" name="BarndSelect" value="0">

  <?php } ?>







                 <div class="form-group" dir="rtl">

                <label>סטטוס</label>

                <select class="form-control" name="Status">

                <option value="0" <?php if ($GetItemInfo->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>

                <option value="1" <?php if ($GetItemInfo->Status=='1') { echo 'selected'; } else {} ?>>לא פעיל</option>

                </select>

                </div>



                    </div>



                </div>

                <div id="step-2" style="padding-top: 10px;">

                    <h4><strong>הגדרת תוקף/הקפאה</strong></h4>

                    <div id="form-step-1" role="form" data-toggle="validator">





               <div class="row">

               	<div class="col-md-3">

                <div class="form-group" dir="rtl">

                <label>תוקף</label>

                <input type="number" max="36" name="Vaild" id="Vaild" value="<?php echo $GetItemInfo->Vaild; ?>" class="form-control" onkeypress='validate(event)'>

                <div class="help-block with-errors"></div>

                </div>

                </div>

                <div class="col-md-3">

                <div class="form-group" dir="rtl">

                <label>חשב לפי</label>

               <select name="Vaild_Type"  class="form-control" style="width:100%;"  data-placeholder="בחר"  >

               <option value="1" <?php if ($GetItemInfo->Vaild_Type=='1') { echo 'selected'; } else {} ?> >ימים</option>

               <option value="2" <?php if ($GetItemInfo->Vaild_Type=='2') { echo 'selected'; } else {} ?> >שבועות</option>

               <option value="3" <?php if ($GetItemInfo->Vaild_Type=='3') { echo 'selected'; } else {} ?> >חודשים</option>

               </select>

                </div>



               </div>



                <div class="col-md-4">

                <div class="form-group" dir="rtl">

                <label>התראה לסיום מנוי בימים</label>

                <input type="number" min="0" value="<?php echo $GetItemInfo->NotificationDays; ?>" name="NotificationDays" class="form-control" placeholder="הקלד בימים" value="3" onkeypress='validate(event)'>

                </div>



                </div>



               </div>



              <hr>



            <div class="row">



                <div class="col-md-3">

                <div class="form-group" dir="rtl">

                <label>מנוי ניתן להקפאה?</label>

               <select name="FreezMemberShip" id="FreezMemberShipNew" class="form-control" style="width:100%;"  data-placeholder="בחר">

               <option value="0" <?php if ($GetItemInfo->FreezMemberShip=='0') { echo 'selected'; } else {} ?> >כן</option>

               <option value="1" <?php if ($GetItemInfo->FreezMemberShip=='1') { echo 'selected'; } else {} ?> >לא</option>

               </select>

               </div>

                </div>





                <div class="col-md-3" id="DivFreezMemberShipNew0" style="display: none;">

                 <div class="form-group" dir="rtl">

                <label>מינימום ימים להקפאה</label>

                <input type="text" name="FreezMemberShipDaysMin" value="<?php echo $GetItemInfo->FreezMemberShipDaysMin; ?>" class="form-control" onkeypress='validate(event)'>

                </div>

                </div>



                <div class="col-md-3" id="DivFreezMemberShipNew1" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>מקסימום ימים להקפאה</label>

                <input type="text" name="FreezMemberShipDays" value="<?php echo $GetItemInfo->FreezMemberShipDays; ?>" class="form-control" onkeypress='validate(event)'>

                </div>



               </div>



                <div class="col-md-3" id="DivFreezMemberShipNew2" style="display: none;">

               <div class="form-group" dir="rtl">

               <label>מספר פעמים להקפאה</label>

               <input type="text" name="FreezMemberShipCount" value="<?php echo $GetItemInfo->FreezMemberShipCount; ?>" class="form-control" onkeypress='validate(event)'>

               </div>

               </div>









               </div>









                    </div>

                </div>





                <div id="step-3" style="padding-top: 10px;">

                <h4><strong>הגדרת מגבלות פריט</strong></h4>

                <div id="form-step-2" role="form" data-toggle="validator">



                <input type="hidden" id="ChangeMe" value="">



               <div id="GetGroupId">







                <?php



                $GetItemGroupInfox = DB::table('items_roles')->where('ItemId', '=', $GetItemInfo->id)->where('CompanyNum', '=', $CompanyNum)->where('Group', '=', 'Class')->get();

                $countsGroups = count($GetItemGroupInfox);

                $Fixi = '1';

                foreach ($GetItemGroupInfox as $GetItemGroupInfo) {



                ?>

                <div id="Group<?php echo $Fixi ?>Div">

                <div id="GroupId<?php echo $Fixi ?>">



                <div class="form-group" dir="rtl">

                <label>בחר שיעור</label>

                <select class="form-control js-example-basic-single select2multipleDesk newid1 text-right" name="ClassMemberType<?php echo $Fixi ?>[]" id="ClassMemberType<?php echo $Fixi ?>" dir="rtl"  multiple="multiple" data-select2order="true" style="width: 100%;">

                <?php

                $myArray = explode(',', $GetItemGroupInfo->Class);

                $SectionInfos = DB::table('class_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->get();

                foreach ($SectionInfos as $SectionInfo) {

                $selected = (in_array($SectionInfo->id, $myArray)) ? ' selected="selected"' : '';



                if ($selected!='') {

                DB::table('templistclass')->insertGetId(

                array('CompanyNum' => $CompanyNum, 'GroupNum' => $Fixi, 'GroupNumber' => $GroupNumber, 'ClassId' => $SectionInfo->id) );

                }





                ?>

                <option value="<?php echo $SectionInfo->id; ?>" <?php echo @$selected; ?> ><?php echo $SectionInfo->Type; ?></option>

                <?php

                 }

                ?>

                </select>

                <input type="hidden" id="CheckClassMemberType<?php echo $Fixi ?>" value="555">

                <div class="help-block with-errors"></div>

                </div>





                 <div id="GetGroupItemId<?php echo $Fixi ?>">

                 <?php



                 $MaxTime = '1';

                 $Item = '';



                 $GetItemGroupItemInfox = DB::table('items_roles')->where('ItemId', '=', $GetItemInfo->id)->where('CompanyNum', '=', $CompanyNum)->where('Group', '=', 'Max')->where('GroupId', '=', $GetItemGroupInfo->GroupId)

                 ->Orwhere('ItemId', '=', $GetItemInfo->id)->where('CompanyNum', '=', $CompanyNum)->where('Group', '=', 'Time')->where('GroupId', '=', $GetItemGroupInfo->GroupId)

                 ->Orwhere('ItemId', '=', $GetItemInfo->id)->where('CompanyNum', '=', $CompanyNum)->where('Group', '=', 'Day')->where('GroupId', '=', $GetItemGroupInfo->GroupId)

                 ->get();

                 $countsGroupItems = count($GetItemGroupItemInfox);



                 if ($countsGroupItems==''){

                 $countsGroupItems = '0';

                 }



                 $Itemfix = '1';

                 foreach ($GetItemGroupItemInfox as $GetItemGroupItemInfo) {

                 $FromTime = '';

                 $ToTime = '';



                 if ($GetItemGroupItemInfo->Group=='Max'){

                 $SelectType = '1';

                 $MaxTime = $GetItemGroupItemInfo->Value;



                 if ($GetItemGroupItemInfo->Item=='Day'){

                 $Item = '4';

                 }

                 else if ($GetItemGroupItemInfo->Item=='Week') {

                 $Item = '5';

                 }

                 else if ($GetItemGroupItemInfo->Item=='Month') {

                 $Item = '6';

                 }

                 else if ($GetItemGroupItemInfo->Item=='Year') {

                 $Item = '7';

                 }

                 else if ($GetItemGroupItemInfo->Item=='Morning') {

                 $Item = '8';

                 }

                 else if ($GetItemGroupItemInfo->Item=='Evening') {

                 $Item = '9';

                 }





                 }

                 else if ($GetItemGroupItemInfo->Group=='Day') {

                 $SelectType = '2';



                 }

                 else if ($GetItemGroupItemInfo->Group=='Time') {

                 $SelectType = '3';





                if (@$GetItemGroupItemInfo->Value!=''){

                $Loops =  json_decode($GetItemGroupItemInfo->Value,true);

                foreach($Loops['data'] as $key=>$val){



                $FromTime = $val['FromTime'];

                $ToTime = $val['ToTime'];

                }

                }

                else {

                $FromTime = '';

                $ToTime = '';

                }







                 }

                 else {

                 $SelectType = '0';

                 }



                 DB::table('templistclass_option')->insertGetId(

                 array('CompanyNum' => $CompanyNum, 'GroupNum' => $Fixi, 'GroupNumber' => $GroupNumber, 'ClassId' => $Item, 'Type' => '1', 'Num' => $Itemfix) );





                 ?>



                <div id="GroupItem<?php echo $Itemfix ?>Div<?php echo $Fixi ?>">



                 <div class="row" id="GetGroupItemId<?php echo $Fixi ?>-<?php echo $Itemfix ?>">

                 <div class="col-md-3" >

                 <div class="form-group" dir="rtl">

                 <label>סוג הגבלה</label>

                 <select class="form-control SelectType" name="SelectType<?php echo $Fixi ?><?php echo $Itemfix ?>" id="SelectType"  data-num="<?php echo $Itemfix ?>" data-id="<?php echo $Fixi ?>" style="width:100%;"  >

                 <option value="0" data-num="<?php echo $Fixi ?>-<?php echo $Itemfix ?>" <?php if ($SelectType=='0') { echo 'selected'; } else {} ?>>ללא</option>

                 <?php $SectionInfos = DB::table('templistclass_data')->where('Type','=','0')->get();

                 foreach ($SectionInfos as $SectionInfo) { ?>

                 <option value="<?php echo $SectionInfo->id; ?>" data-num="<?php echo $Fixi ?>-<?php echo $Itemfix ?>" <?php if ($SelectType==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->Text; ?></option>

                 <?php } ?>

                 </select>

                 </div>

                 </div>

                 <div id="Div0<?php echo $Fixi ?>-<?php echo $Itemfix ?>" style="display:block;">

                 </div>

                 <div class="col-md-6" id="Div1<?php echo $Fixi ?>-<?php echo $Itemfix ?>" style="display:none;">

                 <div class="row">

                 <div class="col-md-6" >

                 <div class="form-group" dir="rtl">

                 <label>מקסימום פעמים</label>

                 <input type="number" min="0" value="<?php echo $MaxTime ?>" name="MaxTime<?php echo $Fixi ?><?php echo $Itemfix ?>" class="form-control">

                 <div class="help-block with-errors"></div>

                 </div>

                 </div>

                 <div class="col-md-6" >

                 <div class="form-group" dir="rtl">

                 <label>אפשרות</label>

                 <select data-num="<?php echo $Itemfix ?>" data-id="<?php echo $Fixi ?>" class="form-control SelectType2" name="SelectType2<?php echo $Fixi ?><?php echo $Itemfix ?>" style="width:100%;">

                 <option value="" data-num="<?php echo $Fixi ?>-<?php echo $Itemfix ?>">בחר</option>

                 <?php $SectionInfos = DB::table('templistclass_data')->where('Type','=','1')->get();

                 foreach ($SectionInfos as $SectionInfo) { ?>

                 <option value="<?php echo $SectionInfo->id; ?>" data-num="<?php echo $Fixi ?>-<?php echo $Itemfix ?>" <?php if ($Item==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->Text; ?></option>

                 <?php } ?>

                 </select>

                 <div class="help-block with-errors"></div>

                 </div>

                 </div>

                 </div>

                 </div>

                 <div class="col-md-6" id="Div2<?php echo $Fixi ?>-<?php echo $Itemfix ?>" style="display:none;">

                 <div class="row">

                 <div class="col-md-12" >

                 <div class="form-group" dir="rtl">

                 <label>בחר ימים</label>

                 <?php

                 if ($GetItemGroupItemInfo->Group=='Day') {

                 $myArrayDays = explode(',', @$GetItemGroupItemInfo->Value);

                 }

                 else {

                 $myArrayDays = explode(',', '999');

                 }



                  ?>

                 <select class="form-control selectdays2"  name="Days<?php echo $Fixi ?><?php echo $Itemfix ?>[]" id="Days<?php echo $Fixi ?><?php echo $Itemfix ?>"  dir="rtl" style="width:100%;" multiple="multiple">

                 <option vaule=""></option>

                 <option vaule="0" <?php echo $selected = (in_array('ראשון', $myArrayDays)) ? ' selected="selected"' : '';  ?> >ראשון</option>

                 <option vaule="1" <?php echo $selected = (in_array('שני', $myArrayDays)) ? ' selected="selected"' : '';  ?>>שני</option>

                 <option vaule="2" <?php echo $selected = (in_array('שלישי', $myArrayDays)) ? ' selected="selected"' : '';  ?>>שלישי</option>

                 <option vaule="3" <?php echo $selected = (in_array('רביעי', $myArrayDays)) ? ' selected="selected"' : '';  ?>>רביעי</option>

                 <option vaule="4" <?php echo $selected = (in_array('חמישי', $myArrayDays)) ? ' selected="selected"' : '';  ?>>חמישי</option>

                 <option vaule="5" <?php echo $selected = (in_array('שישי', $myArrayDays)) ? ' selected="selected"' : '';  ?>>שישי</option>

                 <option vaule="6" <?php echo $selected = (in_array('שבת', $myArrayDays)) ? ' selected="selected"' : '';  ?>>שבת</option>

                 </select>

                 </div>

                 </div>

                 </div>

                 </div>

                 <div class="col-md-6" id="Div3<?php echo $Fixi ?>-<?php echo $Itemfix ?>" style="display:none;">

                 <div class="row">

                 <div class="col-md-6" >

                 <div class="form-group" dir="rtl">

                 <label>בין שעה</label>

                 <input type="time" value="<?php echo $FromTime; ?>" name="FromTime<?php echo $Fixi ?><?php echo $Itemfix ?>" class="form-control">

                 </div>

                 </div>

                 <div class="col-md-6" >

                 <div class="form-group" dir="rtl">

                 <label>לשעה</label>

                 <input type="time" value="<?php echo $ToTime; ?>" name="ToTime<?php echo $Fixi ?><?php echo $Itemfix ?>" class="form-control">

                 </div>

                 </div>

                 </div>

                 </div>

                 <div class="col-md-3" style="padding-top: 40px;" >

                 <a href="javascript:;" onclick='removeElement("GroupItem<?php echo $Itemfix ?>Div<?php echo $Fixi ?>","<?php echo $Fixi ?>","<?php echo $Itemfix ?>")' title="הסר"><i class="fas fa-trash-alt"></i></a>

                 </div>

                 </div>

                </div>

                 <?php ++ $Itemfix; } ?>





                </div>



                <a class="btn btn-secondary btn-sm" href="javascript:void(0);" onclick='addElement("<?php echo $Fixi ?>")' >הוסף מגבלה חדשה +</a>





                <hr>











                <div class="alertb alert-info">הגדרת רישום נוסף ע"ב מקום פנוי</div>



                <?php

                $GetStandBy = DB::table('items_roles')->where('ItemId', '=', $GetItemInfo->id)->where('CompanyNum', '=', $CompanyNum)->where('Group', '=', 'Item')->where('Item', '=', 'StandBy')->where('GroupId', '=', $GetItemGroupInfo->GroupId)->first();



                if (@$GetStandBy->Value!=''){

                $Loops =  json_decode($GetStandBy->Value,true);

                foreach($Loops['data'] as $key=>$val){



                $StandByCount = $val['StandByCount'];

                $StandByVaild_Type = $val['StandByVaild_Type'];

                $StandByTime = $val['StandByTime'];

                $StandByTimeVaild_Type = $val['StandByTimeVaild_Type'];

                $StandByOption = !empty($val['StandByOption'])?$val['StandByOption']:'';

                }

                }

                else {

                $StandByCount = '1';

                $StandByVaild_Type = '3';

                $StandByTime = '1';

                $StandByTimeVaild_Type = '2';

                $StandByOption = '0';

                }



                ?>



                <div class="row">

                <div class="col-3">

                <div class="form-group" dir="rtl">

                <label>בחר אפשרות</label>

               <select name="StandByOption<?php echo $Fixi ?>" class="form-control StandByOption" style="width:100%;"  data-placeholder="בחר"  >

               <option value="0" data-num="<?php echo $Fixi ?>" <?php if (@$StandByOption=='0') { echo 'selected'; } else {} ?>>ללא</option>

               <option value="1" data-num="<?php echo $Fixi ?>" <?php if (@$StandByOption=='1') { echo 'selected'; } else {} ?>>ע"ב מקום פנוי</option>

               </select>

                </div>

                </div>





                <div class="col-2 DivStandBy<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>כמות שיעורים</label>

                <input type="text" name="StandByCount<?php echo $Fixi ?>"  class="form-control" value="<?php echo @$StandByCount ?>">

                </div>

                </div>

                <div class="col-2 DivStandBy<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>בתקופה של</label>

                <select name="StandByVaild_Type<?php echo $Fixi ?>" class="form-control" style="width:100%;"  data-placeholder="בחר"  >

                <option value="1" <?php if (@$StandByVaild_Type=='1') { echo 'selected'; } else {} ?>>יום</option>

                <option value="2" <?php if (@$StandByVaild_Type=='2') { echo 'selected'; } else {} ?>>שבוע</option>

                <option value="3" <?php if (@$StandByVaild_Type=='3') { echo 'selected'; } else {} ?>>חודש</option>

                <option value="4" <?php if (@$StandByVaild_Type=='4') { echo 'selected'; } else {} ?>>שנה</option>

                </select>

                </div>

                </div>



                <div class="col-3 DivStandBy<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>זמן לפני השיעור</label>

                <input type="text" name="StandByTime<?php echo $Fixi ?>" class="form-control" value="<?php echo @$StandByTime ?>">

                </div>

                </div>

                <div class="col-2 DivStandBy<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>אפשרות</label>

                <select name="StandByTimeVaild_Type<?php echo $Fixi ?>" class="form-control" style="width:100%;"  data-placeholder="בחר"  >

                <option value="1" <?php if (@$StandByTimeVaild_Type=='1') { echo 'selected'; } else {} ?>>דקות</option>

                <option value="2" <?php if (@$StandByTimeVaild_Type=='2') { echo 'selected'; } else {} ?> >שעות</option>

                </select>

                </div>

                </div>







                </div>







                <hr>

                <div class="alertb alert-info" style="display: none;">הגדרת פריט UpSale לרכישה אונליין לאחר חסימה ע"ב המגבלות</div>



                <?php

                $GetUpSale = DB::table('items_roles')->where('ItemId', '=', $GetItemInfo->id)->where('CompanyNum', '=', $CompanyNum)->where('Group', '=', 'Item')->where('Item', '=', 'UpSale')->where('GroupId', '=', $GetItemGroupInfo->GroupId)->first();



                if (@$GetUpSale->Value!=''){

                $Loops =  json_decode($GetUpSale->Value,true);



                foreach($Loops['data'] as $key=>$val){



                $UpSaleTitle = $val['UpSaleTitle'];

                $UpSaleClass = $val['UpSaleClass'];

                $UpSalePrice = $val['UpSalePrice'];

                $UpSaleVaild = $val['UpSaleVaild'];

                $UpSaleVaild_Type = $val['UpSaleVaild_Type'];

                $UpSaleOption = !empty($val['UpSaleOption']) ? $val['UpSaleOption'] : '';

                }

                }

                else {

                $UpSaleTitle = '';

                $UpSaleClass = '';

                $UpSalePrice = '';

                $UpSaleVaild = '';

                $UpSaleVaild_Type = '3';

                $UpSaleOption = '0';

                }



                ?>





                <div class="row" style="display: none;">



                <div class="col-2">

                <div class="form-group" dir="rtl">

                <label>בחר אפשרות</label>

                <select name="UpSaleOption<?php echo $Fixi ?>" class="form-control UpSaleOption" style="width:100%;"  data-placeholder="בחר"  >

                <option value="0" data-num="<?php echo $Fixi ?>" <?php if (@$UpSaleOption=='0') { echo 'selected'; } else {} ?>>לא</option>

                <option value="1" data-num="<?php echo $Fixi ?>" <?php if (@$UpSaleOption=='1') { echo 'selected'; } else {} ?>>כן</option>

                </select>

                </div>

                </div>





                <div class="col-2 DivUpSale<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>כותרת לפריט</label>

                <input type="text" name="UpSaleTitle<?php echo $Fixi ?>" value="<?php echo @$UpSaleTitle ?>" class="form-control">

                </div>

                </div>

                <div class="col-2 DivUpSale<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>כמות שיעורים</label>

                <input type="text" name="UpSaleClass<?php echo $Fixi ?>" value="<?php echo @$UpSaleClass ?>" class="form-control">

                </div>

                </div>

                <div class="col-2 DivUpSale<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>מחיר לפריט</label>

                <input type="text" name="UpSalePrice<?php echo $Fixi ?>" value="<?php echo @$UpSalePrice ?>" class="form-control">

                </div>

                </div>

                <div class="col-2 DivUpSale<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>תוקף</label>

                <input type="text" name="UpSaleVaild<?php echo $Fixi ?>" value="<?php echo @$UpSaleVaild ?>" class="form-control">

                </div>

                </div>

                <div class="col-2 DivUpSale<?php echo $Fixi ?>" style="display: none;">

                <div class="form-group" dir="rtl">

                <label>חשב לפי</label>

                <select name="UpSaleVaild_Type<?php echo $Fixi ?>"  class="form-control" style="width:100%;"  data-placeholder="בחר"  >

               <option value="1" <?php if (@$UpSaleVaild_Type=='1') { echo 'selected'; } else {} ?>>ימים</option>

               <option value="2" <?php if (@$UpSaleVaild_Type=='2') { echo 'selected'; } else {} ?>>שבועות</option>

               <option value="3" <?php if (@$UpSaleVaild_Type=='3') { echo 'selected'; } else {} ?> >חודשים</option>

               </select>

                </div>

                </div>

                </div>



               <input type="hidden" name="UpSaleOption<?php echo $Fixi ?>" value="0">



                <input type="hidden" value="<?php echo $countsGroupItems; ?>" id="theValue<?php echo $Fixi ?>" name="tItems<?php echo $Fixi ?>"/><hr>



                </div>

                </div>

                <?php



                 ++ $Fixi; } ?>





              </div>





               <a class="btn btn-dark btn-sm" href="javascript:void(0);" onclick="addElementgroup();">הוסף קבוצת שיעורים חדשה +</a>

               <input type="hidden" value="<?php echo $countsGroups ?>" id="theValueGroup" name="tGroups"/>



                <hr>



                <?php if (Auth::user()->role_id == '1') {?>    
               <div class="form-group" dir="rtl">
               <label>הגדר מגבלות חודשיות לפי חודש קלנדרי?</label>
               <select name="LimitType"  class="form-control" style="width:100%;"  data-placeholder="בחר"  >    
               <option value="0" <?php if (@$GetItemInfo->LimitType=='0') { echo 'selected'; } else {} ?> >כן, קלנדרי</option>
               <option value="1" <?php if (@$GetItemInfo->LimitType=='1') { echo 'selected'; } else {} ?> >לא, לפי תוקף המנוי</option>    
               </select> 
               </div>      
                <?php } ?>  





                </div>

                </div>











<input type="hidden" name="ItemId" value="<?php echo $GetItemInfo->id; ?>">

<input type="hidden" name="GroupNumber" value="<?php echo $GroupNumber; ?>">







        </div>





<style>



.select2-results__option[aria-selected=true] {

    display: none;

}



</style>

  <script>



$( ".selectAddItem" ).select2( {theme:"bootstrap", placeholder: "בחר", 'language':"he", dir: "rtl" } );

$( ".select2BarndSelects" ).select2( {theme:"bootstrap", placeholder: "בחר", 'language':"he", dir: "rtl" } );



$('#BarndSelects').on('select2:select', function (e) {

var selected = $(this).val();



  if(selected != null)

  {

    if(selected.indexOf('0')>=0){

      $(this).val('0').select2( {theme:"bootstrap", placeholder: "בחר סניף", 'language':"he", dir: "rtl" } );

    }

  }



});



  $('#BarndSelects').on('select2:open', function () {

    // get values of selected option

    var values = $(this).val();

    // get the pop up selection

    var pop_up_selection = $('.select2-results__options');

    if (values != null ) {

      // hide the selected values

       pop_up_selection.find("li[aria-selected=true]").hide();



    } else {

      // show all the selection values

      pop_up_selection.find("li[aria-selected=true]").show();

    }



  });







  $("#MembershipNew").change(function() {



  var Id = this.value;

  if (Id=='1'){

  TypeNew2.style.display = "none";

  TypeNew3.style.display = "none";

  TypeNew4.style.display = "none";



  $("#BalanceClass").prop('required',false);

  $("#BalanceClassTry").prop('required',false);

  $("#CostPrice").prop('required',false);

  $("#Vaild").prop('required',true);







  }

  else if (Id=='2') {

  TypeNew2.style.display = "block";

  TypeNew3.style.display = "none";

  TypeNew4.style.display = "none";



  $("#BalanceClass").prop('required',true);

  $("#BalanceClassTry").prop('required',false);

  $("#CostPrice").prop('required',false);

  $("#Vaild").prop('required',false);





  }

  else if (Id=='3') {

  TypeNew2.style.display = "none";

  TypeNew3.style.display = "block";

  TypeNew4.style.display = "none";



  $("#BalanceClass").prop('required',false);

  $("#BalanceClassTry").prop('required',true);

  $("#CostPrice").prop('required',false);

  $("#Vaild").prop('required',false);





  }

  else if (Id=='4') {

  TypeNew2.style.display = "none";

  TypeNew3.style.display = "none";

  TypeNew4.style.display = "block";



  $("#BalanceClass").prop('required',false);

  $("#BalanceClassTry").prop('required',false);

  $("#CostPrice").prop('required',true);

  $("#Vaild").prop('required',false);





  }

  else {

  TypeNew2.style.display = "none";

  TypeNew3.style.display = "none";

  TypeNew4.style.display = "none";

  }

});





$("#FreezMemberShipNew").change(function() {



  var Id = this.value;

  if (Id=='0'){

  DivFreezMemberShipNew0.style.display = "block";

  DivFreezMemberShipNew1.style.display = "block";

  DivFreezMemberShipNew2.style.display = "block";

  }

  else {

  DivFreezMemberShipNew0.style.display = "none";

  DivFreezMemberShipNew1.style.display = "none";

  DivFreezMemberShipNew2.style.display = "none";

  }

});











  $(document).ready(function(){

$('#MembershipNew').trigger('change');

$('#FreezMemberShipNew').trigger('change');





            // Toolbar extra buttons

            var btnFinish = $('<button></button>').text('סיום')

                                             .addClass('btn btn-success')

                                             .on('click', function(){

                                                    if( !$(this).hasClass('disabled')){

                                                        var elmForm = $("#EditItemNewnldsas");

                                                        if(elmForm){

                                                            elmForm.validator('validate');

                                                            var elmErr = elmForm.find('.has-error');

                                                            if(elmErr && elmErr.length > 0){

                                                                alert('יש למלא את השדות חובה לפני שמירה');

                                                                return false;

                                                            }

                                                            else{

                                                                //alert('מוכן לשליחה');

                                                                elmForm.submit();

                                                               // SubmitPayment();

                                                                return false;

                                                            }

                                                        }

                                                    }

                                                });

            var btnCancel = $('<button type="button" class="BtnClassWiz"></button>').text('בטל')

                                             .addClass('btn btn-danger')

                                             .on('click', function(){

                                                    var modal = $('#EditNewItems');

                                                    modal.modal('hide');

                                                    location.hash = "";

                                                    $('#ResultEditNewItems').html("");

                                                });







            // Smart Wizard

            $('#smartwizard').smartWizard({

                    selected: 0,

                    theme: 'arrows',

                    transitionEffect:'fade',

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



            $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {

                var elmForm = $("#form-step-" + stepNumber);

                // stepDirection === 'forward' :- this condition allows to do the form validation

                // only on forward navigation, that makes easy navigation on backwards still do the validation when going next

                if(stepDirection === 'forward' && elmForm){

              //     $('#ClassMemberType1').parent().removeClass('has-error');

                    elmForm.validator('validate');

                    var elmErr = elmForm.find('.has-error');

                //    var CheckClassMemberType1 = $('#CheckClassMemberType1').val();

                //    var MembershipNew = $('#MembershipNew').val();

                    if(elmErr && elmErr.length > 0){

                        // Form validation failed

                  //  $('#ClassMemberType1').parent().addClass('has-error');

                        return false;

                    }

                }

                return true;

            });



            $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {

                // Enable finish button only on last step

                if(stepNumber == 3){

                    $('.btn-finish').removeClass('disabled');

                }else{

                    $('.btn-finish').addClass('disabled');

                }

            });



        });



function getURL(id) {

     return 'action/SelectClassesType.php?GroupNumber=<?php echo @$GroupNumber; ?>&Type=0&GroupNum='+id;

}



function getURL2(id) {

     return 'action/SelectClassesType.php?GroupNumber=<?php echo @$GroupNumber; ?>&Type=1&GroupNum='+id;



}







//// שכפול מגבלה בודדת

function addElement(id)

	{

		var ni = document.getElementById('GetGroupItemId'+id);

		var numi = document.getElementById('theValue'+id);

		var num = (document.getElementById('theValue'+id).value-1)+ 2;

		numi.value = num;

		var newdiv = document.createElement('div');

		var divIdName = 'GroupItem'+num+'Div'+id;

		newdiv.setAttribute('id',divIdName);

		newdiv.innerHTML = '<div class="row" id="GetGroupItemId'+id+'-'+num+'"><div class="col-md-3" ><div class="form-group" dir="rtl"><label>סוג הגבלה</label><select class="form-control SelectType" name="SelectType'+id+''+num+'" id="SelectType"  data-num="'+num+'" data-id="'+id+'" style="width:100%;"  ><option value="0" data-num="'+id+'-'+num+'">ללא</option><?php $SectionInfos = DB::table('templistclass_data')->where('Type','=','0')->get();foreach ($SectionInfos as $SectionInfo) {?><option value="<?php echo $SectionInfo->id; ?>" data-num="'+id+'-'+num+'" ><?php echo $SectionInfo->Text; ?></option><?php } ?></select></div></div><div id="Div0'+id+'-'+num+'" style="display:block;"></div><div class="col-md-6" id="Div1'+id+'-'+num+'" style="display:none;"><div class="row"><div class="col-md-6" ><div class="form-group" dir="rtl"><label>מקסימום פעמים</label><input type="number" min="0" value="1" name="MaxTime'+id+''+num+'" class="form-control"><div class="help-block with-errors"></div></div></div><div class="col-md-6" ><div class="form-group" dir="rtl"><label>אפשרות</label><select data-num="'+num+'" data-id="'+id+'" class="form-control SelectType2" name="SelectType2'+id+''+num+'" style="width:100%;"><option value="" data-num="'+id+'-'+num+'">בחר</option><?php $SectionInfos = DB::table('templistclass_data')->where('Type','=','1')->get();foreach ($SectionInfos as $SectionInfo) {?><option value="<?php echo $SectionInfo->id; ?>" data-num="'+id+'-'+num+'" ><?php echo $SectionInfo->Text; ?></option><?php } ?></select><div class="help-block with-errors"></div></div></div></div></div><div class="col-md-6" id="Div2'+id+'-'+num+'" style="display:none;"><div class="row"><div class="col-md-12" ><div class="form-group" dir="rtl"><label>בחר ימים</label><select class="form-control selectdays2"  name="Days'+id+''+num+'[]" id="Days'+id+''+num+'"  dir="rtl" style="width:100%;" multiple="multiple"><option vaule=""></option><option vaule="0">ראשון</option><option vaule="1">שני</option><option vaule="2">שלישי</option><option vaule="3">רביעי</option><option vaule="4">חמישי</option><option vaule="5">שישי</option><option vaule="6">שבת</option></select></div></div></div></div><div class="col-md-6" id="Div3'+id+'-'+num+'" style="display:none;"><div class="row"><div class="col-md-6" ><div class="form-group" dir="rtl"><label>בין שעה</label><input type="time" name="FromTime'+id+''+num+'" class="form-control"></div></div><div class="col-md-6" ><div class="form-group" dir="rtl"><label>לשעה</label><input type="time" name="ToTime'+id+''+num+'" class="form-control"></div></div></div></div><div class="col-md-3" style="padding-top: 40px;" ><a href="javascript:;" onclick=\'removeElement(\"'+divIdName+'\",\"'+id+'\",\"'+num+'\")\' title="הסר"><i class="fas fa-trash-alt"></i></a></div></div>';

		ni.appendChild(newdiv);



        $(".SelectType").select2( {theme:"bootstrap", placeholder: "בחר שיעור", 'language':"he", dir: "rtl",ajax: {

        url: function() {

            return getURL($(this).data('id'));

        },

        dataType: 'json'

        } } );



        $(".SelectType2").select2( {theme:"bootstrap", placeholder: "בחר אפשרות", 'language':"he", dir: "rtl",ajax: {

        url: function() {

            return getURL2($(this).data('id'));

        },

        dataType: 'json'

        } } );



        $(".selectdays2").select2( {theme:"bootstrap", placeholder: "בחר ימים", 'language':"he", dir: "rtl" } );





        $('.SelectType').on('change',function(){

        var Num = $(this).data('num');

        var Id = $(this).data('id');

        var Vaule = this.value;





        $.ajax({

        url: 'action/TempClassType.php?GroupNumber=<?php echo @$GroupNumber; ?>&Vaule='+Vaule+'&GroupNum='+Id+'&Type=0&Num='+Num,

        type: 'POST',

        success: function(data) {}

        });



        var Num = $(this).find(":selected").data('num');



        if (Vaule=='0'){

        $("#Div0"+Num).css("display", "block");

        $("#Div1"+Num).css("display", "none");

        $("#Div2"+Num).css("display", "none");

        $("#Div3"+Num).css("display", "none");

        }

        else if (Vaule=='1'){

        $("#Div0"+Num).css("display", "none");

        $("#Div1"+Num).css("display", "block");

        $("#Div2"+Num).css("display", "none");

        $("#Div3"+Num).css("display", "none");

        }

        else if (Vaule=='2'){

        $("#Div0"+Num).css("display", "none");

        $("#Div1"+Num).css("display", "none");

        $("#Div2"+Num).css("display", "block");

        $("#Div3"+Num).css("display", "none");

        }

        else if (Vaule=='3'){

        $("#Div0"+Num).css("display", "none");

        $("#Div1"+Num).css("display", "none");

        $("#Div2"+Num).css("display", "none");

        $("#Div3"+Num).css("display", "block");

        }





    });



        $('.SelectType2').on('change',function(){

        var Num = $(this).data('num');

        var Id = $(this).data('id');

        var Vaule = this.value;





        $.ajax({

        url: 'action/TempClassType.php?GroupNumber=<?php echo @$GroupNumber; ?>&Vaule='+Vaule+'&GroupNum='+Id+'&Type=1&Num='+Num,

        type: 'POST',

        success: function(data) {}

        });



    });



	}





 function removeElement(divNum,id,num)

	{

		var d = document.getElementById('GetGroupItemId'+id);

		var olddiv = document.getElementById(divNum);

		var numis = document.getElementById('theValue'+id);

		var nums = (document.getElementById('theValue'+id).value);

		numis.value = nums;

		d.removeChild(olddiv);



    $.ajax({

    url: 'action/TempClassDels.php?GroupNumber=<?php echo @$GroupNumber; ?>&GroupNum='+id+'&Num='+num,

    type: 'POST',

    success: function(data) {}

    });





	}





//// שכפול קבוצה

function addElementgroup()

	{

		var ni = document.getElementById('GetGroupId');

		var numi = document.getElementById('theValueGroup');

		var num = (document.getElementById('theValueGroup').value-0)+ 1;

		numi.value = num;

		var newdiv = document.createElement('div');

		var divIdName = 'Group'+num+'Div';

		newdiv.setAttribute('id',divIdName);

		newdiv.innerHTML = ' <div id="GroupId1"><strong>קבוצה '+num+'</strong><div class="form-group" dir="rtl"><label>בחר שיעור</label><select class="form-control js-example-basic-single select2multipleDesk newid'+num+' text-right" name="ClassMemberType'+num+'[]" id="ClassMemberType'+num+'" dir="rtl"   multiple="multiple" data-select2order="true" style="width: 100%;"></select><input type="hidden" id="CheckClassMemberType'+num+'" value=""><div class="help-block with-errors"></div></div> <div id="GetGroupItemId'+num+'"></div><a class="btn btn-secondary btn-sm" href="javascript:void(0);" onclick=\'addElement(\"'+num+'\")\' >הוסף מגבלה חדשה +</a><hr><div class="alertb alert-info">הגדרת רישום נוסף ע"ב מקום פנוי</div><div class="row"><div class="col-3"><div class="form-group" dir="rtl"><label>בחר אפשרות</label><select name="StandByOption'+num+'" id="StandByOption" class="form-control StandByOption" style="width:100%;"  data-placeholder="בחר"  ><option value="0" data-num="'+num+'">ללא</option><option value="1" data-num="'+num+'">ע"ב מקום פנוי</option></select></div></div> <div class="col-2 DivStandBy'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>כמות שיעורים</label><input type="number" min="1" name="StandByCount'+num+'" class="form-control" value="1"></div></div><div class="col-2 DivStandBy'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>בתקופה של</label><select name="StandByVaild_Type'+num+'" class="form-control" style="width:100%;"  data-placeholder="בחר"  ><option value="1">יום</option><option value="2">שבוע</option><option value="3">חודש</option><option value="4">שנה</option></select></div></div><div class="col-3 DivStandBy'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>זמן לפני השיעור</label><input type="number" class="form-control" name="StandByTime'+num+'" min="1" value="1"></div></div><div class="col-2 DivStandBy'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>אפשרות</label><select name="StandByTimeVaild_Type'+num+'" class="form-control" style="width:100%;"  data-placeholder="בחר"  ><option value="1">דקות</option><option value="2" selected>שעות</option></select></div></div></div><hr><div class="alertb alert-info" style="display: none;">הגדרת פריט UpSale לרכישה אונליין לאחר חסימה ע"ב המגבלות</div>    <div class="row" style="display: none;"><div class="col-2"><div class="form-group" dir="rtl"><label>בחר אפשרות</label><select name="UpSaleOption'+num+'" id="UpSaleOption" class="form-control UpSaleOption" style="width:100%;"  data-placeholder="בחר"  ><option value="0" data-num="'+num+'" selected>לא</option><option value="1" data-num="'+num+'" >כן</option></select></div></div><div class="col-2 DivUpSale'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>כותרת לפריט</label><input type="text" name="UpSaleTitle'+num+'" class="form-control"></div></div><div class="col-2 DivUpSale'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>כמות שיעורים</label><input type="text" name="UpSaleClass'+num+'" class="form-control"></div></div><div class="col-2 DivUpSale'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>מחיר לפריט</label><input type="text" name="UpSalePrice'+num+'" class="form-control"></div></div><div class="col-2 DivUpSale'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>תוקף</label><input type="text" name="UpSaleVaild'+num+'" class="form-control"></div></div><div class="col-2 DivUpSale'+num+'" style="display: none;"><div class="form-group" dir="rtl"><label>חשב לפי</label><select name="UpSaleVaild_Type'+num+'" class="form-control" style="width:100%;"  data-placeholder="בחר"  ><option value="1">ימים</option><option value="2">שבועות</option><option value="3" selected>חודשים</option></select><input type="hidden" name="UpSaleOption'+num+'" value="0"></div></div></div><a href="javascript:;" class="btn btn-danger btn-sm" onclick=\'removeElementgroup(\"'+divIdName+'\",\"'+num+'\")\' title="הסר">הסר קבוצה <i class="fas fa-trash-alt"></i></a><input type="hidden" value="0" id="theValue'+num+'" name="tItems'+num+'"/><hr></div>  ';

		ni.appendChild(newdiv);

        $(".select2multipleDesk").select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור", 'language':"he", dir: "rtl",ajax: {

        url: 'action/SelectClasses.php?GroupNumber=<?php echo @$GroupNumber; ?>',

        dataType: 'json'

        } } );

        removeselectionclass(num);





	}





 function removeElementgroup(divNum,num)

	{

		var d = document.getElementById('GetGroupId');

		var olddiv = document.getElementById(divNum);

		var numis = document.getElementById('theValueGroup');

		var nums = (document.getElementById('theValueGroup').value);

		numis.value = nums;

		d.removeChild(olddiv);



    $.ajax({

    url: 'action/TempClassDel.php?GroupNumber=<?php echo @$GroupNumber; ?>&GroupNum='+num,

    type: 'POST',

    success: function(data) {}

    });





	}





function removeselectionclass(num)

{





$("#ClassMemberType"+num).on("select2:select select2:unselect", function (e) {



    //this returns all the selected item

    var items= $(this).val();

    var Oldarray = $('#ChangeMe').val();

    var array = $('#ChangeMe').val(items);

    $('#CheckClassMemberType'+num).val(items);

    //// עדכון טבלה זמנית



    $.ajax({

    url: 'action/TempClass.php?GroupNumber=<?php echo @$GroupNumber; ?>&Clases='+items+'&GroupNum='+num,

    type: 'POST',

    success: function(data) {}

    });



    //Gets the last selected item

    var lastSelectedItem = e.params.data.id;



});





        $('.UpSaleOption').on('change',function(){



        var Num = $(this).find(":selected").data('num');

        var Vaule = this.value;



        if (Vaule=='0'){

        $(".DivUpSale"+Num).css("display", "none");

        }

        else if (Vaule=='1'){

        $(".DivUpSale"+Num).css("display", "block");

        }





    });





        $('.StandByOption').on('change',function(){



        var Num = $(this).find(":selected").data('num');

        var Vaule = this.value;



        if (Vaule=='0'){

        $(".DivStandBy"+Num).css("display", "none");

        }

        else if (Vaule=='1' || Vaule=='2'){

        $(".DivStandBy"+Num).css("display", "block");

        }





    });











}





        $('.SelectType').on('change',function(){

        var Num = $(this).data('num');

        var Id = $(this).data('id');

        var Vaule = this.value;





        $.ajax({

        url: 'action/TempClassType.php?GroupNumber=<?php echo @$GroupNumber; ?>&Vaule='+Vaule+'&GroupNum='+Id+'&Type=0&Num='+Num,

        type: 'POST',

        success: function(data) {}

        });



        var Num = $(this).find(":selected").data('num');



        if (Vaule=='0'){

        $("#Div0"+Num).css("display", "block");

        $("#Div1"+Num).css("display", "none");

        $("#Div2"+Num).css("display", "none");

        $("#Div3"+Num).css("display", "none");

        }

        else if (Vaule=='1'){

        $("#Div0"+Num).css("display", "none");

        $("#Div1"+Num).css("display", "block");

        $("#Div2"+Num).css("display", "none");

        $("#Div3"+Num).css("display", "none");

        }

        else if (Vaule=='2'){

        $("#Div0"+Num).css("display", "none");

        $("#Div1"+Num).css("display", "none");

        $("#Div2"+Num).css("display", "block");

        $("#Div3"+Num).css("display", "none");

        }

        else if (Vaule=='3'){

        $("#Div0"+Num).css("display", "none");

        $("#Div1"+Num).css("display", "none");

        $("#Div2"+Num).css("display", "none");

        $("#Div3"+Num).css("display", "block");

        }





    });







 $(".select2multipleDesk").select2( {theme:"bootstrap", placeholder: "בחר שיעור", 'language':"he", dir: "rtl",ajax: {

        url: 'action/SelectClasses.php?GroupNumber=<?php echo @$GroupNumber; ?>',

        dataType: 'json'

        } } );





$("#ClassMemberType1").on("select2:select select2:unselect", function (e) {



    //this returns all the selected item

    var items= $(this).val();

    var Oldarray = $('#ChangeMe').val();

    var array = $('#ChangeMe').val(items);

    $('#CheckClassMemberType1').val(items);





    //// עדכון טבלה זמנית



    $.ajax({

    url: 'action/TempClass.php?GroupNumber=<?php echo @$GroupNumber; ?>&Clases='+items+'&GroupNum=1',

    type: 'POST',

    success: function(data) {}

    });



    //Gets the last selected item

    var lastSelectedItem = e.params.data.id;



});





        $('.UpSaleOption').on('change',function(){



        var Num = $(this).find(":selected").data('num');

        var Vaule = this.value;



        if (Vaule=='0'){

        $(".DivUpSale"+Num).css("display", "none");

        }

        else if (Vaule=='1'){

        $(".DivUpSale"+Num).css("display", "block");

        }





    });





        $('.StandByOption').on('change',function(){



        var Num = $(this).find(":selected").data('num');

        var Vaule = this.value;



        if (Vaule=='0'){

        $(".DivStandBy"+Num).css("display", "none");

        }

        else if (Vaule=='1' || Vaule=='2'){

        $(".DivStandBy"+Num).css("display", "block");

        }





    });



      $('.UpSaleOption').trigger('change');

      $('.StandByOption').trigger('change');

      $('.SelectType').trigger('change');

      $('.SelectType2').trigger('change');

       $(".selectdays2").select2( {theme:"bootstrap", placeholder: "בחר ימים", 'language':"he", dir: "rtl" } );





          $(".SelectType").select2( {theme:"bootstrap", placeholder: "בחר שיעור", 'language':"he", dir: "rtl",ajax: {

        url: function() {

            return getURL($(this).data('id'));

        },

        dataType: 'json'

        } } );



        $(".SelectType2").select2( {theme:"bootstrap", placeholder: "בחר אפשרות", 'language':"he", dir: "rtl",ajax: {

        url: function() {

            return getURL2($(this).data('id'));

        },

        dataType: 'json'

        } } );



  </script>

