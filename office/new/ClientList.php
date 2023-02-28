<?php require_once '../../app/initcron.php'; ?>


<style>
  @media only screen and (max-width: 600px) {
    .ClientClose {
    position: relative;
    right: 85%;
  }
}
</style>

<?php
$Id = $_REQUEST['Id'];

$CompanyNum = Auth::user()->CompanyNum;



$AppSettings = DB::table('classsettings')->where('CompanyNum', $CompanyNum)->first();

$settings = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();



$WatingPopUp = $AppSettings->WatingListPOPUP;



$ClassInfo = DB::table('classstudio_date')->where('id','=', $Id)->where('CompanyNum', $CompanyNum)->first();

$Floor = DB::table('sections')->where('id','=', $ClassInfo->Floor)->where('CompanyNum', $CompanyNum)->first();

$ClassDeviceName = DB::table('numbers')->where('CompanyNum', $CompanyNum)->where('id', '=', $ClassInfo->ClassDevice)->where('Status', '=', '0')->first();



$ClassRegularCount = DB::table('classstudio_act')

->where('CompanyNum', '=', $CompanyNum)->where('ClassId', '=', $ClassInfo->id)->where('RegularClass', '=', '1')->whereIn('Status', array(9, 12))

->count();





if ($ClassInfo->ClassMemberType=='BA999'){

$MembershipType = lang('all_membership_types');   

}

else {

$z = '1';

$myArray = explode(',', $ClassInfo->ClassMemberType);	

$MembershipType = '';	

$SoftInfos = DB::table('membership_type')->where('CompanyNum', $CompanyNum)->whereIn('id', $myArray)->get();

$SoftCount = count($SoftInfos);

	

foreach ($SoftInfos as $SoftInfo){



$MembershipType .= $SoftInfo->Type;



if($SoftCount==$z){}else {	

$MembershipType .= ', ';	

}

	

++$z; 	

}	



$MembershipType = $MembershipType;

}





if ($CompanyNum=='100'){

$HideTest = '';    

}

else {

$HideTest = '';    

}





?>





            

 <div class="row">

 <div class="col-md-3">	 

 <?php echo $ClassInfo->ClassName ?> 

 </div>  

  <div class="col-md-3">	 

  <?php echo $ClassInfo->GuideName ?> 

 </div>  

 <div class="col-md-3">	 

 <?php echo $Floor->Title ?> 

 </div>   

  <div class="col-md-3">

 <?php if ($ClassInfo->MinClass=='0') { echo lang('without_min_patricipants'); } else { ?>   

  <?php echo lang('min_participants') ?>: <?php echo $ClassInfo->MinClassNum; } ?> 

 </div>  

</div>





 <div class="row">

 <div class="col-md-3">	 

 <?php echo lang('date') ?>: <?php echo with(new DateTime($ClassInfo->StartDate))->format('d/m/Y'); ?> 

 </div> 

  <div class="col-md-3">	 

  <?php echo lang('day') ?>: <?php echo $ClassInfo->Day ?>

 </div>       

  <div class="col-md-3">	 

 <?php echo lang('class_start') ?>: <?php echo with(new DateTime($ClassInfo->StartTime))->format('H:i'); ?>

 </div>  

  <div class="col-md-3">	 

  <?php echo lang('class_end') ?>: <?php echo with(new DateTime($ClassInfo->EndTime))->format('H:i'); ?> 

 </div>  

</div>



  <hr>            

 <div class="row">

 <div class="col-md-<?php if ($ClassInfo->ClassDevice=='0'){ echo '12'; } else { echo '6'; }?>">	 

 <label><?php echo lang('membership_type_single') ?>:</label>

<?php echo $MembershipType; ?> 

 </div>  

<?php if ($ClassInfo->ClassDevice=='0'){} else {?>     

     <div class="col-md-6">	 

 <label><?php echo lang('eauipment_type_single') ?>:</label>

<?php echo @$ClassDeviceName->Name; ?> 

 </div>

<?php } ?>     

</div>

  <hr>            

 <div class="d-flex justify-content-between">

 <div>

 <label><?php echo lang('class_booking_num') ?>:</label>

<span class="text-primary font-weight-bold"><?php echo $ClassInfo->ClientRegister; ?> <?php echo lang('of_user_manage') ?> <?php echo $ClassInfo->MaxClient; ?> <?php echo lang('registered') ?> (<?php echo $ClassInfo->MaxClient-$ClassInfo->ClientRegister; ?> <?php echo lang('available_spaces') ?>)</span>

 </div>  

     

<div>

<label><?php echo lang('w_list') ?>:</label>

<span class="text-danger font-weight-bold"><?php echo $ClassInfo->WatingList; ?> <?php echo lang('class_in_waitlist') ?></span>

    

  

<a href="#" id="RunWatingList" class="btn btn-outline-secondary btn-sm"><?php echo lang('class_run_waitlist') ?></a>

  

    

 </div>   

     

</div>





<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.0/css/all.css" integrity="sha384-IIED/eyOkM6ihtOiQsX2zizxFBphgnv1zbe1bKA+njdFzkr6cDNy16jfIKWu4FNH" crossorigin="anonymous">



<hr>

  





<div class="row">

<?php if ($ClassInfo->Status=='2'){ ?>

    

 <div class="col-md-12 text-left">

<!-- --><?php //if (Auth::userCan('119')): ?><!--       -->

  <a href="javascript:void(0)" class="btn btn-dark btn-sm text-white" id="ShowClientPopUpDiv" style="pointer-events: none;"><?php echo lang('class_embed_button') ?></a>

<!-- --><?php //endif; ?>

</div>  



<?php } else { ?>    

<div class="col-md-12 text-left">

<?php if (Auth::userCan('119')): ?>

<!-- button becomes unclicked if class was completed or canceled -->

<a href="javascript:void(0)" class="btn btn-dark btn-sm text-white" id="ShowClientPopUpDiv" ><?php echo lang('class_embed_button') ?></a>

<?php endif; ?>

</div>  

 <?php } ?> 

    

<div class="col-md-12">	



<div id="ClientPopUpDiv" style="display: none;">    

<div class="form-group" >

<label><?php echo lang('choose_client') ?></label>

<select name="AddClientActivity" id="AddClientActivity" data-placeholder="<?php echo lang('choose_client') ?>" class="form-control select2ClientDesk" style="width:100%;">

<option value=""></option>  

</select>

</div>  

 

    

<div id="ClientActivityInfo">



</div>    

    

</div> 

</div>

</div>    



    

<div class="alertb alert-danger" id="ClientWatingListText" style="display: none;">	

</div>

   





 <div class="row">

 <div class="col-md-12">	

 <?php echo lang('attendees') ?>:

</div>

</div>     



       <style>

       

           .DivScroll::-webkit-scrollbar {

             width: 5px;

             padding-left: 0px;

             margin-left: 0px;

           } 

           

             .DivScroll::-webkit-scrollbar-thumb {

             background-color: darkgrey;

             outline: 1px solid slategray;

            padding-left: 0px;

             margin-left: 0px;     

           }    

           

       

       </style>   





 <div class="row">

 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:320px; overflow-y:scroll; overflow-x:hidden;'>

<table class="table table-bordered table-sm table-responsive-sm">



<thead>

<th class="text-start"  ><?php echo lang('status') ?></th>

<th class="text-start" ><?php echo lang('class_table_name') ?></th>

<?php if (Auth::userCan('121')): ?>        

<th   class="text-start <?php echo $HideTest; ?>"><?php echo lang('phone') ?></th>

<?php endif ?>    

<?php if (Auth::userCan('122')): ?>        

<th  class="text-start <?php echo $HideTest; ?>" ><?php echo lang('bookkeeping') ?></th>

<?php endif ?>     

<th   class="text-start <?php echo $HideTest; ?>"><?php echo lang('expires_at') ?></th>

<th  class="text-start <?php echo $HideTest; ?>"><?php echo lang('class_tabe_card') ?></th>    



<th  class="text-start"></th>    

</thead>



<tbody>

<?php 



$Clients = DB::table('classstudio_act')->where('ClassId', '=', $Id)->where('CompanyNum', $CompanyNum)->orderBy('StatusCount','ASC')->orderBy('id','ASC')->orderBy('WatingListSort','ASC')->get(); 

foreach ($Clients as $Client) {



   

    

if ($Client->TrueClientId=='0'){

$TrueClientId = $Client->ClientId;

$TrueClientIcon = '';    

}   

else {

$TrueClientId = $Client->TrueClientId;

$TrueClientIcon = '<i class="fas fa-user-friends" data-toggle="tooltip" data-placement="top" title="'.lang('family_membersip').'"></i>';

}    

    

//// בדיקת שיעור ראשון בסטודיו 

$FirstClass = '';

    

$FirstClassCount = DB::table('classstudio_act')->where('ClassDate', '<', $Client->ClassDate)->where('CompanyNum', $CompanyNum)->where('FixClientId', $Client->FixClientId)->whereIn('Status',array(1,2,4,6,8,10,11,12,15,16,21))->count();      

    

if (@$FirstClassCount=='0'){

$FirstClass = '<i class="fas fa-certificate text-primary" data-toggle="tooltip" data-placement="top" title="'.lang('first_class').'"></i>';

DB::table('classstudio_act')

->where('FixClientId', '=', $Client->FixClientId)

->where('CompanyNum', $CompanyNum)    

->where('id', $Client->id)    

->update(array('FirstClass' => '1')); 	

	

}    

else {

$FirstClass = '';   

}    


$ClientName = DB::table('client')->where('id', '=', $Client->FixClientId)->where('CompanyNum', $CompanyNum)->first();

$ActivityInfo = DB::table('client_activities')->where('id', $Client->ClientActivitiesId)->where('CompanyNum', $CompanyNum)->first(); 

$StatusInfoColor = DB::table('class_status')->where('id', '=', $Client->Status)->first();    



$DeviceTitle = DB::table('numberssub')->where('CompanyNum', $CompanyNum)->where('NumbersId', '=', $ClassInfo->ClassDevice)->where('id', '=', $Client->DeviceId)->where('Status', '=', '0')->first();    



    

$Mediacl = DB::table('clientmedical')

->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientName->id)->whereNull('TillDate')->where('Status', '=', '0')

->Orwhere('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientName->id)->where('TillDate', '>=', date('Y-m-d'))->where('Status', '=', '0')

->orderBy('dates','DESC')    

->first();    

    

if (@$Mediacl->id!=''){

$TrueMedicalIcon = ' <i class="fas fa-briefcase-medical text-danger" data-toggle="tooltip" data-placement="top" title="'.lang('table_medical_records').'"></i> ';

} 

else {

$TrueMedicalIcon = '';      

}    

    

    

$CRM = DB::table('clientcrm')

->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientName->id)->where('StarIcon', '=', '1')->where('Status', '=', '0')->whereNull('TillDate')

->Orwhere('CompanyNum', $CompanyNum)->where('ClientId', '=', $ClientName->id)->where('StarIcon', '=', '1')->where('Status', '=', '0')->where('TillDate', '>=', date('Y-m-d'))

->orderBy('dates','DESC')    

->first();    

    

if (@$CRM->id!=''){

if ($CompanyNum=='569121') {    

$TrueCRMIcon = ' <i class="fas fa-exclamation-triangle" data-toggle="tooltip" data-placement="top" title="'.lang('push_notice').'" style="color:sandybrown;"></i> ';

}

else {

$TrueCRMIcon = ' <i class="fas fa-star-of-life" data-toggle="tooltip" data-placement="top" title="'.lang('customer_card_phone_records').'" style="color:sandybrown;"></i> ';

}    

} 

else {

$TrueCRMIcon = '';      

}      

    

    

    



if (@$ClientName->Age!='' && $CompanyNum=='85234'){

$NewAge = '<span class="text-primary" style="font-size: 10px;">('.$ClientName->Age.')</span>';    

} 

else {

$NewAge = '';    

}    

    

    

if ($ClientName->BalanceAmount>0){

$BalanceMoneyColor = 'red';		

}

else {

$BalanceMoneyColor = 'black';		

}

    

    

    if (@$ActivityInfo->Department=='1' && $ActivityInfo->FirstDateStatus=='0') {

  

    if ($ActivityInfo->TrueDate>=date('Y-m-d')){

    $CheckBoxColor = 'success';    

    }

    else {

    $CheckBoxColor = 'danger';     

    }    

        

        

    }

    else if (@$ActivityInfo->Department=='2' || @$ActivityInfo->Department=='3') {

 

        

    if ($ActivityInfo->TrueBalanceValue>='1'){

    $CheckBoxColor = 'success';    

    }

    else {

    $CheckBoxColor = 'danger';     

    }      

        

        

    if ($ActivityInfo->TrueDate!='' && $ActivityInfo->FirstDateStatus=='0'){ 

        

    if ($ActivityInfo->TrueDate>=date('Y-m-d')){

    $CheckBoxColor = 'success';    

    }

    else {

    $CheckBoxColor = 'danger';   

    }      

        

    }

        

    }    

   

    

if (@$ClientName->Dob=='' || @$ClientName->Dob=='0000-00-00'){ $BDayIcon = ''; }else {

$from = new DateTime($ClientName->Dob);

$to   = new DateTime('today');



$ThisBDay = with(new DateTime(@$ClientName->Dob))->format('m');		    

$ThisMonth = date('m');

	

if ($ThisBDay==$ThisMonth){	

$BDayIcon = '<i class="fas fa-birthday-cake" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="'.lang('congratulations').'" style="color:pink;"></i>';

}

else {

$BDayIcon = '';	

}	

	

}

//if ($settings->coronaStmt == '1') {
//  if ($Client->coronaStmt == '1') {
//    $coronaIcon = '<i class="fal fa-head-side-mask" title="מילא הצהרת קורונה" style="color: #0080ff;"></i>';
//  } else {
//    $coronaIcon = '<i class="fal fa-virus" title="לא מילא הצהרת קורונה" style="color: red;"></i>';
//  }
//
//} else {
//  $coronaIcon = '';
//}
//

if($settings->greenPass) {
    if ($ClientName->greenPassStatus == 0) {
        $greenPassText = lang('no_green_pass');
        $cssClass = 'text-danger';
        $badgeIcon = '<i class="far fa-badge fa-lg"></i>';
        $coronaIcon = '<i data-status="'.$ClientName->greenPassStatus.'" data-id="'.$ClientName->id.'" class="js-green-pass-icon far fa-badge cursor-pointer mis-5 '.$cssClass.'" data-toggle="tooltip" data-placement="top" title="'.$greenPassText.'"></i>';
    } elseif ($ClientName->greenPassStatus == 1) {
        $greenPassText = lang('green_pass_pending_notice');
        $cssClass = 'text-orange';
        $badgeIcon = '<i class="far fa-badge-check fa-lg"></i>';
        $coronaIcon = '<i data-status="'.$ClientName->greenPassStatus.'" data-id="'.$ClientName->id.'" class="js-green-pass-icon far fa-badge-check cursor-pointer mis-5 '.$cssClass.'" data-toggle="tooltip" data-placement="top" title="'.$greenPassText.'"></i>';
    } else {
        $greenPassText = lang('green_pass_confirmed_notice');
        $cssClass = 'text-success';
        $badgeIcon = '<i class="fas fa-badge-check fa-lg"></i>';
        $coronaIcon = '<i data-status="'.$ClientName->greenPassStatus.'" data-id="'.$ClientName->id.'" class="js-green-pass-icon fas fa-badge-check cursor-pointer mis-5 '.$cssClass.'" data-toggle="tooltip" data-placement="top" title="'.$greenPassText.'"></i>';
    }
} else {
    $coronaIcon = '';
}





?>



<tr <?php echo $StatusInfoColor->Color; ?> >

<td class="align-middle" width="30%">

<?php //if (Auth::userCan('120')): ?><!--     -->
<?php if (true): //only for pass Auth 120  ?>

<?php if ($ClassInfo->Status == '2')  {?>    
 
 <select name="StatusEvent" id="StatusEvent<?php echo $Client->id ?>" data-placeholder="<?php echo lang('choose_status') ?>" class="form-control" style="width:100%;" disabled>

<?php } else {?>

  <select name="StatusEvent" id="StatusEvent<?php echo $Client->id ?>" data-placeholder="<?php echo lang('choose_status') ?>" class="form-control" style="width:100%;">

<?php }?>

<?php 

$ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('PopUpStatus', '=', '0')->orderBy('id', 'ASC')->get();  

foreach ($ClassStatusInfos as $ClassStatusInfo) {    

?>    

	

<option value="<?php echo $Client->id ?>:<?php echo $Client->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($Client->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo $ClassStatusInfo->Title ?> <?php if ($Client->WatingListSort=='1' && $Client->Status=='9'){ echo lang('first'); } else {} ?></option>

	

<?php } ?>

	

<?php 

$ClassStatusInfos = DB::table('class_status')->where('Status', '=', '0')->where('PopUpStatus', '=', '1')->where('id', '=', $Client->Status)->orderBy('id', 'ASC')->get();  

foreach ($ClassStatusInfos as $ClassStatusInfo) {    

?>    

	

<option value="<?php echo $Client->id ?>:<?php echo $Client->ClientId ?>:<?php echo $ClassStatusInfo->id ?>" <?php if ($Client->Status==$ClassStatusInfo->id){ echo 'selected'; } else {} ?> <?php if ($ClassStatusInfo->PopUpStatus=='1'){ echo 'disabled'; } else {} ?>><?php echo $ClassStatusInfo->Title ?> <?php if ($Client->WatingListSort=='1' && $Client->Status=='9'){ echo lang('first'); } else {} ?></option>

	

<?php } ?>	

	

	

	

</select>



<?php endif ?>    

</td>

<td class="align-middle"><a href="ClientProfile.php?u=<?php echo $ClientName->id; ?>"><span class="text-primary"><?php echo $ClientName->CompanyName; ?></span></a> <?php echo $NewAge; ?> <?php echo $TrueClientIcon; echo $TrueMedicalIcon; echo $TrueCRMIcon; echo $BDayIcon; echo $FirstClass; echo $coronaIcon; ?></td>

<?php if (Auth::userCan('121')): ?>      

<td class="unicode-plaintext align-middle <?php echo $HideTest; ?>"> <?php echo $ClientName->ContactMobile; ?></td>

<?php endif ?>    

<?php if (Auth::userCan('122')): ?>      

<td class="align-middle <?php echo $HideTest; ?>"><span style="color:<?php echo $BalanceMoneyColor?>;"><?php echo number_format($ClientName->BalanceAmount, 2); ?> ₪</span></td>

<?php endif ?>    

<td class="align-middle <?php echo $HideTest; ?>"><span class="text-<?php echo @$CheckBoxColor;?>"><?php if (($ActivityInfo->Department=='1' && $ActivityInfo->FirstDateStatus == '0') || ($ActivityInfo->Department=='2' && $ActivityInfo->TrueDate!='' && $ActivityInfo->FirstDateStatus=='0')){ echo with(new DateTime($ActivityInfo->TrueDate))->format('d/m/Y'); } ?></span></td>

<td class="align-middle <?php echo $HideTest; ?>"><span  class="text-<?php echo @$CheckBoxColor;?>  unicode-plaintext" id="ClientTRDiv_Card<?php echo $Client->id ?>"><?php if ($ActivityInfo->Department=='2' || $ActivityInfo->Department=='3'){ echo $ActivityInfo->TrueBalanceValue; ?> / <?php echo $ActivityInfo->BalanceValue; } ?></span></td>    



    

<td class="align-middle"><a href="javascript:void(0)" id="ShowClientTRDiv<?php echo $Client->id ?>"><span class="text-info"><i class="fas fa-chevron-circle-down"></i></span></a></td>    

    

</tr>

    

    

<tr style="font-size: 13px; display: none; width: 100%" id="ClientTRDiv<?php echo $Client->id ?>">   

<td colspan="<?php if ($ClassInfo->ClassDevice=='0'){ echo '2'; } else { echo '2'; } ?>">

<u><?php echo lang('table_medical_records') ?>:</u> <?php echo @$Mediacl->Content; ?>

<?php if (@$CRM->id!=''){ ?> 

<br> 

<?php if ($CompanyNum=='569121') { ?>    

<u><?php echo lang('push_notice') ?>:</u> <?php echo @$CRM->Remarks; ?>  

<?php } else { ?>    

<u><?php echo lang('customer_card_phone_records') ?>:</u> <?php echo @$CRM->Remarks; ?>    

<?php } ?>        

<?php } ?>    

</td> 

    

<td colspan="<?php if ($ClassInfo->ClassDevice=='0'){ echo '5'; } else { echo '3'; } ?>"><u><?php echo lang('class_notice') ?>:</u> <span id="ClientTRDiv_Remarks<?php echo $Client->id ?>"><?php echo $Client->Remarks ?></span>  <a href="javascript:void(0)" id="ShowClientTRRemarksDiv<?php echo $Client->id ?>"><span class="text-info"><i class="fas fa-edit"></i></span></a></td>

<?php if ($ClassInfo->ClassDevice=='0'){} else {?>    

<td colspan="2"><u><?php echo lang('a_select_equipment') ?>:</u> <span id="ClientTRDiv_DeviceTitle<?php echo $Client->id ?>"><?php echo @$DeviceTitle->Name; ?></span> <a href="javascript:void(0)" id="ShowClientTRDeviceDiv<?php echo $Client->id ?>"><span class="text-info"><i class="fas fa-edit"></i></span></a></td> 

<?php } ?>

</tr>

    



<tr style="font-size: 13px; display: none;" id="ClientTRRemarksDiv<?php echo $Client->id ?>" class="HideDiv">   

<td colspan="7">

   

<form action="AddClientRemarksPopUp" class="ajax-form clearfix text-right" autocomplete="off"> 

<div class="form-group">     

<input type="hidden" name="ActivityId" value="<?php echo $Client->id; ?>">    

<input type="hidden" name="ClientId" value="<?php echo $Client->ClientId; ?>">    

<select name="ShowRemarks" class="form-control" style="width:100%;" >

<option value="1"><?php echo lang('displayed_to_client') ?></option>

<option value="0" selected><?php echo lang('hidden_from_client') ?></option>    

</select>    

</div> 

<div class="form-group">    

<textarea name="ClientTRRemarks" class="form-control" rows="2" dir="rtl"><?php echo $Client->Remarks ?></textarea>

</div>   

<div class="form-group">

<button type="submit" name="submit" class="btn btn-dark btn-sm text-white"><?php echo lang('save_changes_button') ?></button>    

</div>

</form>    

</td>    

</tr> 

    

<tr style="font-size: 13px; display: none;" id="ClientTRDeviceDiv<?php echo $Client->id ?>" class="HideDiv">   

<td colspan="7">

   

<form action="AddClientDevicePopUp" class="ajax-form clearfix text-right" autocomplete="off"> 

<div class="form-group">     

<input type="hidden" name="ActivityId" value="<?php echo $Client->id; ?>">    

<input type="hidden" name="ClientId" value="<?php echo $Client->ClientId; ?>">

    

<?php

echo @$ClassDeviceName->Name;     

?>

</div>     

<div class="form-group">     

<select name="DeviceId" data-placeholder="בחר <?php echo @$ClassDeviceName->Name; ?>" class="form-control select2General" style="width:100%;" >

<option value=""></option>    

<?php 

$ClassDeviceInfos = DB::table('numberssub')->where('NumbersId', $ClassInfo->ClassDevice)->where('CompanyNum', $CompanyNum)->where('Status', '=', '0')->orderBy('Name', 'ASC')->get();  

foreach ($ClassDeviceInfos as $ClassDeviceInfo) {   

    

$CheckClassDevice = DB::table('classstudio_act')->where('ClassId', '=', $Id)->where('CompanyNum', $CompanyNum)->where('DeviceId', '=', $ClassDeviceInfo->id)->where('StatusCount', '=', '0')->first();

 

if (@$CheckClassDevice->id!=''){ } else { 

    

?>    

<option value="<?php echo $ClassDeviceInfo->id ?>" <?php if (@$Client->DeviceId==$ClassDeviceInfo->id){ echo 'disabled'; } else {} ?>><?php echo $ClassDeviceInfo->Name ?></option>

<?php } } ?>

</select>    

    

</div>   

<div class="form-group">

<button type="submit" name="submit" class="btn btn-dark btn-sm text-white"><?php echo lang('save_changes_button') ?></button>    

</div>

</form>    

</td>    

</tr>     

  



<tr style="font-size: 13px; width: 100%;" id="ClientTRDivs<?php echo $Client->id ?>">   

<td colspan="8"><u><?php echo lang('complete_class') ?>:</u> <span id="ClientTRDiv_ClassTitle<?php echo $Client->id ?>"><?php echo $Client->ReClassReason ?></span>  <a href="javascript:void(0)" id="ShowClientTRClassInsetedDiv<?php echo $Client->id ?>"><span class="text-info"><i class="fas fa-edit"></i></span></a></td>   

</tr>    



    

    

<tr style="font-size: 13px; display:none;" id="ClientTRClassInsetedDiv<?php echo $Client->id ?>" class="HideDiv">   

<td colspan="8">

   

<form action="AddClientClassInsetedPopUp" class="ajax-form clearfix text-right" autocomplete="off"> 

<div class="form-group">     

<input type="hidden" name="ActivityId" value="<?php echo $Client->id; ?>">    

<input type="hidden" name="ClientId" value="<?php echo $Client->ClientId; ?>">

<?php echo lang('select_a_class_to_complete') ?>   

</div>     

<div class="form-group">     



<select name="ForWhichReClass" data-placeholder="<?php echo lang('choose_class') ?>" class="form-control select2General" style="width:100%;" >

<option value=""></option>    

<?php 

$ReplaceClassInfos = DB::table('classstudio_act')->where('ClientId', $Client->ClientId)->where('CompanyNum', $CompanyNum)->where('ClassDate', '<', $Client->ClassDate)->orderBy('ClassDate', 'ASC')->get();  

foreach ($ReplaceClassInfos as $ReplaceClassInfo) {   

    

?>    

<option value="<?php echo $ReplaceClassInfo->id ?>" <?php if (@$Client->ForWhichReClass==$ReplaceClassInfo->id){ echo 'selected'; } else {} ?>><?php echo $ReplaceClassInfo->ClassName; ?></option>

<?php } ?>

</select>     

</div>   

<div class="form-group">   

<label><?php echo lang('class_fullfill_reason') ?></label>    

<textarea name="ClientReClassReason" class="form-control" rows="2" dir="rtl"><?php echo $Client->ReClassReason ?></textarea>    

</div>     

<div class="form-group">

<button type="submit" name="submit" class="btn btn-dark btn-sm text-white"><?php echo lang('save_changes_button') ?></button>    

</div>

</form>    

</td>    

</tr>     



    

<script>

			

$(document).ready(function(){	



$('#ShowClientTRDiv<?php echo $Client->id ?>').click(function() {

     if($('#ClientTRDiv<?php echo $Client->id ?>').is(":hidden"))

    {   

     $('#ClientTRDiv<?php echo $Client->id ?>').show();

     $('#ShowClientTRDiv<?php echo $Client->id ?>').html('<span class="text-info"><i class="fas fa-chevron-circle-up"></i></span>');

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();    

     $( "#ClientActivityInfo" ).empty(); 

     ClientPopUpDiv.style.display = "none";    

    }

    else {

     $('#ClientTRDiv<?php echo $Client->id ?>').hide();

     $('#ShowClientTRDiv<?php echo $Client->id ?>').html('<span class="text-info"><i class="fas fa-chevron-circle-down"></i></span>');

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();    

     $("#ClientActivityInfo").empty();        

     ClientPopUpDiv.style.display = "none";    

    }

 });



$('#ShowClientTRRemarksDiv<?php echo $Client->id ?>').click(function() {

     if($('#ClientTRRemarksDiv<?php echo $Client->id ?>').is(":hidden"))

    {   

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').show();

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();    

     $( "#ClientActivityInfo" ).empty();     

     ClientPopUpDiv.style.display = "none";     

    }

    else {

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();    

     $( "#ClientActivityInfo" ).empty();     

     ClientPopUpDiv.style.display = "none";     

    }

 });

    

    

$('#ShowClientTRDeviceDiv<?php echo $Client->id ?>').click(function() {

     if($('#ClientTRDeviceDiv<?php echo $Client->id ?>').is(":hidden"))

    {   

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').show();

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();    

     $( "#ClientActivityInfo" ).empty();     

     ClientPopUpDiv.style.display = "none";     

    }

    else {

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();    

     $( "#ClientActivityInfo" ).empty();     

     ClientPopUpDiv.style.display = "none";     

    }

 }); 

    

    

 $('#ShowClientTRClassInsetedDiv<?php echo $Client->id ?>').click(function() {

     

     if($('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').is(":hidden"))

    {   

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').show();

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').hide();    

     $( "#ClientActivityInfo" ).empty();     

     ClientPopUpDiv.style.display = "none";     

    }

    else {

     $('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRDeviceDiv<?php echo $Client->id ?>').hide();

     $('#ClientTRRemarksDiv<?php echo $Client->id ?>').hide();     

     $( "#ClientActivityInfo" ).empty();     

     ClientPopUpDiv.style.display = "none";     

    }

 });    

    

    

<?php

if ($Client->Status=='10'){

?>

$('#ClientTRDivs<?php echo $Client->id ?>').show();

<?php    

}

else {

?>

$('#ClientTRDivs<?php echo $Client->id ?>').hide();    

<?php   

}    

?>    

    

    



$("#StatusEvent<?php echo $Client->id ?>").change(function () {

var Acts = this.value;    

$.ajax({

type: 'POST',  

data:'Act='+ Acts,

dataType: 'json',    

url:'new/StatusChange.php',     

success: function(data){   

$('#ClientTRDiv_Card<?php echo $Client->id ?>').html(data.Cards);



if (data.WatingList=='True'){    

$('#ClientWatingListText').html(data.WatingListText).show(); 

}

else {

$('#ClientWatingListText').html('').hide();    

}  

    

if (data.ReClass=='True'){    

$('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').show(); 

}

else {

$('#ClientTRClassInsetedDiv<?php echo $Client->id ?>').hide();   

}      

    

//scheduler.load("new/data/deskplan.php");  

}

});

		 

		 

		 

		 

    });    

    

    

});

			

		</script>

    



<?php } ?>

 </tbody>



</table>  



</div>

</div>       





				<div class="ip-modal-footer text-start px-0">

                <div class="ip-actions">

                </div>

                

				<button type="button" class="btn btn-dark text-white ip-close ip-closePopUp ClientClose" data-dismiss="modal"><?php echo lang('close') ?></button>

                </form>

				</div>



<div id="greenPassModalClientList"></div>

<script>

    

    

$(function () {

  $('[data-toggle="tooltip"]').tooltip()

})    

    

$( ".select2ClientDesk" ).select2( {

		theme:"bootstrap", 

		placeholder: "<?php echo lang('search_client') ?>",

		language: "<?php echo isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == "eng" ? 'en' : 'he' ?>",

		allowClear: true,

		width: '100%',

     ajax: {

            url: 'SearchClient.php',

            type: 'POST',

            dataType: 'json',

            cache: true

        },

		minimumInputLength: 3,

        dir: "rtl" } ); 

    



$( ".select2General" ).select2( {

		theme:"bootstrap",   
		allowClear: true,

		width: '100%',

       } );

$(document).on("click",".js-green-pass-icon",function () {
    let clientId = $(this).attr("data-id");
    var data = {
        client_id: clientId,
        fun : "modal"
    }

    $.ajax({
        url: "/office/ajax/covidGreenPass.php",
        type: "post",
        data: data,
        success: function (response) {
            $("#greenPassModalClientList").append(response);
            $("#green_pass_modal").modal('show');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(textStatus);
            console.log(textStatus, errorThrown);
        },
    });
})
// $(document).on("click","#greenPassModalClientList .ip-close",function (){
//     $("#green_pass_modal").remove();
// })



    

    

    

    

$('#AddClientActivity').on('change',function(){



  var ClientId = $(this).children('option:selected').val();  

  var Activity = '<?php echo $ClassInfo->ClassMemberType; ?>';

  var ClassId = '<?php echo $Id; ?>';    

  if ($('#AddClientActivity option:selected').length > 0 ||  ClientId!=null) {

  var urls= 'action/ClientActivity.php?ClientId='+ClientId+'&Activity='+Activity+'&ClassId='+ClassId;

  $('#ClientActivityInfo').load(urls,function(){     

  $('#ClientActivityInfo .ajax-form').on('submit', BeePOS.ajaxForm);

  return false;      

  });

}

else {

 $( "#ClientActivityInfo" ).empty();    

}                               



});    

 

<?php if ($WatingPopUp=='1'){ ?> 

    

$('.ClientClose').click(function() {

var modalcode = $('#RunWatingListClass');   

modalcode.modal('show');     

$('#RunWatingListClassID').val('<?php echo $Id; ?>');       



});   

    

    

$('.ClientCloseNew').click(function() {



var modalcode = $('#RunWatingListClass');   

modalcode.modal('show');     

$('#RunWatingListClassID').val('<?php echo $Id; ?>');    

    

});      

 

<?php } ?>    

    

 $('#RunWatingList').click(function() {

 var ClassId = '<?php echo $Id; ?>';    

     

$.ajax({

type: 'POST',  

data:'ClassId='+ ClassId,

dataType: 'json',    

url:'new/RunWatingList.php',     

success: function(data){    

}

});     

     

  var url = 'new/ClientList.php?Id='+ClassId; 

     $('#DivViewDeskInfo').load(url,function(e){    

     $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);  

      return false;     

      });      



 

 });      

    



    

    

    



    $("#ShowClientPopUpDiv").click(function(){

    $('.HideDiv').hide();    

    if(ClientPopUpDiv.style.display == 'none')

    {

    $(".select2ClientDesk").select2("val", "");   

    ClientPopUpDiv.style.display = "block";   

    }

    else {

    $(".select2ClientDesk").select2("val", "");    

    ClientPopUpDiv.style.display = "none";    

    }

    

    $( "#ClientActivityInfo" ).empty();

        

    }); 

			   

    

</script>

