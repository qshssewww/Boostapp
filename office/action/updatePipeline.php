<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('pipeline_category')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=' , $ItemId)->first();


?>

              
                <div class="form-group" >
                <label><?php echo lang('pipeline_name_edit') ?></label>
                <input type="text" name="Title" id="Title" class="form-control" value="<?php echo htmlentities($Items->Title); ?>" required>
                </div>   
              

                <div class="form-group">
                <label><?php echo lang('display_by_representative') ?></label>
                <select name="PipeAgentView" class="form-control" style="width:100%;" >
                <option value="0" <?php echo $Items->PipeAgentView == '0' ? 'selected' : '' ?> ><?php echo lang('display_all') ?></option>
                <option value="1" <?php echo $Items->PipeAgentView == '1' ? 'selected' : ''?> ><?php echo lang('display_by_represent') ?></option>
                </select>  
                </div>
    
                <div class="form-group" >
                <label><?php echo lang('max_leads_load') ?></label>
                <input type="number" max="150" min="1" name="MaxRecord" class="form-control" value="<?php echo $Items->MaxRecord; ?>" onkeypress="validate(event)">
                </div>   


              <?php if ($Items->Act=='0'){ ?>
              <div class="form-group">
              <label><?php echo lang('status_table') ?> </label>
              <select name="Status" id="Status" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('choose_status') ?>"  >
               <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>><?php echo lang('displayed') ?></option>
               <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>><?php echo lang('hidden') ?></option>

              </select>  
              
              </div>
              <?php } else { ?>
              <input type="hidden" name="Status" value="0">
              <?php } ?>