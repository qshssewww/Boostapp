<?php require_once '../app/initcron.php'; 
if (Auth::userCan('49')):   

if (Auth::userCan('48')): 
$OpenEdit = '';
 else: 
$OpenEdit = 'disablesortable';
endif; 

$CompanyNum = Auth::user()->CompanyNum;
$MainPipeId = $_REQUEST['PipeId'];

$All = @$_REQUEST['All'];
$AgentId = @$_REQUEST['AgentId'];

$MainPipeLine = DB::table('pipeline_category')->where('id', @$MainPipeId)->where('CompanyNum' ,'=', $CompanyNum)->first();
$PipeAgentView = @$MainPipeLine->PipeAgentView;
$MaxRecord = @$MainPipeLine->MaxRecord;  

$count = $_REQUEST['count'];
$showlimit = $count + 30 > $MaxRecord ?  $MaxRecord : 30;

$SettingsLeads = DB::table('leadsettings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$PipeAgentView = @$SettingsLeads->PipeAgentView;
$CalAgentView = @$SettingsLeads->CalAgentView;

$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$BrandsMain = $SettingsInfo->BrandsMain;

 

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


	$id = $_REQUEST['id'];
    if ($PipeAgentView=='0' && @$_GET['AgentId']=='' || $All=='True') {
        $PipeLines = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->select('pipeline.*', 'client.CompanyName', 'client.ContactMobile', 'client.Email', 'client.Dob', 'client.Gender')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum','=', $CompanyNum)
            ->where('pipeline.PipeId','=', $id)
            ->take($count+$showlimit)
            ->orderBy('pipeline.TaskStatus', 'ASC')->orderBy('pipeline.NoteDates', 'ASC')->orderBy('pipeline.Dates', 'ASC')
            ->get();
        $PipeLinesLimit = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum', '=', $CompanyNum)
            ->where('pipeline.PipeId', '=', $id)
            ->count();
    }
    else {
        $PipeLines = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->select('pipeline.*', 'client.CompanyName', 'client.ContactMobile', 'client.Email', 'client.Dob', 'client.Gender')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum','=', $CompanyNum)
            ->where('pipeline.PipeId','=', $id)
            ->where('pipeline.AgentId','=', $AgentId)
            ->take($count+$showlimit)
            ->orderBy('TaskStatus', 'ASC')->orderBy('NoteDates', 'ASC')->orderBy('Dates', 'ASC')
            ->get();
        $PipeLinesLimit = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum', '=', $CompanyNum)
            ->where('pipeline.PipeId', '=', $id)
            ->where('pipeline.AgentId','=', $AgentId)
            ->count();
    }


    if(count($PipeLines) > 0){

	$ColorRed = '';
	$DataPopUp = '';	
	
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
    $GenderIcon = '<i class="fas fa-mars" data-toggle="tooltip" title='.lang('male').'></i>';
    }   
    else if ($Gender=='2'){
    $GenderIcon = '<i class="fas fa-venus" data-toggle="tooltip" title='.lang('female').'></i>';
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
		
    if ($val['Date'] < date('Y-m-d') || $val['Date'] == date('Y-m-d') && $val['Time'] < date('H:i:s')){
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
     <a href="javascript:void(0);" <?php if ($SettingsInfo->VoiceCenterToken!='') {?>OnClick="CallToClient(<?php echo $ClientId; ?>,<?php echo $PipeLine->id; ?>);"<?php }?> style="color: #AEAEAE;">
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
     <span class="text-secondary disablesortable"> <a href='javascript:PipeMore(<?php echo $PipeLine->id; ?>,<?php echo $PipeLine->MainPipeId; ?>,<?php echo $PipeLine->PipeId; ?>,<?php echo $PipeLine->ClientId; ?>);' dir="rtl" class="text-secondary text-start disablesortable">  <i class="fas fa-info-circle disablesortable" data-toggle="tooltip" title="פרטים נוספים"></i></a> <?php if ($CheckNotes>='1'){ ?><a href='javascript:PipeAddNote(<?php echo $PipeLine->id; ?>,<?php echo $PipeLine->ClientId; ?>);' class='text-secondary'><i class="fas fa-sticky-note disablesortable" data-toggle="tooltip" title="פתקים"></i></a><?php } ?> <?php if (@$CheckClass->id!=''){ ?><a href='javascript:PipeAddCalss(<?php echo $PipeLine->id; ?>,<?php echo $PipeLine->ClientId; ?>);' class='<?php echo $ClassActColor; ?>' ><i class="fas fa-calendar-check disablesortable <?php echo $ClassActColor; ?>" data-toggle="tooltip" title="<?php echo $ClassActText; ?>"></i></a><?php } ?> </span>
     </div>  
    <div class="d-flex justify-content-between" style="color: #AEAEAE;">
        <span style="font-size: 13px;" class="disablesortable" > 
        <a href='javascript:PipeAction(<?php echo $PipeLine->id; ?>,<?php echo $PipeLine->MainPipeId; ?>,<?php echo $PipeLine->PipeId; ?>,<?php echo $PipeLine->ClientId; ?>);'  class="text-start disablesortable">
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
       
     <a href="javascript:void(0);" class="btnPopover text-start disablesortable" onclick="LeadsData.btnPopoverClick(this, event)" rel="popover" data-toggle="popover" data-html="true" data-content="<div style='width: 250px; padding-top: 5px; padding-bottom: 5px; padding-right: 5px;'>
     <div class='DivScroll text-dark' style='max-height:220px; overflow-y:scroll; overflow-x:hidden;margin: 0px; padding: 0px; '>
 <?php echo $DataPopUp; ?>
 </div>
 
<div style='text-align: center;' align='center'>
<?php if (Auth::userCan('48')): ?>
    <a data-task='<?php echo $NewTask; ?>' data-client='<?php echo $PipeLine->ClientId; ?>'  data-pipe-id='<?php echo $PipeLine->id; ?>' class='text-dark js-new-task'><?= lang('new_task') ?></a>
<?php endif; ?>
 </div> 
  </div>">
    <style type="text/css"> 
        .bsapp-corner-badge.badge-color-<?php echo $PipeLine->id ?>:before{
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
	
	}

	}
	
	if($PipeLinesLimit > count($PipeLines)){ ?>
        <script>localStorage.setItem("limitflag", 0);</script>
    <?php }
	else { ?>
		<script>localStorage.setItem("limitflag", 1);</script>
	
	<?php }

endif;