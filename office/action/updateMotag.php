<?php require_once '../../app/init.php'; ?>

<?php

$MotagId = $_POST['MotagId'];

$Motag = DB::table('motag')->where('MotagID', '=' , $MotagId)->first();

?>

                <div class="form-group" dir="rtl">
                <label>מותג</label>
                <input type="text" name="MotagName" id="MotagName" class="form-control" placeholder="כותרת מותג" value="<?php echo htmlspecialchars($Motag->Motag); ?>">
                </div>  
                
                <div class="form-group" dir="rtl">
                <label>כותרת באתר</label>
                <input type="text" name="MotagSite" id="MotagSite" class="form-control" placeholder="כותרת באתר" value="<?php echo htmlspecialchars($Motag->MotagSite); ?>">
                </div>    

  <div class="checkbox">
    <label style="padding-right:25px;">
      <input type="checkbox" class="pull-right" name="ShowPOS" <?php if ($Motag->Status=='0'){ echo 'checked'; } else {} ?>> הצג בקופה?
    </label>
  </div>
  
  <div class="checkbox">
    <label style="padding-right:25px;">
      <input type="checkbox" class="pull-right" name="ShowSite" <?php if ($Motag->StatusSite=='0'){ echo 'checked'; } else {} ?>> הצג באתר?
    </label>
  </div>