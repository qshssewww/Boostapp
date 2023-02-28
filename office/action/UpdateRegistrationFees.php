<?php require_once '../../app/initcron.php'; ?>
<?php if (Auth::userCan('154')): ?>
<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;

$Items = DB::table('registration_fees')->where('CompanyNum','=',$CompanyNum)->where('id', '=' , $ItemId)->first();

?>

                <div class="form-group" dir="rtl">
                <label>כותרת</label>
                <input type="text" name="ItemName" id="ItemName" class="form-control" value="<?php echo htmlentities($Items->ItemName); ?>">
                </div>


                <div class="row">
                <div class="col-md-4 col-sm-12">
				<div class="form-group" dir="rtl">
                <label>סכום</label>
                <input type="text" name="ItemPrice" id="ItemPrice" class="form-control" onkeypress='validate(event)' value="<?php echo $Items->ItemPrice; ?>" required>
                </div> 	
				</div>

				<div class="col-md-4 col-sm-12">
				<div class="form-group" dir="rtl">
                <label>סכום לפני מע"מ</label>
                <input type="text" name="ItemPriceVat" id="ItemPriceVat" class="form-control" onkeypress='validate(event)' value="<?php echo $Items->ItemPriceVat; ?>"  disabled>
                </div> 	
				</div>	
					
				<div class="col-md-4 col-sm-12">
				<div class="form-group" dir="rtl">
                <label>מע"מ</label>
                <input type="text" name="VatAmount" id="VatAmount" class="form-control" onkeypress='validate(event)' value="<?php echo $Items->VatAmount; ?>"  disabled>
                </div> 	
				</div>		
					
                </div>


               <div class="row">
				   
				<div class="col-md-3">   
                <div class="form-group" dir="rtl">
                <label>הגדרת חידוש</label>
                <select name="VaildType" id="VaildType"  class="form-control" style="width:100%;"  data-placeholder="בחר"  >    
                <option value="0" <?php if ($Items->Vaild!='0') { echo 'selected'; } else {} ?> >כן</option>
                <option value="1" <?php if ($Items->Vaild=='0') { echo 'selected'; } else {} ?> >לא</option>       
                </select>      
                </div> 
                </div>      
				   
               	<div class="col-md-3" id="DivVaildType0" style="display: <?php if ($Items->Vaild=='0') { echo 'none'; } else {} ?>;">   
                <div class="form-group" dir="rtl">
                <label>תוקף</label>
                <input type="number" max="36" <?php if ($Items->Vaild=='0') { echo 'min="0"'; } else { echo 'min="1"'; echo 'required'; } ?> name="Vaild" id="Vaild" value="<?php echo $Items->Vaild; ?>" class="form-control" onkeypress='validate(event)'>
                <div class="help-block with-errors"></div>     
                </div> 
                </div>     
                <div class="col-md-3" id="DivVaildType1" style="display: <?php if ($Items->Vaild=='0') { echo 'none'; } else {} ?>;">
                <div class="form-group" dir="rtl">
                <label>חשב לפי</label>
               <select name="Vaild_Type"  class="form-control" style="width:100%;"  data-placeholder="בחר"  >    
               <option value="1" <?php if ($Items->Vaild_Type=='1') { echo 'selected'; } else {} ?> >ימים</option>
               <option value="2" <?php if ($Items->Vaild_Type=='2') { echo 'selected'; } else {} ?> >שבועות</option>
               <option value="3" <?php if ($Items->Vaild_Type=='3') { echo 'selected'; } else {} ?> >חודשים</option>       
               </select> 
                </div>     
                    
               </div>
                    
                <div class="col-md-3" id="DivVaildType2" style="display: <?php if ($Items->Vaild=='0') { echo 'none'; } else {} ?>;">
                <div class="form-group" dir="rtl">
                <label>התראה לסיום בימים</label>
                <input type="number" min="0" value="<?php echo $Items->NotificationDays; ?>" name="NotificationDays" class="form-control" placeholder="הקלד בימים" value="3" onkeypress='validate(event)'>   
                </div>   
                     
                </div> 
  
               </div> 

                <div class="form-group" dir="rtl">
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>לא פעיל</option>      
                </select>
                </div>   


<script>

$("#VaildType").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  DivVaildType0.style.display = "block"; 
  DivVaildType1.style.display = "block"; 
  DivVaildType2.style.display = "block";
	  
$("#Vaild").attr({
       "min" : 1          // values (or variables) here
    });	  
  $("#Vaild").prop('required',true);	  
  } 
  else {
  DivVaildType0.style.display = "none";
  DivVaildType1.style.display = "none"; 
  DivVaildType2.style.display = "none"; 

$("#Vaild").attr({
       "min" : 0          // values (or variables) here
    });	 	  
  $("#Vaild").prop('required',false);	  
  }    
});	 


</script>




<?php endif ?>