<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('leadsource')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=' , $ItemId)->first();


?>

                <div class="form-group" >
                <label><?php echo lang('lead_source_name') ?></label>
                <input type="text" name="Title" id="Title" class="form-control" value="<?php echo htmlentities($Items->Title); ?>" required>
                </div>   
              


               <div class="form-group">
              <label><?php echo lang('status_table') ?> </label>
              <select name="Status" id="Status" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('choose_status') ?>"  >
               <option value="0" <?php echo $Items->Status=='0' ? 'selected' : '' ?>><?php echo lang('displayed') ?></option>
               <option value="1" <?php echo $Items->Status=='1' ? 'selected' : '' ?>><?php echo lang('hidden') ?></option>

              </select>  
              
              </div>