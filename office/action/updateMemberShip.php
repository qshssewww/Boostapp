<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('membership_type')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();
$myArray = explode(',', $Items->ClassMemberType);
?>

                <div class="form-group" >
                <label>כותרת המנוי</label>
                <input type="text" name="Type" class="form-control" value="<?php echo htmlentities($Items->Type); ?>" required>
                </div>   
                <hr>

                <div class="form-group" >
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>      
                </select>
                </div>  


                <a href="javascript:void(0)" id="AdvanceSettingsBtnA">הגדרות מתקדמות</a>

                <div id="AdvanceSettingsA" style="display: <?php if ($Items->ViewClassAct=='1') { echo 'block'; } else { echo 'none'; } ?>; padding-top: 5px;">
    
                <div class="form-group" >
                <label>להציג תצוגת שיעורים שונה באפליקציה?</label>
                <select class="form-control" name="ViewClassAct" id="ViewClassActA">
                <option value="1" <?php if ($Items->ViewClassAct=='1') { echo 'selected'; } else {} ?> >כן</option>  
                <option value="0" <?php if ($Items->ViewClassAct=='0') { echo 'selected'; } else {} ?> >לא</option>      
                </select>
                </div>   
    
                <div id="ViewClassActDivA" style="display: <?php if ($Items->ViewClassAct=='1') { echo 'block'; } else { echo 'none'; } ?>;">    
                <div class="form-group" >
                <label>מספר ימים לתצוגה באפליקציה</label>
                <input type="number" min="0" name="ViewClassDayNum" class="form-control" value="<?php echo $Items->ViewClassDayNum; ?>">
                </div>
                </div>
                    
                </div> 

  

<style>

.select2-results__option[aria-selected=true] {
    display: none;
}
</style>

<script> 
    
    
 $('#AdvanceSettingsBtnA').click(function() {
     
     if($('#AdvanceSettingsA').is(":hidden"))
    {   
     $('#AdvanceSettingsA').show();   
    }
    else {
     $('#AdvanceSettingsA').hide();     
    }
 });     
    
    
 $("#ViewClassActA").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  ViewClassActDivA.style.display = "block";   
  } 
  else {
  ViewClassActDivA.style.display = "none";      
  }    
});	    
    
    
$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור",  minimumResultsForSearch: -1, } );
    
$('#ClassMemberTypes').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור", minimumResultsForSearch: -1, } );
    }
  }
    
});	      
   
    
  $('#ClassMemberTypes').on('select2:open', function () {
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
    
</script>    