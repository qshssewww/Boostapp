<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=' , $ItemId)->first();


?>

              
                <div class="form-group" >
                <label>שם הסטטוס</label>
                <input type="text" name="Title" id="Title" class="form-control" value="<?php echo htmlentities($Items->Title); ?>" required>
                </div>   
              


               <div class="form-group">
              <label>סטטוס </label>
              <select name="Status" id="Status" class="form-control" style="width:100%;"  data-placeholder="בחר סטטוס"  >
               <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>מוצג</option>
               <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>

              </select>  
              
              </div>