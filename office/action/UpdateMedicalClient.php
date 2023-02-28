<?php require_once '../../app/init.php'; ?>

<?php
$CompanyNum = Auth::user()->CompanyNum;
$ItemId = $_POST['ItemId'];

$CRM = DB::table('clientmedical')->where('id', '=' , $ItemId)->where('CompanyNum', '=' , $CompanyNum)->first();

?>


<div class="form-group">
<label>עד תאריך</label>   
<input name="TillDate" type="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $CRM->TillDate; ?>" class="form-control">    
</div>       
     
<div class="alertb alert-info">לתיעוד רפואי קבוע השאר שדה 'עד תאריך' ריק.</div>    

<div class="form-group">
<label>סטטוס</label>   
<select name="Status" class="form-control">
<option value="0" <?php if ($CRM->Status=='0'){ echo 'selected'; } ?>>מוצג</option>
<option value="1" <?php if ($CRM->Status=='1'){ echo 'selected'; } ?> >מוסתר</option>    
</select>  
</div>