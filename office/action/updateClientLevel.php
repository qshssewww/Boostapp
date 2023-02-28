<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum; 

$Items = DB::table('clientlevel')->where('id', '=' , $ItemId)->where('CompanyNum','=', $CompanyNum)->first();

?>

                <div class="form-group">
                <label>כותרת המנוי</label>
                <input type="text" name="Type" class="form-control" value="<?php echo htmlentities($Items->Level); ?>" required>
                </div>   
                <hr>



     