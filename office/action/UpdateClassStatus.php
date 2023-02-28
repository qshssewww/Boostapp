<?php require_once '../../app/initcron.php'; ?>

<?php

$ClassId = $_REQUEST['ClassId'];
$Status = $_REQUEST['Status'];
$CompanyNum = Auth::user()->CompanyNum;
$Class = DB::table('classstudio_date')->where('id', '=' , $ClassId)->where('CompanyNum', '=' , $CompanyNum)->first();

?>


              <input type="hidden" name="ClassId" value="<?php echo $Class->id; ?>">

              <div class="form-group">
              <label>סטטוס </label>
              <select name="Status" id="Status" class="form-control" style="width:100%;"  data-placeholder="בחר סטטוס"  >
               <option value="0" <?php if ($Class->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>
               <option value="1" <?php if ($Class->Status=='1') { echo 'selected'; } else {} ?>>הושלם</option>
               <option value="2" <?php if ($Class->Status=='2') { echo 'selected'; } else {} ?>>בוטל</option>

              </select>  
              
              </div>