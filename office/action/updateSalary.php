<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_REQUEST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('coach_paymentstep')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();
$myArray = explode(',', $Items->ClassType);
?>


                <div class="form-group" dir="rtl">
                <label><?php echo lang('salary_type') ?></label>
                <select name="Salary" id="SalaryA" class="form-control">
                <option value="1" <?php if ($Items->Type=='1') { echo 'selected'; } else {} ?> ><?php echo lang('time_clock') ?></option>
                <option value="2" <?php if ($Items->Type=='2') { echo 'selected'; } else {} ?> ><?php echo lang('class_hours_agentprofile') ?></option>
                <option value="3" <?php if ($Items->Type=='3') { echo 'selected'; } else {} ?> ><?php echo lang('number_of_trainee_agentprofile') ?></option>
                <option value="4" <?php if ($Items->Type=='4') { echo 'selected'; } else {} ?> ><?php echo lang('reports_fixed_payroll') ?></option>
                </select>
                </div>  

			
			    <div class="form-group">
                <label><?php echo lang('start_date_salary') ?> <em><?php _e('main.required') ?></em></label>
                <input type="date" class="form-control" name="StartDate" min="<?php echo $Items->StartDate; ?>" value="<?php echo $Items->StartDate; ?>">
                </div>
    
                <div id="DivNumClientA">
                <div class="form-group">
                <label><?php echo lang('starting_from_agentprofile') ?></label>
                <input type="number" min="1" class="form-control" name="NumClient" value="<?php echo $Items->NumClient; ?>">
                </div>
                <div class="alertb alert-info"><?php echo lang('step_participants_agentprofile') ?><br>
                    <?php echo lang('example_1_3_agentprofile') ?><br>
                    <?php echo lang('plus_4_6_agentprofile') ?> <br>
                </div>      
                </div>
    
                <div id="DivClassTypeA">
                <div class="form-group">
                <label><?php echo lang('wage_type_class_agentprofile') ?></label>
                <select class="form-control js-example-basic-single select2multipleDesk text-right" name="ClassMemberType[]" id="ClassMemberTypeA" dir="rtl"  multiple="multiple" data-select2order="true" style="width: 100%;">
                <option value=""></option> 
                <?php 
                $SectionInfos = DB::table('class_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Type', 'ASC')->get();
                foreach ($SectionInfos as $SectionInfo) {
                $selected = (in_array($SectionInfo->id, $myArray)) ? ' selected="selected"' : '';     
                ?>  
                <option value="<?php echo $SectionInfo->id; ?>" <?php echo @$selected; ?> ><?php echo $SectionInfo->Type; ?></option>	  
                <?php 
                }
                ?>  
                </select> 
                </div> 
                </div> 
    
    
                <div class="form-group">
                <label><?php echo lang('wage_type_class_agentprofile') ?> <em><?php _e('main.required') ?></em></label>
                <input type="text" class="form-control" name="Amount" value="<?php echo $Items->Amount; ?>" onkeypress="validate(event)">
                </div>

                <div class="form-group">
                <label><?php echo lang('wage_assistant_agentprofile') ?> <em><?php _e('main.required') ?></em></label>
                <input type="text" class="form-control" name="ExtraAmount" value="<?php echo $Items->ExtraAmount; ?>" onkeypress="validate(event)">
                </div>

              <div id="DivLateCancelA">
              <div class="checkbox">
              <label>
              <input type="checkbox" class="pull-right" value="1" name="NoneShow" <?php if ($Items->NoneShow=='1') { echo 'checked'; } else {} ?> > <?php echo lang('count_charged_agentprofile') ?>
			  </label>
              </div>  
    
              <div class="checkbox">
              <label>
              <input type="checkbox" class="pull-right" value="1" name="LateCancel" <?php if ($Items->LateCancel=='1') { echo 'checked'; } else {} ?>> <?php echo lang('count_late_cancel_agentprofile') ?>
			  </label>
              </div>   
              </div> 


                <div class="form-group" dir="rtl">
                <label><?php echo lang('status_table') ?></label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>><?php echo lang('active') ?></option>
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>><?php echo lang('hidden') ?></option>
                </select>
                </div>  


  

<style>

.select2-results__option[aria-selected=true] {
    display: none;
}
</style>

<script> 
    
  $('#SalaryA').trigger('change');  
    
  $("#SalaryA").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivNumClientA.style.display = "none";
  DivClassTypeA.style.display = "none";
  DivLateCancelA.style.display = "none";      
  } 
  else if (Id=='2') {
  DivNumClientA.style.display = "none";
  DivClassTypeA.style.display = "block";
  DivLateCancelA.style.display = "none";      
  } 
  else if (Id=='3') {
  DivNumClientA.style.display = "block";
  DivClassTypeA.style.display = "block";
  DivLateCancelA.style.display = "block";      
  }  
  else if (Id=='4') {
  DivNumClientA.style.display = "none";
  DivClassTypeA.style.display = "block";
  DivLateCancelA.style.display = "none";      
  }   
     
     
     
     
     
});	 
    
    
$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose_class_type') ?>", 'language':"he", dir: "rtl", minimumResultsForSearch: -1, } );
    
$('#ClassMemberTypeA').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose_class_type') ?>", 'language':"he", dir: "rtl", minimumResultsForSearch: -1, } );
    }
  }
    
});	      
   
    
  $('#ClassMemberTypeA').on('select2:open', function () {
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
    
$("#SalaryA").trigger('change');    
    
</script>    