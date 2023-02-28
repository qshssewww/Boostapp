<?php

require_once '../../app/initcron.php';





$CompanyNum = Auth::user()->CompanyNum;

$ClientId = $_REQUEST['ClientId'];

$Activity = $_REQUEST['Activity'];

$ClassId = $_REQUEST['ClassId'];



$ClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClientId)->first();

$ClassInfo = DB::table('classstudio_date')->where('id','=', $ClassId)->where('CompanyNum', $CompanyNum)->first();



 

$MemberShipClients = DB::select('select * from boostapp.client_activities where (CompanyNum = "'.$CompanyNum.'" AND Department != "4" AND Status = "0" AND FIND_IN_SET("'.$ClientId.'",TrueClientId) > 0 ) OR (CompanyNum = "'.$CompanyNum.'" AND ClientId = "'.$ClientId.'" AND Department != "4" AND Status = "0") Order By `CardNumber` DESC '); 



    

$Disabled = '';   

$i = '1';

$notStarted = false;

if (!empty($MemberShipClients)){

?>    

<form action="AddNewClientPopUp" class="ajax-form clearfix text-right" autocomplete="off"> 

<input type="hidden" name="ClassId" value="<?php echo $ClassId; ?>">    

<input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">

   

<?php    

foreach ($MemberShipClients as $MemberShipClient) {

    $Disabled = '';

    if ($MemberShipClient->TrueClientId=='0'){

    $TrueClientText = '';    

    }

    else {

    $TrueClientText = '<i class="fas fa-user-friends" title="מנוי משפחתי"></i> מנוי משפחתי';    

    }



    $membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $MemberShipClient->MemberShip)->first();  

    if ($MemberShipClient->MemberShip=='BA999'){

    $Type = lang('no_membership_type');    

    } 

    else {

    $Type = $membership_type->Type;     

    }                                                    



    if ($MemberShipClient->Department=='1' && $MemberShipClient->FirstDateStatus=='0') {

    $TokefText = lang('valid_until '). with(new DateTime($MemberShipClient->TrueDate))->format('d/m/Y');

    $BalnaceText = '';

        

    if ($MemberShipClient->TrueDate>=date('Y-m-d')){

    $CheckBoxColor = 'success';    

    }

    else {

    $CheckBoxColor = 'danger';

    $Disabled = 'disabled';     

    }    

        

        

    }

    else if ($MemberShipClient->Department=='2' || $MemberShipClient->Department=='3') {

    $BalnaceText = '<span dir="ltr">'.$MemberShipClient->TrueBalanceValue.'</span> <span dir="rtl">יתרת שיעורים:</span>'; 

    $TokefText = ''; 

        

    if ($MemberShipClient->TrueBalanceValue>='1'){

    $CheckBoxColor = 'success';    

    }

    else {

    $CheckBoxColor = 'danger';

    $Disabled = 'disabled';     

    }      

        

        

    if ($MemberShipClient->TrueDate!='' && $MemberShipClient->FirstDateStatus=='0'){

    $TokefText = lang('valid_until '). with(new DateTime($MemberShipClient->TrueDate))->format('d/m/Y');  

        

    if ($MemberShipClient->TrueDate>=date('Y-m-d')){

    $CheckBoxColor = 'success';    

    }

    else {

    $CheckBoxColor = 'danger';

    $Disabled = 'disabled';    

    }      

        

    }

        

    }

    else {

    $BalnaceText = lang('validity_calculated_notice'); 

    $TokefText = '';     

    $CheckBoxColor = 'success';     

    }
    
    if ($MemberShipClient->StartDate > date('Y-m-d')) {
      $TokefText = lang('validity_date_notice '). with(new DateTime($MemberShipClient->StartDate))->format('d/m/Y');
      $CheckBoxColor = 'danger';
      $notStarted = true;
    }




    if($Disabled != 'disabled'){

?>



<div class="checkbox">

<label style="padding-right:25px;">

<input name="ActivityId" id="ActivityId_<?php echo $i; ?> " type="radio" value="<?php echo $MemberShipClient->id; ?>" class="pull-right" <?php echo $Disabled; ?>>
    <span class="text-<?php echo $CheckBoxColor; ?>"> <?php echo $Type; ?>, #<?php echo $MemberShipClient->CardNumber; ?> - <?php echo $MemberShipClient->ItemText; ?> // </span>
    <span dir="ltr" class="text-<?php echo $CheckBoxColor; ?>"><?php echo $BalnaceText; ?></span><span class="text-<?php echo $CheckBoxColor; ?>"> <?php echo $TokefText; ?></span>
    <?php echo $TrueClientText;?>  </label>
    <br>

</div>


<?php }?>




<?php ++ $i; } ?> 

  

    

<?php if ($ClassInfo->ClassDevice!='0'){ ?>   

 <div class="form-group"> 

 <label><?php echo lang('select_equipment') ?></label>

  <select class="form-control js-example-basic-single text-right select2" name="DeviceId" dir="rtl" data-placeholder="<?php echo lang('select_equipment') ?>" style="width: 100%" required>

  <option value=""><?php echo lang('choose') ?></option>      

  <?php 

  $ClassDeviceNames = DB::table('numberssub')->where('CompanyNum', $CompanyNum)->where('NumbersId', '=', $ClassInfo->ClassDevice)->where('Status', '=', '0')->get();

  foreach ($ClassDeviceNames as $ClassDeviceName) {	

      

  $CheckDevice = DB::table('classstudio_act')->where('CompanyNum', $CompanyNum)->where('ClassId', '=', $ClassId)->where('DeviceId', '=', $ClassDeviceName->id)->where('StatusCount', '=', '0')->count();      



  if (@$CheckDevice!='0'){} else {      

      

  ?>  

  <option value="<?php echo $ClassDeviceName->id; ?>"><?php echo $ClassDeviceName->Name; ?></option>	  

  <?php 

  }	 }

  ?>

      

  <?php if ($ClassInfo->ClientRegister>=$ClassInfo->MaxClient) { ?>      

  <option value="0"><?php echo lang('select_waitlist') ?></option>      

  <?php } ?>      

      

  </select>      

  </div>  

<?php } else { ?>

<input type="hidden" value="0" name="DeviceId">  

<?php } ?>    

<div class="alertb alert-warning"> 

<?php echo lang('select_from_list') ?> <br>   

<?php echo lang('book_subscription_notice') ?><br>

<?php echo lang('subsciption_class_notice') ?>

</div> 



<?php if ($ClientInfo->LastClassDate!=''){ ?>    

<div class="alertb alert-info"> 

<?php echo lang('last_class_date') ?> <?php echo with(new DateTime($ClientInfo->LastClassDate))->format('d/m/Y'); ?>

</div> 

<?php } ?>

    

    

<div class="form-group" dir="rtl">

<button type="submit" name="submit" class="btn btn-dark text-white"><?php echo lang('save_changes_button') ?></button>    

</div>

</form>

<?php } else { ?> 



<?php if (!empty($ClientId)){ ?>

<div class="alertb alert-warning">             

<?php echo lang('customer_without_subscription') ?><br>

<?php echo lang('add_subscription_nortice') ?>

</div> 



<div class="form-group" dir="rtl">

<a  href="ClientProfile.php?u=<?php echo $ClientId; ?>" class="btn btn-dark btn-sm text-white"><?php echo lang('go_to_customer_card') ?></a>    

</div>



<?php } } ?>



