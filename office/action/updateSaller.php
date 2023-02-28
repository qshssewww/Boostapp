<?php require_once '../../app/init.php'; ?>


<?php

$ItemId = $_POST['Id'];

$Items = DB::table('leads')->where('id', '=' , $ItemId)->first();

?>

                <div class="form-group" dir="rtl">
                <label>מנהל מכירות</label>
                <select class="form-control select4" name="Manager" required data-placeholder="בחר מנהל מכירות">
                <option value="">בחר</option>
                <?php 
                $Users = DB::table('users')->where('ActiveStatus', '=' , '0')->where('role_id', '!=' , '2')->get();   foreach ($Users as $User){ 
                ?>
                <option value="<?php echo $User->id; ?>" <?php if ($Items->Manager==$User->id) { echo 'selected'; } else {} ?>><?php echo $User->display_name; ?></option>  
                <?php } ?>
                </select>
                </div>  

                <div class="form-group" dir="rtl">
                <label>נציג מכירות</label>
                <select class="form-control select4" name="Seller" required data-placeholder="בחר נציג מכירות">
                <option value="">בחר</option> 
                <?php 
                $Users = DB::table('users')->where('ActiveStatus', '=' , '0')->get();   foreach ($Users as $User){ 
                ?>
                <option value="<?php echo $User->id; ?>" <?php if ($Items->Seller==$User->id) { echo 'selected'; } else {} ?>><?php echo $User->display_name; ?></option>  
                <?php } ?>     
                </select>
                </div>  
                
                <div class="form-group" dir="rtl">
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <?php 
                $Statuss = DB::table('leadstatus')->get();   
                foreach ($Statuss as $Status){ 
                ?>
                <option value="<?php echo $Status->id; ?>" <?php if ($Items->Status==$Status->id) { echo 'selected'; } else {} ?>><?php echo $Status->Status; ?></option>  
                <?php } ?>        
                </select>
                </div>  

<script>
$( ".select4" ).select2( { placeholder: "Select a State", maximumSelectionSize: 6,language: "he" } );
</script>