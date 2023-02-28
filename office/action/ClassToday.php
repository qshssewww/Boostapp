<?php



require_once '../../app/initcron.php';



$CompanyNum = Auth::user()->CompanyNum;

$Act = $_REQUEST['Act'];



if ($Act=='0'){

    

$Today = date('Y-m-d');    

    

}

else if ($Act=='1'){

 

$Today = date('Y-m-d',strtotime("+1 day"));     

    

}



else if ($Act=='2'){

 

$Today = date('Y-m-d',strtotime("-1 day"));      

    

}



?>





 <?php

  $ClassTodays = DB::table('classstudio_date')->where('CompanyNum','=',$CompanyNum)->where('Status','!=','2')->where('StartDate','=',$Today)->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();  



foreach ($ClassTodays as $ClassToday) {  

    

if ($ClassToday->Status=='1'){

$StatusColor = 'style="background-color:#FBFBFB;"';    

}

else {

$StatusColor = '';    

}    

    

    

$FloorName = DB::table('sections')->where('CompanyNum','=',$CompanyNum)->where('id','=',$ClassToday->Floor)->first();

$GuideName = DB::table('users')->where('CompanyNum','=',$CompanyNum)->where('id','=',$ClassToday->GuideId)->first(); 



if ($ClassToday->ClientRegister!='0'){    

$FixTotalClassMax = @round((($ClassToday->ClientRegister/$ClassToday->MaxClient))*100);

}

else {

$FixTotalClassMax = '0';    

}    

?>       

       <div class="alertb alert-light text-dark text-right" <?php echo $StatusColor ?> >   

   <div class="row align-items-center">

    <div class="col-md-2">       



    <div class="ClassPrograss" value="<?php echo $FixTotalClassMax; ?>" data-class="<?php echo $ClassToday->ClientRegister; ?>/<?php echo $ClassToday->MaxClient; ?>" ></div>

       

   </div>   



   <div class="col-md-2">       

   <small><?php echo $ClassToday->ClassName; ?></small>

   </div>

       

       <div class="col-md-2">       

   <small><?php echo $FloorName->Title; ?></small>

   </div>         

       

   <div class="col-md-2">       

   <small><?php echo @$GuideName->display_name; ?></small>

   </div> 

       

   <div class="col-md-2">       

   <small><?php echo $ClassToday->Day; ?> | <?php echo with(new DateTime($ClassToday->StartTime))->format('H:i'); ?></small>

   </div>        

       

   <div class="col-md-2">       

     <div class="btn-group btn-group-sm">

    <button type="button" class="btn btn-light text-dark dropdown-toggle text-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

    פעולות

    </button>

    <div class="dropdown-menu text-right dropdown-menu-right"> 

        

    <?php if (Auth::userCan('83')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClientList" data-classid="<?php echo $ClassToday->id; ?>"><small>מתאמנים משובצים</small></a>

    <?php endif ?>

        

    <?php if (Auth::userCan('84')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClientWatingList" data-classid="<?php echo $ClassToday->id; ?>"><small>סידור רשימת המתנה</small></a>

    <?php endif ?>

    <?php if (Auth::userCan('83')): ?>    
    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClientListRegular" data-classid="<?php echo $ClassToday->id; ?>"><small>מתאמנים קבועים</small></a>     
    <div class="dropdown-divider"></div>  
    <?php endif ?> 

        

    <?php if (Auth::userCan('85')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClassClose" data-classid="<?php echo $ClassToday->id; ?>"><small>שיעור הושלם</small></a>

    <?php endif ?>

        

    <?php if (Auth::userCan('86')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClassCancel" data-classid="<?php echo $ClassToday->id; ?>"><small>ביטול שיעור</small></a>

    <div class="dropdown-divider"></div>     

    <?php endif ?>

        

    <?php if (Auth::userCan('87')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="SendNofitication" data-classid="<?php echo $ClassToday->id; ?>"><small>שליחת הודעה לרשומים</small></a>

    <div class="dropdown-divider"></div> 

    <?php endif ?>

        

    <?php if (Auth::userCan('83')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="AddRemarks" data-classid="<?php echo $ClassToday->id; ?>"><small>תוכן לשיעור</small></a>

    <div class="dropdown-divider"></div> 

    <?php endif ?>    

        

        

    <?php if (Auth::userCan('82')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClassViewDesks" data-classid="<?php echo $ClassToday->id; ?>"><small>צפה בפרטי השיעור</small></a>

    <?php endif ?>

        

    <?php if (Auth::userCan('89')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClassEditDesk" data-classid="<?php echo $ClassToday->id; ?>"><small>ערוך פרטי שיעור</small></a>  

    <div class="dropdown-divider"></div>  

    <?php endif ?>

        

    <?php if (Auth::userCan('134')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClassLogDesk" data-classid="<?php echo $ClassToday->id; ?>"><small>לוג שיעור</small></a>  

    <div class="dropdown-divider"></div>  

    <?php endif ?>    

        

    <?php if (Auth::userCan('89')): ?>    

    <a class="dropdown-item ShowClass" href="javascript:void(0)" data-classact="ClassDuplicateDesk" data-classid="<?php echo $ClassToday->id; ?>"><small>שכפל שיעור</small></a>

    <?php endif ?>  

        

        

    </div>

  </div>

   </div>  

       

 

       

    </div>

    </div> 

    <hr>     

   <?php } ?> 



<script>



$('.ClassPrograss').each(function(i) {

    var circle = new ProgressBar.Circle(this, {

  color: '#aaa',

  strokeWidth: 5,

  trailWidth: 1,

  easing: 'easeInOut',

  duration: 1000,

  text: {

    autoStyleContainer: false

  },

	});

    

    var value = ($(this).attr('value') / 100);

    var DataClass = ($(this).attr('data-class'));



	circle.animate(value, {

     from: { color: '#aaa', width: 1 },

     to: { color: '#48AD42', width: 5 },

	    step: function(state, circle) {

	    circle.path.setAttribute('stroke', state.color);

        circle.path.setAttribute('stroke-width', state.width);

	    circle.setText(DataClass);

	    }

	});

}); 

   

    

    $(".ShowClass").click( function()

    {



    var ClassId =  ($(this).attr('data-classid'));     

    var ClassAct = ($(this).attr('data-classact')); 

   //  alert(ClassAct);   

        

    if (ClassAct=='ClientList'){

$( "#DivViewDeskInfo" ).empty();	 

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('מתאמנים משובצים');    

    

 modalcode.modal('show'); 

 var url = 'new/ClientList.php?Id='+ClassId; 

// $('#DivViewDeskInfo').load(url); 



  $('#DivViewDeskInfo').load(url,function(e){    

  $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);  

   return false;     

  });

    

    

}

    

if (ClassAct=='ClientWatingList'){

$( "#DivViewDeskInfo" ).empty();	 

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('סידור רשימת המתנה');    

    

 modalcode.modal('show'); 

 var url = 'new/ClientWatingList.php?Id='+ClassId; 

// $('#DivViewDeskInfo').load(url); 



  $('#DivViewDeskInfo').load(url,function(e){    

  $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

   return false;      

  });

    

    

} 

if (ClassAct=='ClientListRegular'){
$( "#DivViewDeskInfo" ).empty();	 
var modalcode = $('#ViewDeskInfo');
$('#ViewDeskInfo .ip-modal-title').html('מתאמנים קבועים');    
    
 modalcode.modal('show'); 
 var url = 'new/ClientListRegular.php?Id='+ClassId; 
// $('#DivViewDeskInfo').load(url); 

  $('#DivViewDeskInfo').load(url,function(e){    
  $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);  
   return false;     
  });

  }

   



else if (ClassAct=='ClassClose'){

$( "#DivViewDeskInfo" ).empty();

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('שיעור הושלם');    

    

 modalcode.modal('show'); 

 var url = 'new/ClassClose.php?Id='+ClassId; 

 $('#DivViewDeskInfo').load(url,function(e){    

 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

 return false;      

 });    

    

}



  

else if (ClassAct=='ClassCancel'){

$( "#DivViewDeskInfo" ).empty();

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('ביטול שיעור');    

    

 modalcode.modal('show'); 

 var url = 'new/ClassCancel.php?Id='+ClassId; 

 $('#DivViewDeskInfo').load(url,function(e){    

 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

 return false;      

 });     

	

}

    

else if (ClassAct=='SendNofitication'){

$( "#DivViewDeskInfo" ).empty();

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('שליחת הודעה לרשומים');    

    

 modalcode.modal('show'); 

 var url = 'new/SendNofitication.php?Id='+ClassId; 

 $('#DivViewDeskInfo').load(url,function(e){    

 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

 return false;      

 });     

	

}    



  else if (ClassAct=='ClassLogDesk'){

$( "#DivViewDeskInfo" ).empty();	

var modalcode = $('#ViewDeskInfo');

$('#ViewDeskInfo .ip-modal-title').html('לוג שיעור');    

    

 modalcode.modal('show'); 

 var url = 'new/ClassLog.php?Id='+ClassId; 

 $('#DivViewDeskInfo').load(url,function(e){    

 $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

 return false;      

 });    

    

}           

        

else if (ClassAct=='ClassViewDesks'){

    

NewViewClass(ClassId);    

    

//$( "#DivViewDeskInfo" ).empty();

//var modalcode = $('#ViewDeskInfo');

//$('#ViewDeskInfo .ip-modal-title').html('פרטי השיעור');    

//    

// modalcode.modal('show'); 

// var url = 'new/ClassViewDesks.php?Id='+ClassId; 

// $('#DivViewDeskInfo').load(url,function(e){    

// $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

// return false;      

// });    

    

}	

    

else if (ClassAct=='ClassEditDesk'){

    

NewEditClass(ClassId);

    

//$( "#DivViewDeskInfo" ).empty();	

//var modalcode = $('#ViewDeskInfo');

//$('#ViewDeskInfo .ip-modal-title').html('עריכת שיעור');    

//    

// modalcode.modal('show'); 

// var url = 'new/ClassEditDesk.php?Id='+ClassId; 

// $('#DivViewDeskInfo').load(url,function(e){    

// $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

// return false;      

// });    

    

}

              

else if (ClassAct=='ClassDuplicateDesk'){

    

NewDuplicateClass(ClassId);

    

//$( "#DivViewDeskInfo" ).empty();	

//var modalcode = $('#ViewDeskInfo');

//$('#ViewDeskInfo .ip-modal-title').html('שכפל שיעור');    

//    

// modalcode.modal('show'); 

// var url = 'new/ClassDuplicateDesk.php?Id='+ClassId; 

// $('#DivViewDeskInfo').load(url,function(e){    

// $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);       

// return false;      

// });    

    

}	        

        

});    

</script>

