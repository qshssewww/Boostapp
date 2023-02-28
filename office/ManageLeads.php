<?php
require_once '../app/init.php';
redirect_to(__DIR__.'/manage-leads.php');
exit;

$pageTitle = lang('uppercase_pipeline');
require_once '../app/views/headernew.php';

?>
<?php if (Auth::check()):?>
<?php if (Auth::userCan('47')): ?>
<?php
   CreateLogMovement(lang('enter_pipeline'), '0');

        /** @var TYPE_NAME $CompanyNum */
        $Category2 = DB::table('automation')->where('CompanyNum','=', $CompanyNum)->where('Category','=', '2')->where('Type','=', '1')->where('Status','=', '0')->count();

   ?>
<link href="assets/css/fixstyle.css" rel="stylesheet">
<?php
   $CompanyNum = Auth::user()->CompanyNum;
   $MainPipeLine = DB::table('pipeline_category')->where('id', @$_GET['u'])->where('CompanyNum' ,'=', $CompanyNum)->first();
   if (empty($_GET['u']) || !is_object($MainPipeLine)) {

    ErrorPage(
      lang('error_oops_something_went_wrong')
      , lang('error_pipeline_no_longer_exists')
    );
   }
   else {

       $All = @$_GET['All'];
       if (@$All==''){
           $All = 'False';
       }

       $AgentId = @$_GET['AgentId'];
       if (@$AgentId=='') {
           $AgentId = Auth::user()->id;

           $All = 'True';


           if (Auth::userCan('142')) {
               $All = 'True';
           } else {
               $All = 'False';
           }
       }






       $MainPipeId = @$MainPipeLine->id;

       $SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
       $BrandsMain = $SettingsInfo->BrandsMain;

       $PipeAgentView = @$MainPipeLine->PipeAgentView;
       $MaxRecord = @$MainPipeLine->MaxRecord;

       ///הצלחה
       $GetSuccessInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '1')->first();
       /// כשלון
       $GetFailsInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '2')->first();
       /// לא רלוונטי
       $GetNoneFailsInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '3')->first();

       $GetSuccess = $GetSuccessInfo->id;
       $GetFails = $GetFailsInfo->id;
       $GetNoneFails = $GetNoneFailsInfo->id;

       $NewTask = '0';

       if (Auth::userCan('48') || Auth::userCan('51')) {
           $OpenEdit = '';
       } else {
           $OpenEdit = 'disablesortable';
       }

   ?>
<!--<script src="--><?php //echo App::url('CDN/moment.js') ?><!--"></script>-->
<canvas id="SuccessConfetti" style="z-index: 200;display: none;"></canvas>
<style>
   .show_more_main {
   margin: 15px 25px;
   }
   .show_more {
   color: #48AD42;
   border-bottom: 1px solid #e5e5e5;
   font-size: 14px;
   outline: 0;
   }
   .show_more {
   cursor: pointer;
   display: block;
   padding: 10px 0;
   text-align: center;
   font-weight:bold;
   }
   .show_more:active {
   color: #48AD42;
   border-bottom: 1px solid #e5e5e5;
   cursor: pointer;
   display: block;
   padding: 10px 0;
   text-align: center;
   font-weight:bold;
   }
   .loading {
   color: #48AD42;
   border-bottom: 1px solid #e5e5e5;
   font-size: 14px;
   display: block;
   text-align: center;
   padding: 10px 0;
   outline: 0;
   font-weight:bold;
   }
   .loading_txt {
   background-position: left;
   background-repeat: no-repeat;
   border: 0;
   display: inline-block;
   height: 16px;
   padding-left: 20px;
   }
   .SmallDiv {
   max-height: 100px!important;
   }
   .SmallDivs {
   z-index: -1 !important;
   }
   .list-special .list-group-item:first-child {
   border-top-right-radius: 0px !important;
   border-top-left-radius: 0px !important;
   }
   .list-special .list-group-item:last-child {
   border-bottom-right-radius: 0px !important;
   border-bottom-left-radius: 0px !important;
   }
   .cursorcursor:active {cursor: move;
   }
   .cursorcursor li.ui-sortable-helper{
   cursor: move;
   }
   .ui-draggable-dragging{
   /**-ms-transform: rotate(7deg);-webkit-transform: rotate(7deg);=transform: rotate(7deg);**/
   z-index: 999999999;
   background:#F0F0F0;
   border: 1px dashed #525252 !important;
   }
   .hover li {
   -moz-box-shadow:    inset 0 0 10px #000000;
   -webkit-box-shadow: inset 0 0 10px #000000;
   box-shadow:         inset 0 0 10px #000000;
   }

   .bsapp-corner-badge{
      width: 30px;
    height: 30px;
    display: flex;
    justify-content: flex-end;
    position: relative;
    z-index: 99;
    padding-top: 15px;

   }


   .bsapp-corner-badge:before{
    content: "";
    border-width: 30px 30px 0px 0px;
    border-style: solid;
    position: absolute;
    z-index: -1;
    right: -7px;
    left: unset;
    bottom : 0px ;
   }

   [dir="rtl"] .bsapp-corner-badge:before{
      content: "";
    border-width: 0px 30px 30px 0px;
    border-style: solid;
    position: absolute;
    z-index: -1;
    left: -7px;
    right: unset;
   }


</style>
<div class="col-md-12 col-sm-12">
    <a href="javascript:;" class="floating-plus-btn d-flex d-flex bg-primary"
       onclick=NewClient('lead') title="<?= lang('new_lead') ?>">
   <i class="fal fa-plus fa-lg margin-a"></i>
   </a>
   <nav class="d-flex justify-content-between align-items-center py-10 mb-20 shadow bg-light pie-15 rounded">
      <ol class="breadcrumb align-middle bg-transparent  py-5 my-auto">
         <li class="breadcrumb-item align-middle" style="padding-top: 3px;"><a href="index.php" class="text-dark"><?= lang('main') ?></a></li>
         <li class="breadcrumb-item align-middle">
            <select class="form-control-sm mr-8" id="ChoosePipeline" name="ChoosePipeline"  >
               <?php
                  $UserInfos = DB::table('pipeline_category')->where('CompanyNum' ,'=', $CompanyNum)->where('Status' ,'=', '0')->orderBy('Title', 'ASC')->get();
                  foreach ($UserInfos as $UserInfo) {
                  ?>
               <option value="<?php echo $UserInfo->id; ?>" <?php if ($UserInfo->id == $MainPipeId) { echo 'selected'; } else {} ?> ><?= lang('uppercase_pipeline') ?> :: <?php echo $UserInfo->Title; ?></option>
               <?php
                  }
                  ?>
            </select>
         </li>
      </ol>
      <div class="d-flex">
         <a href="#" class="d-flex justify-content-center align-items-center rounded-circle" data-toggle="tooltip" title="<?= lang('tasks_planned_for_today') ?>" style="color : #9ce2a7 !important ;width:16px;height:16px;"><i class="fas fa-circle" ></i></a>
         <a href="#"  class="mis-5 d-flex justify-content-center align-items-center rounded-circle" data-toggle="tooltip" title="<?= lang('expired_tasks') ?>" style="color : #ff8080 !important;width:16px;height:16px;"><i class="fas fa-circle" ></i></a>
         <a href="#" class="mis-5 d-flex justify-content-center align-items-center rounded-circle" style="background:  #fff0b3 !important ;width:16px;height:16px;" data-toggle="tooltip" title="<?= lang('tasks_are_not_defined') ?>">
         <span style="color: #efc15d;  font-size: 10px;">
         <i class="fas fa-exclamation"></i>
         </span>
         </a>
         <a href="#"  class="mis-5 d-flex justify-content-center align-items-center rounded-circle"  data-toggle="tooltip" title="<?= lang('no_tasks_planned_tasks_were_in_past') ?>"  style="color : #abb1bf !important;width:16px;height:16px;" ><i class="fas fa-circle" ></i></a>
         <a href="#" class="mis-5 d-flex justify-content-center align-items-center rounded-circle" data-toggle="tooltip" title="<?= lang('tasks_are_planned_not_expired_yet') ?>" style="color : #40A4C5 !important;width:16px;height:16px;"><i class="fas fa-circle" ></i></a>
      </div>
   </nav>
   <nav  class="d-flex justify-content-between align-items-center py-10 mb-20 shadow bg-light pie-15 rounded" >
      <ol class=" bg-transparent  py-5 my-auto pis-15 ">
         <li class="breadcrumb-item">
            <a class="btn btn-outline-primary" href="/office/LeadsJoinReport.php"><?php echo lang('leads_report') ?> <i class="fal fa-file-alt"></i></a>
         </li>
      </ol>
      <?php if (Auth::userCan('142'))  { ?>
      <div>
         <select class="form-control text-start ChooseAgentForPipeline" id="ChooseAgentForPipeline" name="AgentId"  data-placeholder="<?= lang('choose_taking_care_representative') ?>" style="max-width: 200px;">
            <option value="BA999" <?php if ('True' == $All) { echo 'selected'; } else {} ?> ><?= lang('everyone') ?></option>
            <?php
               $UserInfos = DB::table('users')->where('CompanyNum' ,'=', $CompanyNum)->where('status' ,'=', '1')->orderBy('display_name', 'ASC')->get();
               foreach ($UserInfos as $UserInfo) {
               ?>
            <option value="<?php echo $UserInfo->id; ?>" <?php if ($UserInfo->id == $AgentId && $All=='False') { echo 'selected'; } else {} ?> ><?php echo $UserInfo->display_name; ?></option>
            <?php
               }
               ?>
         </select>
      </div>
      <?php } ?>
      </ol>
   </nav>
   <div class="row d-flex flex-row-reverse" style="padding: 15px;">
      <?php
         $PipeTitles = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Status','=', '0')->orderBy('Sort', 'DESC')->get();
         $i = '1';
         foreach ($PipeTitles as $PipeTitle) {


             if (($PipeAgentView == '0' && @$_GET['AgentId'] == '') || $All=='True') {
                 $count = DB::table('pipeline')
                     ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                     ->where('client.CompanyName', '!=', '')
                     ->where('pipeline.CompanyNum','=', $CompanyNum)
                     ->where('pipeline.PipeId','=', $PipeTitle->id)
                     ->count();
             } else {
                 $count = DB::table('pipeline')
                     ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                     ->where('client.CompanyName', '!=', '')
                     ->where('pipeline.CompanyNum','=', $CompanyNum)
                     ->where('pipeline.PipeId','=', $PipeTitle->id)
                     ->where('pipeline.AgentId','=', $AgentId)
                     ->count();
             }
             $PipeTitle->count = $count;

//             $PipeTitle->count = count((array)$PipeTitle)
           ?>
      <?php  $randnumber = rand(3,16); ?>
      <div class="col-md col-sm-12" style="padding: 0px; border-bottom: 1px solid #e5e5e5;border-right: 1px solid #e5e5e5;<?php if ($i == '1') {echo "border-left: 1px solid #e5e5e5;";} ?>">
         <ul class="list-group list-special sortable uldiv  getbackground<?php echo $PipeTitle->id; ?>"  id="<?php echo $PipeTitle->id; ?>" style="min-height: 300px;height: 100%;">
            <a data-toggle="collapse" href="#DashPipe<?php echo $PipeTitle->id; ?>" aria-expanded="true" aria-controls="DashPipe<?php echo $PipeTitle->id; ?>" class="text-dark" data-placement="bottom" style="text-decoration: none;">
               <li class="unsortable d-flex justify-content-between  list-group-item text-start text-dark padding-0 bg-light" style="padding:15px; border-bottom: 1px solid #e5e5e5;border-top: 1px solid #e5e5e5;" >
                  <div>
                     <strong><?php echo $PipeTitle->Title; ?></strong>
                  </div>
                  <span class="text-secondary" style="font-size: 15px;">
                     <strong>
                     <span id="DashPipeCount<?php echo $PipeTitle->id; ?>" class="mie-5"></span>
                       <i class="fas fa-angle-double-right fa-lg " style="color: lightgray;"></i>
                     </strong>
                  </span>
               </li>
            </a>
            <div class="collapse show" id="DashPipe<?php echo $PipeTitle->id; ?>">
               <li class="item list-group-item text-start text-dark padding-0 cursorcursor lidiv" style="display: none;"></li>
               <?php
                  $ColorRed = '';
                  $DataPopUp = '';
                  $Limit = $MaxRecord && $MaxRecord < 30 ? $MaxRecord : 30;
                     if (($PipeAgentView == '0' && @$_GET['AgentId'] == '') || (@$_GET['AgentId'] == '' && $All == 'True')) {
                         $PipeLines = DB::table('pipeline')
                             ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                             ->select('pipeline.*', 'client.CompanyName', 'client.ContactMobile', 'client.Email', 'client.Dob', 'client.Gender')
                             ->where('client.CompanyName', '!=', '')
                             ->where('pipeline.CompanyNum','=', $CompanyNum)
                             ->where('pipeline.PipeId','=', $PipeTitle->id)
                             ->orderBy('pipeline.TaskStatus', 'ASC')->orderBy('pipeline.NoteDates', 'ASC')->orderBy('pipeline.Dates', 'ASC')
                             ->limit($Limit)
                             ->get();
                     } else {
                         $PipeLines = DB::table('pipeline')
                             ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                             ->select('pipeline.*', 'client.CompanyName', 'client.ContactMobile', 'client.Email', 'client.Dob', 'client.Gender')
                             ->where('client.CompanyName', '!=', '')
                             ->where('pipeline.CompanyNum','=', $CompanyNum)
                             ->where('pipeline.AgentId','=', $AgentId)
                             ->where('pipeline.PipeId','=', $PipeTitle->id)
                             ->orderBy('TaskStatus', 'ASC')->orderBy('NoteDates', 'ASC')->orderBy('Dates', 'ASC')
                             ->limit($Limit)
                             ->get();
                     }

                     foreach ($PipeLines as $PipeLine) {


                  $ContactInfo = $PipeLine->ContactInfo;
                  $ClientId = $PipeLine->ClientId;
                         if(!empty($PipeLine->CompanyName)){
                             $CompanyName = $PipeLine->CompanyName;
                             $ContactMobile = $PipeLine->ContactMobile;
                             $Email = $PipeLine->Email;
                             $Dob = $PipeLine->Dob;
                             $Gender = $PipeLine->Gender;
                     }else{
                     continue;
                     $CompanyName = lang('error_no_info');
                     $ContactMobile = '';
                     $Email = '';
                     $Dob = '';
                     $Gender = '0';
                     }

                     if ($ContactMobile!=''){
                     $ContactInfo = $ContactMobile;
                     }
                     else if ($ContactMobile=='' && $Email!=''){
                     $ContactInfo = $Email;
                     }
                     else {
                     $ContactInfo = $PipeLine->ContactInfo;
                     }


                     if ($Gender=='1'){
                     $GenderIcon = '<i class="fas fa-mars" data-toggle="tooltip" title="' . lang('male') .'"></i>';
                     }
                     else if ($Gender=='2'){
                     $GenderIcon = '<i class="fas fa-venus" data-toggle="tooltip" title="' . lang('female') .'"></i>';
                     }
                     else {
                     $GenderIcon = '';
                     }

                     if (@$Dob=='' || @$Dob=='0000-00-00'){ $NewAge = '';  }else {
                     $from = new DateTime($Dob);
                     $to   = new DateTime('today');
                     $NewAge =  $from->diff($to)->y.'.'.$from->diff($to)->m;
                     }

                     $CheckMemberShip = DB::table('client_activities')->where('CompanyNum','=', $CompanyNum)->where('ClientId','=', $ClientId)->where('Department','=', '3')->count();
                     $CheckNotes = DB::table('clientcrm')->where('CompanyNum','=', $CompanyNum)->where('ClientId','=', $ClientId)->count();

                     $ClassActColor = 'text-secondary';
                     $ClassActText = lang('trial_lesson');

                     $CheckClass = DB::table('classstudio_act')->where('CompanyNum','=', $CompanyNum)->where('ClientId','=', $ClientId)->where('TestClass','=', '2')->orderBy('TestClassStatus','ASC')->orderBy('ClassDate','DESC')->first();

                     if (@$CheckClass->Status=='2'){
                     $ClassActColor = 'text-primary';
                     $ClassActText = lang('arrived_to_lesson');
                     }
                     else if (@$CheckClass->Status=='7' || @$CheckClass->Status=='8'){
                     $ClassActColor = 'text-danger';
                     $ClassActText = lang('not_arrived_to_lesson');
                     }
                     else if (@$CheckClass->Status=='3' || @$CheckClass->Status=='4' || @$CheckClass->Status=='5'){
                     $ClassActColor = 'text-danger';
                     $ClassActText = lang('canceled_lesson');
                     }
                     else {
                     $ClassActColor = 'text-secondary';
                     $ClassActText = lang('trial_lesson');
                     }


                  $PipeId = $PipeLine->id;

                     $PipeLineColor = $PipeLine->StatusColor;



                     if ($PipeLine->Tasks==''){

                  $DataPopUp = '';

                  } else {

                  $Loops =  json_decode($PipeLine->Tasks,true);
                     foreach($Loops['data'] as $key=>$val){

                     if ($val['Date'] < date('Y-m-d') || ($val['Date'] == date('Y-m-d') && $val['Time'] < date('H:i:s'))){
                  $ColorRed = '#ff8080';
                  $textColor = 'text-danger';
                  }

                  else {
                  $textColor = 'text-success';
                  }


                  $DataPopUp .= "<div class='row ".$textColor."'>
                          <a class='col-12 js-new-task' data-task='".$val['Id']."' data-client='".$PipeLine->ClientId."' data-pipe-id='".$PipeLine->id."' style='cursor: pointer;'><i class='".$val['Icon']." fa-xs'></i> ".$val['Title']."<br>
                          <i class='fas fa-calendar-alt fa-xs'></i> <span style='font-size: 11px;'>".date('H:i', strtotime($val['Time']))." ".date('d/m/Y', strtotime($val['Date']))."</span>
                          </a>
                          </div>
                          <hr>";

                  }


                     $PipeLineColor = $PipeLineColor;

                  }

                     ?>
               <li class="item list-group-item text-start text-dark padding-0 cursorcursor <?php echo $OpenEdit; ?> pb-0" style="padding:5px; border-bottom: 1px solid #e5e5e5; pointer-events: stroke;" id="<?php echo $PipeLine->id; ?>" data-sort="<?php echo $PipeLine->TaskStatus; ?>,<?php echo $PipeLine->NoteDates; ?>" data-id="<?php echo $PipeLine->id; ?>" data-clientid="<?php echo $PipeLine->ClientId; ?>">
                  <span   class="text-secondary"> <?php echo $GenderIcon; ?> <?php echo $NewAge; ?></span>
                  <a href="ClientProfile.php?u=<?php echo $ClientId; ?>" style="font-size: 15px;" style="text-decoration: none;" class="text-dark disablesortable">
                  <?php echo $CompanyName; ?>
                  </a>
                  <div class="d-flex justify-content-between" style="color: #AEAEAE;">
                     <span style="font-size: 13px;" class="disablesortable unicode-plaintext">
                     <?php
                        if (!ctype_digit($ContactInfo)) {
                        ?>
                     <?php echo $ContactInfo; ?>
                     <?php
                        } else {
                        ?>
                     <a href="javascript:void(0);" <?php if ($SettingsInfo->VoiceCenterToken!='') {?>OnClick="CallToClient(<?php echo $ClientId; ?>,<?php echo $PipeLine->id; ?>);"<?php }?> style="color: #AEAEAE;" >
                     <?php echo $ContactInfo; ?>
                     <span class="CallClientdivb<?php echo $PipeLine->id; ?>" style="display: none;">
                     <i class="fas fa-spinner fa-spin text-warning"></i>
                     </span>
                     <span class="CallClientdiv<?php echo $PipeLine->id; ?>" style="display: none;">
                     <span class="fa-layers fa-fw text-success">
                     <i class="fas fa-circle"></i>
                     <i class="fa-inverse fas fa-phone" data-fa-transform="shrink-6"></i>
                     </span>
                     </span>
                     </a>
                     <?php } ?>
                     </span>
                     <span class="text-secondary disablesortable"> <a href='javascript:PipeMore(<?php echo $PipeLine->id; ?>,<?php echo $PipeLine->MainPipeId; ?>,<?php echo $PipeLine->PipeId; ?>,<?php echo $PipeLine->ClientId; ?>);'  class="text-secondary text-start disablesortable">  <i class="fas fa-info-circle disablesortable" data-toggle="tooltip" title="<?= lang('more_details') ?>"></i></a> <?php if ($CheckNotes>='1'){ ?><a href='javascript:PipeAddNote(<?php echo $PipeLine->id; ?>,<?php echo $PipeLine->ClientId; ?>);' class='text-secondary'><i class="fas fa-sticky-note disablesortable" data-toggle="tooltip" title="<?= lang('notes') ?>"></i></a><?php } ?> <?php if (@$CheckClass->id!=''){ ?><a href='javascript:PipeAddCalss(<?php echo $PipeLine->id; ?>,<?php echo $PipeLine->ClientId; ?>);' class='<?php echo $ClassActColor; ?>' ><i class="fas fa-calendar-check disablesortable <?php echo $ClassActColor; ?>" data-toggle="tooltip" title="<?php echo $ClassActText; ?>"></i></a><?php } ?> </span>
                  </div>
                  <div class="d-flex justify-content-between" style="color: #AEAEAE;">
                     <span style="font-size: 13px;" class="disablesortable" >
                     <a href="javascript:PipeAction('<?php echo $PipeLine->id; ?>','<?php echo $PipeLine->MainPipeId; ?>','<?php echo $PipeLine->PipeId; ?>','<?php echo $PipeLine->ClientId; ?>')"  class="text-start disablesortable">
                     <span class="text-secondary"><i class="fas fa-bars"></i>  <?= lang('actions') ?>
                     </span></a>
                     </span>

                     <style>
                     .DivScroll::-webkit-scrollbar {
                     width: 2px;
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
                  <a href="javascript:void(0);"  class="btnPopover text-start disablesortable" rel="popover" data-toggle="popover" data-html="true"   data-content="<div  style='width: 250px; padding-top: 5px; padding-bottom: 5px; padding-right: 5px;'>
                     <div class='DivScroll text-dark' style='max-height:220px; overflow-y:scroll; overflow-x:hidden;margin: 0px; padding: 0px; '>
                     <?php echo $DataPopUp; ?>
                     </div>
                     <div style='text-align: center;' align='center'>
                     <?php if (Auth::userCan('48')): ?>
                     <a  data-task='<?php echo $NewTask; ?>' data-client='<?php echo $PipeLine->ClientId; ?>'  data-pipe-id='<?php echo $PipeLine->id; ?>' class='text-dark js-new-task'><?= lang('new_task') ?></a>
                     <?php endif; ?>
                     </div>
                     </div>">

                     <style type="text/css">
                        .bsapp-corner-badge.badge-color-<?php echo $PipeLine->id ?>:before {
                           border-color : transparent   <?php echo $PipeLineColor; ?>  transparent  transparent;
                        }
                        [dir="rtl"] .bsapp-corner-badge.badge-color-<?php echo $PipeLine->id ?>:before{
                           border-color : transparent  transparent  <?php echo $PipeLineColor; ?> transparent;
                        }
                     </style>
                     <div class="bsapp-corner-badge badge-color-<?php echo $PipeLine->id ?>">
                        <?php if($PipeLine->TaskStatus == '2'): ?>
                            <i class="fas fa-exclamation bsapp-fs-10" style="color: #AEAEAE;"></i>
                        <?php endif; ?>
                     </div>
                  </a>
                  </div>

               </li>
               <?php
                  $DataPopUp = '';
                  $ColorRed = '';

                  } ?>
            </div>

            <span id="loadmore<?php echo $PipeTitle->id ?>" data-ajax="<?php echo $PipeTitle->id ?>" class="show_more" title="<?= lang('load_more') ?>..." > <?= lang('load_more') ?> <i class="fas fa-caret-down"></i></span>
            <span id="loading<?php echo $PipeTitle->id ?>" class="loading" style="display: none;"><span class="loading_txt" > <?= lang('loading')?> <i class="fas fa-spinner fa-pulse"></i></span></span>
            <?php
            if (($PipeAgentView == '0' && @$_GET['AgentId'] == '') || $All=='True') {
                $count = DB::table('pipeline')
                    ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                    ->where('client.CompanyName', '!=', '')
                    ->where('pipeline.CompanyNum','=', $CompanyNum)
                    ->where('pipeline.PipeId','=', $PipeTitle->id)
                    ->count();
            } else {
                $count = DB::table('pipeline')
                    ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                    ->where('client.CompanyName', '!=', '')
                    ->where('pipeline.CompanyNum','=', $CompanyNum)
                    ->where('pipeline.PipeId','=', $PipeTitle->id)
                    ->where('pipeline.AgentId','=', $AgentId)
                    ->count();
            }
            ?>
             <script type="text/javascript">
                 var countShow = $('#DashPipe<?php echo $PipeTitle->id ?> li').length-1;
                 var count = <?php echo $PipeTitle->count ?? 0 ?>;
                 if(count <= countShow || countShow == 0) {
                   $('#loadmore<?php echo $PipeTitle->id; ?>').hide();
               }
            </script>
         </ul>
      </div>
      <?php ++$i;  } ?>
   </div>
   <script type="text/javascript">
       let popover_placement = 'right';
       $(document).ready(function(){

           var myDefaultWhiteList = $.fn.tooltip.Constructor.Default.whiteList;
           myDefaultWhiteList.a = ['data-client','data-pipe-id','data-task']


            if($("html").attr("dir") == 'rtl'){
               popover_placement = 'left';
            }
           $("body").on("click",".js-new-task",function(){
               var new_task = $(this).attr("data-task");
               var client_id = $(this).attr("data-client");
               var pipe_id = $(this).attr("data-pipe-id");
              NewCal(new_task , client_id , pipe_id);
           });
          $(document).on('click','.show_more',function(){
              var ID = $(this).data('ajax');
          var count = $('#DashPipe' + ID + ' li').length-1;
          $('#loadmore'+ID).hide();
              $('#loading'+ID).show();
              //////5555
              $.ajax({
                  type:'POST',
                  url:'MoreLeades.php',
                  <?php if (($PipeAgentView == '0' && @$_GET['AgentId'] == '') || $All=='True') { ?>
                  data : { id : ID, count : count, PipeId : '<?php echo $MainPipeId; ?>', All : '<?php echo $All; ?>'},
                  <?php } else { ?>
                  data : { id : ID, count : count, PipeId : '<?php echo $MainPipeId; ?>', AgentId : '<?php echo $AgentId; ?>' },
                  <?php } ?>
                  success:function(html){
              $('#loading'+ID).hide();
              $('#DashPipe'+ID).html("");
              $('#DashPipe'+ID).append('<li class="item list-group-item text-start text-dark padding-0 cursorcursor lidiv" style="display: none;"></li>' + html);
              $('.sortable').trigger('sortupdate');
              $('.btnPopover').click(function(e){
                e.stopPropagation();
                $(this).popover({
                  html: true,
                  trigger: 'manual',
                  placement: popover_placement
                }).popover('toggle');
                $('.btnPopover').not(this).popover('hide');
              });




              if (localStorage.getItem("limitflag") == "0")
              {
                $('#loadmore'+ID).show();
              }
              localStorage.removeItem('limitflag');
              var count = $('#DashPipe<?php echo $PipeTitle->id ?> li').length-1;
                      if (count==''){
                      count ='1';
                      }
              $(window).scrollTop($('#DashPipe'+ID+' li:nth-last-child('+count+')').offset().top);
                  }
              });
          });
      });
   </script>
</div>
<div class="wonlosediv bg-light sortable" style="display: none; width: 100%; height: 95px; position: fixed; left:0; bottom: 0;margin: 0px;padding: 0px; border-top: 10px solid #000000; z-index: 100;" id="<?php echo Auth::user()->CompanyNum; ?>100">
   <center>
      <div style="display:inline-block;margin-top: 10px;text-align: center;align-content: center; max-width: 30%;" class="text-white sortable wonlosedivbg<?php echo $GetNoneFails; ?>" id="<?php echo $GetNoneFails; ?>">
         <ul style="list-style-type: none;" class="list-group list-special">
            <li class="unsortable bg-secondary" style="padding: 20px;width: 300px; max-width: 100%;" ><i class="fas fa-trash-alt fa-fw"></i> <?= lang('not_relevant') ?></li>
         </ul>
      </div>
      <div style="display:inline-block;margin-top: 10px;text-align: center;align-content: center; max-width: 30%;" class="text-white sortable wonlosedivbg<?php echo $GetFails; ?>" id="<?php echo $GetFails; ?>">
         <ul style="list-style-type: none;" class="list-group list-special">
            <li class="unsortable bg-danger" style="padding: 20px;width: 300px; max-width: 100%;" ><i class="fas fa-times fa-fw"></i> <?= lang('failure') ?></li>
         </ul>
      </div>
      <div style="display:inline-block;margin-top: 10px;text-align: center;align-content: center; max-width: 30%;" class="text-white sortable wonlosedivbg<?php echo $GetSuccess; ?>" id="<?php echo $GetSuccess; ?>">
         <ul style="list-style-type: none;" class="list-group list-special">
            <li class="unsortable bg-success" style=" padding: 20px;width: 300px; max-width: 100%;" ><i class="fas fa-trophy fa-fw"></i> <?= lang('success') ?></li>
         </ul>
      </div>
   </center>
</div>
<div class="ip-modal text-start"  role="dialog" id="PipLinePopUp" data-backdrop="static" data-keyboard="false" aria-hidden="true">
   <div class="ip-modal-dialog BigDialog">
      <div class="ip-modal-content">
         <div class="ip-modal-header d-flex justify-content-between">
            <h4 class="ip-modal-title"></h4>
            <a class="ip-close" title="Close"   data-dismiss="modal" aria-label="Close">&times;</a>
         </div>
         <div class="ip-modal-body">
            <div id="DivPipLinePopUp"></div>
         </div>
      </div>
   </div>
</div>

<!-- מודל פעולות חדש -->
<div class="ip-modal text-start" role="dialog" id="PipeActionPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
   <div class="ip-modal-dialog BigDialog">
      <div class="ip-modal-content">
         <div class="ip-modal-header d-flex justify-content-between">
            <h4 class="ip-modal-title" id="PipeActionPopupTitle"></h4>
            <a class="ip-close ClassClosePopUp" title="Close"  data-dismiss="modal" aria-label="Close">&times;</a>
         </div>
         <div class="ip-modal-body">
            <div id="ResultPipeline">
               <center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- מודל פעולות חדש -->

<!-- מודל פעולות חדש -->
<div class="ip-modal text-start" role="dialog" id="MoveLeadProfilePopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
   <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
      <div class="ip-modal-content">
         <div class="ip-modal-header d-flex justify-content-between"  <?php //_e('main.rtl') ?>>
            <!--                <a class="ip-close ClassClosePopUp" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>-->
            <h4 class="ip-modal-title"><?= lang('redirect_to_client_card') ?></h4>
         </div>
         <form action="MoveLeadProfile"  class="ajax-form clearfix" autocomplete="off">
            <input type="hidden" name="ItemId" id="MoveLeadProfileId" value="">
            <div class="ip-modal-footer d-flex justify-content-between">
               <button type="submit" name="submit" class="btn btn-primary"><?= lang('yes') ?></button>
               <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?= lang('no') ?></a>
            </div>
         </form>
      </div>
   </div>
</div>
       <?php include_once __DIR__.'/partials-views/archive-popup/clientFailReasonPopup.php'; ?>
<link href="assets/css/fixstyle.css?<?php echo date('YmdHis') ?>" rel="stylesheet">
<script type="text/javascript" charset="utf-8">
   $('#ChooseAgentForPipeline').on('change', function() {
   var Id = this.value;
   if (Id=='BA999'){
   window.location.href = "ManageLeads.php?u=<?php echo $MainPipeId; ?>&All=True";
   }
   else {
   window.location.href = "ManageLeads.php?u=<?php echo $MainPipeId; ?>&AgentId="+Id;
   }

   });



   $('#ChoosePipeline').on('change', function() {
   var Id = this.value;
   var AgentId = $('#ChooseAgentForPipeline').val();
   window.location.href = "ManageLeads.php?u="+Id;

   });


   $(document).ready(function() {
   $('#PipeLineSelect').trigger('change');
   });


   $('#PipeLineSelect').on('change', function() {
   var Id = this.value;

    $('#StatusSelect option')
           .hide() // hide all
           .filter('[data-ajax="'+$(this).val()+'"]') // filter options with required value
           .show(); // and show them

    $('#StatusSelect').val('');
   });




    function PipeAction(PipeLineId,MainPipeId,PipeId,ClientId) {


       var PipeLineId =  PipeLineId;
       var MainPipeId =  MainPipeId;
       var PipeId =  PipeId;
       var ClientId = ClientId;


       $( "#ResultPipeline" ).empty();
       var modalcode = $('#PipeActionPopup');
       $('#PipeActionPopupTitle').html('<?= lang('actions') ?>');
       modalcode.modal('show');

        var url = 'new/PipeLine_Action.php?Id='+PipeLineId+'&ClientId='+ClientId;

       $('#ResultPipeline').load(url,function(e){
       $('#ResultPipeline .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });


    }


    function PipeMore(PipeLineId,MainPipeId,PipeId,ClientId) {


       var PipeLineId =  PipeLineId;
       var MainPipeId =  MainPipeId;
       var PipeId =  PipeId;
       var ClientId = ClientId;


       $( "#ResultPipeline" ).empty();
       var modalcode = $('#PipeActionPopup');
       $('#PipeActionPopupTitle').html('<?= lang('more_details') ?>');
       modalcode.modal('show');
       var url = 'new/PipeLine_More.php?Id='+PipeLineId+'&ClientId='+ClientId;

       $('#ResultPipeline').load(url,function(e){
       $('#ResultPipeline .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });

      }



       function PipeAddNote(PipeLineId,ClientId) {


       var PipeId =  PipeLineId;
       var ClientId = ClientId;


       $( "#DivPipLinePopUp" ).empty();
       var modalcode = $('#PipLinePopUp');
       $('#PipLinePopUp .ip-modal-title').html('<?= lang('manage_notes') ?>');

       modalcode.modal('show');
       var url = 'new/PipeLine_AddNote.php?Id='+PipeId+'&ClientId='+ClientId;


       $('#DivPipLinePopUp').load(url,function(e){
       $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });

      }


       function PipeSendSMS(PipeLineId,ClientId) {


       var PipeId =  PipeLineId;
       var ClientId = ClientId;


       $( "#DivPipLinePopUp" ).empty();
       var modalcode = $('#PipLinePopUp');
       $('#PipLinePopUp .ip-modal-title').html('<?= lang('send_message') ?>');

       modalcode.modal('show');
       var url = 'new/PipeLine_SendMessage.php?Id='+PipeId+'&ClientId='+ClientId;


       $('#DivPipLinePopUp').load(url,function(e){
       $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });

      }



       function PipeAddMemberShip(PipeLineId,ClientId) {


       var PipeId =  PipeLineId;
       var ClientId = ClientId;


       $( "#DivPipLinePopUp" ).empty();
       var modalcode = $('#PipLinePopUp');
       $('#PipLinePopUp .ip-modal-title').html('<?= lang('define_trial_membership') ?>');

       modalcode.modal('show');
       var url = 'new/PipeLine_AddMemberShip.php?Id='+PipeId+'&ClientId='+ClientId;


       $('#DivPipLinePopUp').load(url,function(e){
       $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });

      }


        function PipeAddCalss(PipeLineId,ClientId) {


       var PipeId =  PipeLineId;
       var ClientId = ClientId;

       $( "#DivPipLinePopUp" ).empty();
       var modalcode = $('#PipLinePopUp');
       $('#PipLinePopUp .ip-modal-title').html('<?= lang('set_trial_lesson') ?>');

       modalcode.modal('show');
       var url = 'new/PipeLine_AddClass.php?Id='+PipeId+'&ClientId='+ClientId;


       $('#DivPipLinePopUp').load(url,function(e){
       $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });

      }


        function PipeSendForm(PipeLineId,ClientId) {

       var PipeId =  PipeLineId;
       var ClientId = ClientId;

       $( "#DivPipLinePopUp" ).empty();
       var modalcode = $('#PipLinePopUp');
       $('#PipLinePopUp .ip-modal-title').html('<?= lang('send_joining_form') ?>');

       modalcode.modal('show');
       var url = 'new/PipeLine_SendForm.php?Id='+PipeId+'&ClientId='+ClientId;


       $('#DivPipLinePopUp').load(url,function(e){
       $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });

      }


          function PipeSendMedicalForm(PipeLineId,ClientId) {

       var PipeId =  PipeLineId;
       var ClientId = ClientId;

       $( "#DivPipLinePopUp" ).empty();
       var modalcode = $('#PipLinePopUp');
       $('#PipLinePopUp .ip-modal-title').html('<?= lang('send_health_declaration_form') ?>');

       modalcode.modal('show');
       var url = 'new/PipeLine_SendMedicalForm.php?Id='+PipeId+'&ClientId='+ClientId;


       $('#DivPipLinePopUp').load(url,function(e){
       $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
       return false;

       });

      }



   $(".select2multipleDeskClass").select2({
       theme: "bootstrap",
       placeholder: lang('select'),
       language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
       dir: $("html").attr("dir"),
       width: "100%"
   });

   $(".ChangeLeadAgentp").select2({
       theme: "bootstrap",
       placeholder: lang('choose_representative'),
       language: $("html").attr("dir") == 'rtl' ? 'he' : 'en',
       dir: $("html").attr("dir")
   });


   $('#ClassTypeClass').on('select2:select', function (e) {
   var selected = $(this).val();

     if(selected != null)
     {
       if(selected.indexOf('BA999')>=0){
         $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?= lang('choose_class_type') ?>"} );
       }
     }

   });


   $('#BrandsTypeClass').on('select2:select', function (e) {
   var selected = $(this).val();

     if(selected != null)
     {
       if(selected.indexOf('BA999')>=0){
         $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?= lang('choose_branch') ?>"} );
       }
     }

   });


   $('#TypeOption_0').trigger('click');

</script>
<script>
   var confettiSettings = { target: 'SuccessConfetti', max: '500' };
   var confetti = new ConfettiGenerator(confettiSettings);
   confetti.render();

   $(document).ready(function() {
       $(".ip-close").click(function(){
       $('#FormCalendarClient').trigger("reset");
      // scheduler.clearAll();
     //  scheduler.setCurrentView(<?php echo date('Y-m-d') ?>);
     //  scheduler.load("new/data/events.php");
       $('#CalTaskStatus').prop('disabled', true);
       });

       $('#minor_checkbox').on('click', function() {
         if ($(this).is(":checked")) {
            $("#minor-lead-div").show();
            $('#minor-lead-div').height(200);
            $("#minor_firstName").prop('required', true);
            $("#minor_lastName").prop('required', true);
            $("#minor_lastName").val($('#lead_LastName').val());

         } else {
            $("#minor_firstName").prop('required', false);
            $("#minor_lastName").prop('required', false);
            $("#minor_lastName").val();
            $('#minor-lead-div').height(0);
            setTimeout(() => {
                $("#minor-lead-div").hide();
            }, 200);
        }
      });
   });

         //CallButton
       function CallToClient( ClientID, PipelineId )
       {
            $(".CallClientdivb"+PipelineId).show();
       var callspinner = $.notify(
         {
         icon: 'fas fa-spinner fa-spin',
         message: '<?= lang('try_calling_client_few_moments') ?>',
         },{
         type: 'warning',
       });

       $.ajax({
              type: "POST",
              url: "POS3/CallClient.php?u="+ClientID,
              success: function(dataN)
              {
      callspinner.close();
       $.notify(
         {
         icon: 'fas fa-phone',
         message: '<?= lang('call_is_being_made_pleasant_call') ?>',
         },{
         type: 'success',
       });
                 $(".CallClientdivb"+PipelineId).hide();
          $(".CallClientdiv"+PipelineId).show();
          setTimeout(function() {$(".CallClientdiv"+PipelineId).hide( "bounce", { times: 3 }, "slow" )}, 10000);
              }
            });
       }
         //END CallButton




     $('body').on('click', function (e) {
       //did not click a popover toggle or popover
       if ($(e.target).data('toggle') !== 'popover'
           && $(e.target).parents('.popover.in').length === 0) {
           $('[data-toggle="popover"]').popover('hide');
       }
   });


     $('.sortable').on('sortupdate',function(){

           <?php
      $PipeTitles = DB::table('leadstatus')->where('Status','=', '0')->orderBy('Sort', 'DESC')->get();
      foreach ($PipeTitles as $PipeTitle) { ?>

    $('#DashPipe<?php echo $PipeTitle->id; ?>').each(function(){
       $(this).html($(this).children('li').sort(function(a, b){
      if (($(a).data('sort')) == ($(b).data('sort')))
      {
        // score is the same, sort by endgame
        if (($(a).data('id')) > ($(b).data('id'))) return 1;
      }
        // sort the higher score first:
        return ($(a).data('sort')) > ($(b).data('sort')) ? 1 : -1;
       }));
   });
   <?php } ?>


   });
   $('.sortable').trigger('sortupdate');


    $(function() {
       var count = 0;
       $( ".sortable" ).sortable({
       items: "li:not(.unsortable)",
       dropOnEmpty: true,
       opacity: 0.5,
    zIndex: 999,
    scroll: false,
    cancel: '.disablesortable',
       connectWith: ".sortable",
    start:  function( event, ui ) {
    StartId = this.id;
    ui.item.addClass( "ui-draggable-dragging" );
      $(".wonlosediv").show();
       $('.sortable').sortable('refresh');
   //    $(".sortable").sortable('disable');
      $(function() {
       $('.btnPopover').click(function(e){
    e.stopPropagation();
       $(this).popover({
           html: true,
           trigger: 'manual',
            placement: popover_placement
       }).popover('toggle');
           $('.btnPopover').not(this).popover('hide');
       });

   });



    },

        receive: function(event, ui) {
          if (this.id=='<?php echo Auth::user()->CompanyNum; ?>100'){
          ui.sender.sortable("cancel");
          }
         else if (this.id=='<?php echo $GetSuccess; ?>' || this.id=='<?php echo $GetFails; ?>' || this.id=='<?php echo $GetNoneFails; ?>'){
         ui.item.remove();
         }


           },


       over: function(event, ui) {

        if (this.id=='<?php echo Auth::user()->CompanyNum; ?>100' || this.id=='<?php echo $GetSuccess; ?>' || this.id=='<?php echo $GetFails; ?>' || this.id=='<?php echo $GetNoneFails; ?>'){
        $('.uldiv').removeClass('sortable');
        $('.lidiv').removeClass('item');
        $('.uldiv').addClass('SmallDiv');
        $(".sortable").sortable('disable');
   //     $('.uldiv').css('z-index', '-1');


        }

        else {
        $('.uldiv').addClass('sortable');
        $('.lidiv').addClass('item');
        $('.uldiv').removeClass('SmallDiv');
        $(".sortable").sortable('enable');
        }


      $('.uldiv').removeClass( "bg-light" );
           $('.getbackground'+this.id).addClass( "bg-light" );
      $('.wonlosedivbg<?php echo $GetSuccess; ?>').removeClass( "hover" );
      $('.wonlosedivbg<?php echo $GetFails; ?>').removeClass( "hover" );
           $('.wonlosedivbg<?php echo $GetNoneFails; ?>').removeClass( "hover" );
           $('.wonlosedivbg'+this.id).addClass( "hover" );
       },
   update:
        function( event, ui ) {

     $('.popover').popover('hide'); /// הסתר פרטים
     $('.uldiv').addClass('sortable');
        $('.lidiv').addClass('item');
        $('.uldiv').removeClass('SmallDiv');
        $(".sortable").sortable('enable');


          <?php
      $PipeTitles = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('PipeId','=', $MainPipeId)->where('Status','=', '0')->orderBy('Sort', 'DESC')->get();
      foreach ($PipeTitles as $PipeTitle) { ?>


    $('#DashPipe<?php echo $PipeTitle->id; ?>').each(function(){
       $(this).html($(this).children('li').sort(function(a, b){
      if (($(a).data('sort')) == ($(b).data('sort')))
      {
        // score is the same, sort by endgame
        if (($(a).data('id')) > ($(b).data('id'))) return 1;
      }
        // sort the higher score first:
        return ($(a).data('sort')) > ($(b).data('sort')) ? 1 : -1;
       }));
   });



   <?php } ?>


     //  console.info('לאן עבר: '+this.id);
    //   console.info('מהיכן עבר: '+ StartId);
   //    console.info('איזה ליד: '+ui.item[0].id);

        if (this.id!=StartId) {
            switch (this.id) {
                case '<?= $GetFails ?>':
                    archivePopupVars.newStatus = 1;
                    break;
                case '<?= $GetSuccess ?>':
                    archivePopupVars.newStatus = 0;
                    break;
                default:
                    archivePopupVars.newStatus = 2;
                    break;
            }

            const submitBtn = archivePopupVars.popUp.find('#submitReason');
            if (archivePopupVars.newStatus == 1) {
                if (!submitBtn.length){
                    archivePopupVars.requestType = 0;
                    archivePopupVars.pipeId = this.id;
                    archivePopupVars.leadId = ui.item[0].id;

                    CreateFailReasonPopupButtons(true);
                }
                archivePopupVars.popUp.modal('show');
            } else {
                $.ajax({
                    url: "action/UpdatePipeNew.php?PipeId=" + this.id + "&LeadId=" + ui.item[0].id,
                    error: function () {
                        alert('<?= lang('error_oops_something_went_wrong'); ?>');
                    },
                    success: function () {

                    },
                    complete: function() {

                    },
                });
            }
        }

       if (this.id=='<?php echo $GetSuccess; ?>'){
   $.notify({
    icon: 'fas fa-trophy',
    message: '<?= lang('well_done_keep_up_the_good_work') ?>',
   },{
    type: 'success',
   });

   $("#SuccessConfetti").show();
   setTimeout(function(){ $("#SuccessConfetti").fadeOut("slow");}, 2000 );

   <?php if (Auth::user()->id=='1') { ?>
   var modalcode = $('#MoveLeadProfilePopup');
   modalcode.modal('show');
   $('#MoveLeadProfileId').val(ui.item[0].id);
   <?php } ?>


   }


   if (this.id=='<?php echo $GetFails; ?>'){
   $.notify({
    icon: 'fas fa-times',
    message: "<?= lang('too_bad_moving_to_new_lead') ?>",
   },{
    type: 'danger',
       z_index: 999999,
   });
   var modalcode = $('#PipeReasonsPopup');
   modalcode.modal('show');
   $('#ReasonsItemId').val(ui.item[0].id);

   }


   if (this.id=='<?php echo $GetNoneFails; ?>'){
   $.notify({
    icon: 'fas fa-trash-alt',
    message: "<?= lang('lead_moved_to_trash') ?>",
   },{
    type: 'secondary',
   });
   }

   },

   stop : function(event, ui){


      $(function() {
    ui.item.removeClass( "ui-draggable-dragging" );
    $('.uldiv').removeClass( "bg-light" );
       $('.uldiv').removeClass('SmallDiv');
    $(".wonlosediv").hide();
       $('.btnPopover').click(function(e){
    e.stopPropagation();
       $(this).popover({
           html: true,
           trigger: 'manual',
            placement: popover_placement
       }).popover('toggle');
           $('.btnPopover').not(this).popover('hide');
       });

   });

   $('.sortable').sortable('refresh');



           },


       }).disableSelection();



         $(".sortable").disableSelection();

        <?php
      $PipeTitles = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('PipeId','=', $MainPipeId)->where('Status','=', '0')->orderBy('Sort', 'DESC')->get();
        foreach ($PipeTitles as $PipeTitle) {
        if (($PipeAgentView == '0' && @$_GET['AgentId'] == '') || $All=='True') {
            $count = DB::table('pipeline')
                ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                ->where('client.CompanyName', '!=', '')
                ->where('pipeline.CompanyNum','=', $CompanyNum)
                ->where('pipeline.PipeId','=', $PipeTitle->id)
                ->count();
        } else {
            $count = DB::table('pipeline')
                ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
                ->where('client.CompanyName', '!=', '')
                ->where('pipeline.CompanyNum','=', $CompanyNum)
                ->where('pipeline.PipeId','=', $PipeTitle->id)
                ->where('pipeline.AgentId','=', $AgentId)
                ->count();
        }
        $PipeTitle->count = $count;
        ?>

        count= <?php echo $PipeTitle->count; ?>;
       $('#DashPipeCount<?php echo $PipeTitle->id; ?>').text(count);
    <?php } ?>


    });




</script>
<script>
   $(function() {
       $('.btnPopover').click(function(e){
    e.stopPropagation();
       $(this).popover({
           html: true,
           trigger: 'manual',
            placement: popover_placement
       }).popover('toggle');
           $('.btnPopover').not(this).popover('hide');
       });

   });






   $(function() {
        var time = function(){return'?'+new Date().getTime()};

        $('#AddNewLead').imgPicker({
        });
        $('#AddNewClient').imgPicker({
        });
      $('#AddNewTask').imgPicker({

        });


   });


</script>
<?php } ?>
<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>
<?php endif ?>
<?php if (Auth::guest()): ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>
<?php require_once '../app/views/footernew.php'; ?>