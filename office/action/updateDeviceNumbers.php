<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('numbers')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();

?>

                <div class="form-group" >
                <label>כותרת</label>
                <input type="text" name="Type" class="form-control" value="<?php echo htmlentities($Items->Name); ?>" required>
                </div>   

               

                <div class="form-group" >
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>      
                </select>
                </div>   
