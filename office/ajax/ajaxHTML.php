<?php
require_once '../../app/init.php';
require_once "../Classes/RegistrationFees.php";
require_once '../Classes/Client.php';
require_once '../Classes/Token.php';
require_once '../Classes/TempReceiptPaymentClient.php';
require_once '../services/LoggerService.php';
require_once '../services/receipt/ReceiptService.php';

if (!empty($_POST["fun"])) {

    switch ($_POST["fun"]) {
        case "fixedPayment":
            $regFees = new RegistrationFees();
            $res = $regFees->getFixedPaymentOfItem($_POST['companyNum'], $_POST["membership"]);
            if (count($res) > 0):
                foreach ($res as $key => $val):
                    ?>
                    <div class = "d-flex align-items-center  justify-content-between   py-7 px-10 js-fixed-payment-item">
                        <div class = "custom-control custom-checkbox  custom-control-inline mx-0 mie-10">
<!--                            //todo add checked to input (js-checkbox)-->
                            <input type="checkbox" id="js-checkbox-<?php echo $val->CompanyNum; ?>-<?php echo $val->id; ?>" name="js-checkbox" class="custom-control-input js-item-fixed-payment">
                            <label class = "custom-control-label" for = "js-checkbox-<?php echo $val->CompanyNum; ?>-<?php echo $val->id; ?>"><?php echo $val->ItemName; ?></label>
                        </div>
                        <div>
                            <h6><span class=""><?php echo $val->ItemPrice; ?></span>₪</h6>
                            <input type="hidden" class="js-fixed-item-price"  id="<?php echo $val->id;?>" value="<?php echo $val->ItemPrice;?>" />
                        </div>
                    </div>
                <?php
                endforeach;
            endif;
            break;
        case "totalPayment":
            $item_price = empty($_POST['total_price']) ? 0 : $_POST['total_price'];
            $sum = 0;
            if (!empty($_POST['selected_items'])):
                foreach ($_POST['selected_items'] as $key => $selected):
                    ?>
                    <div class="list-group-item d-flex justify-content-between">
                        <div class="js-final-item-name"><?php echo $selected["item_name"]; ?></div>
                        <div >
                            ₪<span><?php echo $selected["item_price"]; ?></span>
                            <input type="hidden"  name="<?php echo $selected["item_name"];?>"  id="<?php echo $selected["id"]?>"  value="<?php echo $selected["item_price"]; ?>" class="js-final-item-price" />
                        </div>
                    </div>
                    <?php
                    $sum += $selected["item_price"];
                endforeach;
            endif;
            $total = $item_price + $sum;
            ?>
            <div class="list-group-item d-flex justify-content-between">
                <h6><strong><?php echo lang('table_total_to_pay')?></strong></h6>
                <h6>₪<strong><?php echo $total; ?></strong></h6>
                <input type="hidden" class="js-final-pay-amount" value="<?php echo $total; ?>" />
            </div>
        <?php break;
        case "addPaymentRow":
            $errorMessage = null;
            $typePayment = array(
                1 => "מזומן",
                3 => "כרטיס אשראי",
                2 => "המחאה",
                4 => "העברה בנקאית",
                5 => "תו",
                6 => "פתק החלפה",
                7 => "שטר",
                8 => "הוראת קבע",
                9 => "אחר"
            );
            $tashType = array(
                1 => "רגיל",
                3 => "תשלומים",
                2 => "קרדיט",
                4 => "חיוב נדחה",
                5 => "אחר"
            );
            if (!isset($_POST["trueFinalInvoiceNum"])) {
                $errorMessage = "trueFinalInvoiceNum required";
            } elseif (!is_numeric($_POST["trueFinalInvoiceNum"])) {
                $errorMessage = "הסכום חייב להיות מספרי";
            } elseif (!isset($_POST['tempId'])) {
                $errorMessage = "tempId required";
            } elseif (!isset($_POST['typeDoc'])) {
                $errorMessage = "typeDoc required";
            }  elseif (!isset($_POST['companyNum']))  {
                $errorMessage = "companyNum required";
            }  elseif (!isset($_POST['clientActivityId']))  {
                $errorMessage = "clientActivityId required";
            } elseif (!is_numeric($_POST["companyNum"])) {
                $errorMessage = "companyNum צריך להיות מספרי";
            } else {
                $typeDoc = $_POST['typeDoc'];
                $companyNum = $_POST['companyNum'];
                $tempId = $_POST['tempId'];
                $trueFinalInvoiceNum = $_POST["trueFinalInvoiceNum"];
                $clientActivityId = $_POST['clientActivityId'];

                $tempsPayments = TempReceiptPaymentClient::getReceiptPaymentTempByClientId($tempId, $typeDoc, $companyNum, $clientActivityId);
                ?>
                <div id="step-3-summary">
                <div class="list-group list-group-flush" id="list-payment">

               <?php
                if ($tempsPayments === null) {
                    $getAmount = 0;
                    $getExcess = 0;
                } else {
                    foreach ($tempsPayments as $tempsPayment){
                        if ($tempsPayment->TypePayment == '1') {
                            $docPaymentNotes = '';
                        } elseif ($tempsPayment->TypePayment == '2') {
                            $docPaymentNotes = 'מ.המחאה ' . @$tempsPayment->CheckNumber;
//                            $docPaymentNotes = 'מספר המחאה ' . @$tempsPayment->CheckNumber . ' קוד בנק ' . @$tempsPayment->CheckBankCode . ' מספר חשבון ' . @$tempsPayment->CheckBank . ' מספר סניף ' . @$tempsPayment->CheckBankSnif;
                        } elseif ($tempsPayment->TypePayment == '3') {
//                            $docPaymentNotes = @$tempsPayment->BrandName . ' המסתיים ב-' . @$tempsPayment->L4digit . ' ב-' . @$tempsPayment->Payments . ' תשלומים ' . array_search(@$tempsPayment->tashType, $tashType) . ', מס׳ אישור: ' . @$tempsPayment->ACode;
                            $docPaymentNotes = '(' . @$tempsPayment->L4digit . ' **** )';
                        } elseif ($tempsPayment->TypePayment == '4') {
//                            $docPaymentNotes = '(' . '3445' . ' **** )';
                            $docPaymentNotes = 'מ.אסמכתא ' . @$tempsPayment->BankNumber;
                        } elseif ($tempsPayment->TypePayment == '5') {
                            $docPaymentNotes = '';
                        } elseif ($tempsPayment->TypePayment == '6') {
                            $docPaymentNotes = '';
                        } elseif ($tempsPayment->TypePayment == '7') {
                            $docPaymentNotes = '';
                        } elseif ($tempsPayment->TypePayment == '8') {
                            $docPaymentNotes = '';
                        } elseif ($tempsPayment->TypePayment == '9') {
                            $docPaymentNotes = '';
                        } else {
                            $docPaymentNotes = 'ללא פירוט';
                        } ?>
                        <div class="list-group-item d-flex justify-content-between payment-row">
                            <div class="flex">
                                 <span>
                                    <?php if ($tempsPayment->TypePayment != 3) {  ?>
                                        <i class="fas fa-minus-circle text-danger"  onclick="ClientForm.removePaymentRow(this)"></i>
                                    <?php } echo $typePayment[$tempsPayment->TypePayment];?>
                                 </span>
                                 <div class="text-gray-400 mr-5">
                                 <span>
                                     <?php echo $docPaymentNotes;?>
                                 </span>
                            </div>
                         </div>

                            <h6>₪<span><?php echo $tempsPayment->Amount+$tempsPayment->Excess;?></span></h6>
                            <input type="hidden" class="js-payment-row" payment-value="<?php echo $tempsPayment->Amount+$tempsPayment->Excess;?>" type-payment="<?php echo $tempsPayment->TypePayment;?>" value="<?php echo $typePayment[$tempsPayment->TypePayment];?>" id-payment="<?php echo $tempsPayment->id; ?>" />
                        </div>
                        <?php
                    }
                    $getAmount = TempReceiptPaymentClient::getReceiptPaymentTempSum($tempId, $typeDoc, $companyNum,'Amount',$clientActivityId);
                    $getExcess = TempReceiptPaymentClient::getReceiptPaymentTempSum($tempId, $typeDoc, $companyNum, 'Excess',$clientActivityId);
                }
                 $moreAmount = $trueFinalInvoiceNum-$getAmount;
                ?>
                    <div class="list-group-item d-flex justify-content-between">
                        <h6><strong><?php echo lang('total_revenue')?></strong></h6>
                        <h6>₪<strong id="total-revenue-amount"><?php echo number_format((float)$getAmount+$getExcess, 2, '.', ''); ?></strong></h6>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <h6><strong><?php echo lang('remainder_of_payment')?></strong></h6>
                        <h6>₪<strong id="remainder-payment-amount"><?php echo number_format((float)$moreAmount, 2, '.', ''); ?></strong></h6>
                    </div>
                </div>
            <?php }
            if (isset($errorMessage)) {
                LoggerService::error($errorMessage, 'addPaymentRowFront');
                header('HTTP/1.0 506 error');
                die(json_encode(["Message" => $errorMessage, "Status" => "Error"]));
            }
            break;
        case "getCreditCards":
        if (!empty($_POST['clientId']) && is_numeric($_POST["clientId"]) && !empty($_POST['companyNum'])):
            $clientId = $_POST['clientId'];
            $companyNum = $_POST['companyNum'];
            $client = new Client($clientId);
            if ($parent = $client->__get("parentClientId")) {
                $clientId = $parent;
            }

            $tokens = Token::getTokens($companyNum, $clientId);

            if (count($tokens) > 0):
                foreach ($tokens as $key => $val):
                    if($key === 4) {
                        break;
                    }
                    $tokeMonth = $val->Tokef ? substr($val->Tokef, 0, 2) : '--';
                    $tokeYear = $val->Tokef ? substr($val->Tokef, 2, 2) : '--';
                    ?>
                        <div class="px-15 w-100 d-flex py-8 border-bottom border-light js-cc-item" number="<?php echo $key; ?>" id="creditCard=<?php echo $val->id; ?>">
                        <div class="w-30p">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="js-radio-payment-mode-<?php echo $val->id; ?>" name="customRadio"
                                       class="custom-control-input js-select-card" value="<?php echo $val->Token; ?>" <?php echo $key === 0? 'checked': ''?>>
                                <label class="custom-control-label"
                                       for="js-radio-payment-mode-<?php echo $val->id; ?>"></label>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-fill">
                            <div>
                                <span class="mie-10 js-cc-item-text">כרטיס אשראי: </span> <span>**** <?php echo $val->L4digit; ?></span>
                            </div>
                            <div class="text-gray-400">
                                <span class="mie-10">תוקף :</span> <span><?php echo $tokeMonth; ?>/<?php echo $tokeYear; ?></span>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            endif;
        endif;
        break;
    }
} else {
    echo json_encode(array("Message" => "No Function", "Status" => "Error"));
}
