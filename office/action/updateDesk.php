<?php require_once '../../app/init.php'; ?>

<?php

$TaskId = $_POST['TaskId'];

$Task = DB::table('desk')->where('id', '=' , $TaskId)->first();

?>

<style>

input[type=radio]:checked:before {
	content: '\2022';
	text-indent: -9999px;
	-webkit-border-radius: 50px;
	border-radius: 50px;
	font-size: 24px;
	width: 6px;
	height: 6px;
	margin: 4px;
	line-height: 16px;
	background: #33A93D;
}

</style>				
<div class="form-group">
<label class="radio-inline" style="text-decoration:underline;">סוג</label>               
<label class="radio-inline">
<input type="radio" name="TaskType" id="inlineRadio1" value="1" <?php if ($Task->TaskType=='1') { echo 'checked';} else {} ?>> התקנה+פירוק
</label>
<label class="radio-inline">
<input type="radio" name="TaskType" id="inlineRadio1" value="2" <?php if ($Task->TaskType=='2') { echo 'checked';} else {} ?>> התקנה
</label>
<label class="radio-inline">
<input type="radio" name="TaskType" id="inlineRadio2" value="3" <?php if ($Task->TaskType=='3') { echo 'checked';} else {} ?>> פירוק
</label>
<label class="radio-inline">
<input type="radio" name="TaskType" id="inlineRadio3" value="4" <?php if ($Task->TaskType=='4') { echo 'checked';} else {} ?>> שירות
</label>
<label class="radio-inline">
<input type="radio" name="TaskType" id="inlineRadio3" value="5" <?php if ($Task->TaskType=='5') { echo 'checked';} else {} ?>> ייעוץ
</label>
</div>       


             <div class="form-group">
              <label>בחר לקוח</label>
              <select name="Client" id="Client" data-placeholder="בחר לקוח" class="form-control select2" style="width:100%;">
              <option value=""></option>

              <?php

$ClientsNames = DB::table('client')->get();

foreach ($ClientsNames as $ClientsName) {
	
?>  

                <option value="<?php echo $ClientsName->id; ?>" <?php if ($Task->ClientId==$ClientsName->id) { echo 'selected';} else {} ?>><?php echo $ClientsName->CompanyName; ?></option>
                
                <?php } ?>
               
              </select>
              
              </div>

                       
             <div class="form-group">
              <label>כותרת</label>
              <input name="TaskTitle" type="text" class="form-control focus-me" id="TaskTitle" placeholder="כותרת" <?php _e('main.rtl') ?> value="<?php echo $Task->TaskTitle; ?>">
              
              </div>
              
               <div class="clearfix" style="margin-bottom:20px;">   
              <div class="pull-<?php _e('main.right') ?>" style="margin-<?php _e('main.left') ?>: 5px;">
              <label>תאריך התחלה</label>
              <input name="StartDate" type="date" class="form-control" id="StartDate" placeholder="תאריך התחלה" <?php _e('main.rtl') ?> value="<?php echo with(new DateTime($Task->StartDate))->format('Y-m-d'); ?>">
              </div>
              <div class="pull-<?php _e('main.right') ?>" style="margin-<?php _e('main.left') ?>: 5px;">
              <label>שעת התחלה</label>
              <input name="StartTime" type="time" class="form-control" id="StartTime" placeholder="שעת התחלה" <?php _e('main.rtl') ?> value="<?php echo with(new DateTime($Task->StartTime))->format('H:i:s'); ?>">
              </div> 
              
              <div class="pull-<?php _e('main.right') ?>" style="margin-<?php _e('main.left') ?>: 5px;">
              <label>תאריך סיום</label>
              <input name="EndDate" type="date" class="form-control" id="EndDate" placeholder="תאריך סיום" <?php _e('main.rtl') ?> value="<?php echo with(new DateTime($Task->EndDate))->format('Y-m-d'); ?>">
              </div> 
              
              </div>


                <div class="form-group">
                 <label>פרטים</label>
                <textarea name="Details" id="Details" class="form-control" rows="3" <?php _e('main.rtl') ?> placeholder="פרטים"><?php echo $Task->Details; ?></textarea>
                </div>
                
     <script>
	 
	 $(document).ready(function(){

$( ".select2" ).select2( { placeholder: "Select a State", maximumSelectionSize: 6,language: "he" } );

$( ".select3" ).select2( { placeholder: "Select a State", maximumSelectionSize: 6,language: "he" } );


 });
	 
	 </script>           