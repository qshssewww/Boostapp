<?php
 require_once '../../app/initcron.php';
 require_once '../Classes/ClientRegistrationFees.php';
 require_once '../Classes/RegistrationFees.php';
 require_once '../Classes/PaymentPage.php';
 require_once '../Classes/Functions.php';
 if (Auth::userCan('55')):

     $regId = $_REQUEST["reg"];
     $CompanyNum = Auth::user()->CompanyNum;
     $clientRegObj = new ClientRegistrationFees();
     $regFeeObj = new RegistrationFees();
     $clientRegistration = $clientRegObj->getClientRegByRegId($regId);
     $regFee = $regFeeObj->getSingleRegistration($regId);
     $paymentObj = new PaymentPage();
     $payment = $paymentObj->getRow($clientRegistration->purchase_page_id);
     $paymentType = "";
     if($regFee->Type == 1){
         $paymentType = "חד פעמי";
     }
     elseif ($regFee->Type == 2){
         $paymentType = "כל רכישה";
     }
     elseif ($regFee->Type == 3){
         $paymentType = "תקופתי";
         $endDate = date("d/m/Y",strtotime("+" . $regFee->Vaild . " " . Functions::getValidType($regFee->Vaild_Type), strtotime($clientRegistration->purchase_time)));
     }
     ?>

     <span class="text-center font-weight-bold"><?php echo $regFee->ItemName; ?></span>
     <hr>

     <div class="form-group" dir="rtl">
         <label for="regSelectOption">בחר פעולה לביצוע</label>
         <select class="form-control" name="regSelectOption" id="regSelectOption">
                 <option value="">בחר</option>
                 <option value="9">הצגת פרטי התשלום</option>
                 <?php if (Auth::userCan('58')){ ?>
                     <option value="1">שינוי תאריך תחילת המנוי</option>
                 <?php } ?>
                 <?php if (Auth::userCan('60')) {
                     if ($clientRegistration->status == 1) { ?>
                         <option value="2">ביטול תשלום קבוע</option>
                     <?php } else { ?>
                         <option value="3">הפעלת תשלום קבוע</option>
                     <?php }
                 }?>
         </select>
     </div>
     <hr>
     <div id="regDisplay" style="display: none;">
         <form class="ajax-form clientRegForm">
             <input type="hidden" name="clientIdReg" value="<?php echo $clientRegistration->client_id ?>">
             <input type="hidden" name="regId" value="<?php echo $clientRegistration->id ?>">
             <input type="hidden" name="regType" value="9">
             <div class="form-group">
                 <label>עמוד סליקה</label>
                 <input type="text" class="form-control" value="<?php echo $payment->Title ?? '' ?>" disabled>
             </div>
             <div class="form-group">
                 <label>אופן תשלום</label>
                 <input type="text" class="form-control" value="<?php echo $paymentType ?>" disabled>
             </div>
             <div class="form-group">
                 <label>תאריך רכישה</label>
                 <input type="text" class="form-control" value="<?php echo date("d/m/Y",strtotime($clientRegistration->purchase_update)); ?>" disabled>
             </div>
             <div class="form-group">
                 <label>תאריך התחלה</label>
                 <input type="text" class="form-control" value="<?php echo date("d/m/Y",strtotime($clientRegistration->purchase_time)); ?>" disabled>
             </div>
             <?php if(isset($endDate)){ ?>
             <div class="form-group">
                 <label>תאריך סיום</label>
                 <input type="text" class="form-control" value="<?php echo $endDate ?>" disabled>
             </div>
             <?php } ?>
             <div class="ip-modal-footer">
<!--                 <div class="ip-actions">-->
<!--                     <button type="submit" name="submit" class="btn btn-primary">--><?php //_e('main.save_changes') ?><!--</button>-->
<!--                 </div>-->
                 <button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
             </div>

         </form>
     </div>
     <div id="regStartDate" style="display: none;">
        <form class="ajax-form clientRegForm">
            <input type="hidden" name="clientIdReg" value="<?php echo $clientRegistration->client_id ?>">
            <input type="hidden" name="regId" value="<?php echo $clientRegistration->id ?>">
            <input type="hidden" name="regType" value="1">
            <input type="text" class="form-control" value="<?php echo date("d/m/Y",strtotime($clientRegistration->purchase_time)); ?>" disabled>
            <div class="form-group">
                <label>שינוי תאריך תחילת מנוי <em><?php _e('main.required') ?></em></label>
                <input type="date" class="form-control" name="regDate">
            </div>
            <div class="ip-modal-footer">
                <div class="ip-actions">
                    <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                </div>
                <button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
            </div>

        </form>
     </div>
     <div id="regCancel" style="display: none;">
     <form class="ajax-form clientRegForm">
         <input type="hidden" name="clientIdReg" value="<?php echo $clientRegistration->client_id ?>">
         <input type="hidden" name="regId" value="<?php echo $clientRegistration->id ?>">
         <input type="hidden" name="regType" value="2">
         <div class="form-group">
             האם אתה בטוח שברצונך לבטל תשלום קבוע זה?
         </div>
         <div class="ip-modal-footer">
             <div class="ip-actions">
                 <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
             </div>
             <button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
         </div>
     </form>
 </div>
     <div id="regActive" style="display: none;">
         <form class="ajax-form clientRegForm">
             <input type="hidden" name="clientIdReg" value="<?php echo $clientRegistration->client_id ?>">
             <input type="hidden" name="regId" value="<?php echo $clientRegistration->id ?>">
             <input type="hidden" name="regType" value="3">
             <div class="form-group">
                 האם אתה בטוח שברצונך להפעיל תשלום קבוע זה?
             </div>
             <div class="ip-modal-footer">
                 <div class="ip-actions">
                     <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>
                 <button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
             </div>

         </form>
     </div>
 <?php endif;
