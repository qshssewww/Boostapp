<?php require_once '../../app/init.php'; ?>

<?php

$ContactId = $_POST['ContactId'];
$CompanyNum = Auth::user()->CompanyNum;
$Contact = DB::table('clientcontact')->where('id', '=' , $ContactId)->where('CompanyNum', '=' , $CompanyNum)->first();

?>

               <div class="form-group">
              <label>תפקיד\מחלקה</label>
               <input name="JobsRole" type="text" class="form-control" id="JobsRole" value="<?php echo $Contact->JobsRole ?>">
               
              </div>
              
              <div class="form-group">
              <label>שם איש קשר <em><?php _e('main.required') ?></em></label>
              <input name="ContactName" type="text" class="form-control" id="ContactName" value="<?php echo $Contact->ContactName ?>">
              </div>
              
              <div class="form-group">
              <label>נייד <em><?php _e('main.required') ?></em></label>
              <input name="ContactMobile" type="text" class="form-control" id="ContactMobile" onkeypress='validate(event)' value="<?php echo $Contact->ContactMobile ?>">
              </div>
              

              
              <div class="form-group">
              <label>טלפון</label>
              <input name="ContactPhone" type="text" class="form-control" id="ContactPhone" onkeypress='validate(event)' value="<?php echo $Contact->ContactPhone ?>">
              </div>

              <div class="form-group">
              <label>פקס</label>
              <input name="ContactFax" type="text" class="form-control" id="ContactFax" onkeypress='validate(event)' value="<?php echo $Contact->ContactFax ?>">
              </div>
                            
              <div class="form-group">
              <label>דואר אלקטרוני</label>
              <input name="ContactEmail" type="text" class="form-control" id="ContactEmail" value="<?php echo $Contact->ContactEmail ?>">
              </div>

               