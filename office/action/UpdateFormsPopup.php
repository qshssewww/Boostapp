<?php require_once '../../app/initcron.php'; ?>

<?php

$FormsId = $_POST['FormsId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Forms = DB::table('dynamicforms')->where('id', '=' , $FormsId)->where('CompanyNum','=', $CompanyNum)->first();

?>

                <div class="form-group" dir="rtl">
                <label>כותרת הטופס</label>
                <input type="text" name="Title" class="form-control" value="<?php echo htmlentities($Forms->name); ?>" required>
                </div>   

				 <?php 
				  $BrandsSettings = DB::table('boostapp.brands')->where('FinalCompanynum', '=', $CompanyNum)->where('ShowBrand', '=', '1')->first(); 
				  if (@$BrandsSettings->id!=''){                        
				  ?>                        
				  <div class="form-group">
				  <label>סניף</label>
				  <select class="form-control js-example-basic-single select2BarndSelects text-right" data-placeholder="בחר סניפים"  name="BarndSelect[]" id="BarndSelects" dir="rtl"  multiple="multiple" data-select2order="true" style="width: 100%;" required>
				  <option value="0" <?php if ($Forms->Brands=='0' || $Forms->Brands=='') { echo 'selected'; } else {} ?> >כל הסניפים</option>
				  <?php 
				  $myArray = explode(',', $Forms->Brands);      
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
				  <input type="hidden" name="BarndSelect" value="A0">                                      
				  <?php } ?>    

                <div class="form-group" dir="rtl">
                <label>הגדרת חישוב תוקף לפי</label>
                <select class="form-control text-right" name="VaildType" id="PrivateSection2" dir="rtl"> 	
				<option value="0" <?php if ('0'==@$Forms->VaildType){ echo 'selected';} else {} ?>>ללא חישוב תוקף</option>
                <option value="1" data-text="(שבועות)" <?php if ('1'==@$Forms->VaildType){ echo 'selected';} else {} ?>>אחת ל-X שבועות</option>
				<option value="2" data-text="(חודשים)" <?php if ('2'==@$Forms->VaildType){ echo 'selected';} else {} ?>>אחת ל-X חודשים</option>
				<option value="3" data-text="(שנים)" <?php if ('3'==@$Forms->VaildType){ echo 'selected';} else {} ?>>אחת ל-X שנים</option>	
				</select>
                </div>	
  
                <?php
                if (@$Forms->VaildType=='1'){
				$VaildText = '(שבועות)';
				$Max = 'max="3360"';
				$Required = 'required';	
				} 
                else if (@$Forms->VaildType=='2'){
				$Max = 'max="120"';
				$Required = 'required';	
				}
                else if (@$Forms->VaildType=='3'){
				$Max = 'max="10"';
				$Required = 'required';	
				}
                ?>
             
                <div id="DivPrivateSection2" style="display: <?php if ('0'!=$Forms->VaildType){ echo 'block';} else { echo 'none'; } ?>;">
                <div class="form-group" dir="rtl">
                <label>תוקף <span id="TextVaild"><?php echo @$VaildText; ?></span> </label>
                <input type="number" name="VaileVaule" id="VaileVaule" min="1" <?php echo @$Max; ?> class="form-control" value="<?php echo htmlentities($Forms->VaileVaule); ?>" <?php echo @$Required; ?>>
                </div>
					
				<div class="alertb alert-info">שים לב! בהגדרת תוקף לטופס, בעת מילוי הטופס ע"י הלקוח יחושב תאריך קבלת ההתראה שישלח לסטודיו למעקב.
				</div>	
					
				<div class="alertb alert-warning">שים לב!
                בעדכון הגדרת תוקף, בעת שמירת הנתונים יעודכנו כלל הלקוחות שחתמו על טופס זה.
				</div>	
					
                </div>


                <div class="form-group" dir="rtl">
                <label>סטטוס</label>
                <select class="form-control" name="Status" id="StatusDiv">
                <option value="0" <?php if ($Forms->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Forms->Status=='1') { echo 'selected'; } else {} ?>>לא פעיל</option>      
                </select>
                </div>  

                 <div id="StatusDiv2" style="display: <?php if ('1'==$Forms->Status && '0'!=$Forms->VaildType){ echo 'block';} else { echo 'none'; } ?>;">
					 
				<div class="alertb alert-warning">שים לב!
                בשינוי סטטוס ללא פעיל, לא יתקבלו התראות לתוקף.
				</div>	

                 </div>


<script>
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
	
$("#PrivateSection2").change(function() {
  
  var Id = this.value;
  var TextVaild = $(this).find(':selected').attr('data-text'); // $(this).data("text");	
	
  if (Id!='0'){    
  DivPrivateSection2.style.display = "block";  
  $('#TextVaild').html(TextVaild);		  
  } 
  else {
  DivPrivateSection2.style.display = "none";
  $('#TextVaild').html('');		  
  } 
	
  if (Id=='1'){
  $('#VaileVaule').attr('Max','3360');
  $('#VaileVaule').attr('required',true);
  $('#VaileVaule').val('1');  
  }
  else if (Id=='2'){
  $('#VaileVaule').attr('Max','120');
  $('#VaileVaule').attr('required',true);	  
  $('#VaileVaule').val('1');  
  }
  else if (Id=='3'){
  $('#VaileVaule').attr('Max','10');
  $('#VaileVaule').attr('required',true);	  
  $('#VaileVaule').val('1');	  
  }
  else {
  $('#VaileVaule').removeAttr('Max');
  $('#VaileVaule').removeAttr('required');	  
  $('#VaileVaule').val('1');	  
  }	
	
	
  var StatusDiv = $('#StatusDiv').val();	
	
  if (StatusDiv=='1' && Id!='0'){
  StatusDiv2.style.display = "block"; 	   
  }	
  else {
  StatusDiv2.style.display = "none";	  
  }	
	
});		

$("#StatusDiv").change(function() {
  
  var Id = this.value;
  var TextVaild = $('#PrivateSection2').val();
	
  if (Id=='1' && TextVaild!='0'){    
  StatusDiv2.style.display = "block";  	  
  } 
  else {
  StatusDiv2.style.display = "none";	  
  } 
	
	
});		
	
	
</script>

                