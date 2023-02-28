<?php

require_once '../../app/init.php';

if (isset($_GET['company_id'])) {

    $clientresult = $_GET['company_id'];
    $CompanyNum = Auth::user()->CompanyNum;

    $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

    if ($SettingsInfo->BusinessType == '5' || $SettingsInfo->BusinessType == '6') {
        $Docs = DB::table('docs')->where('ClientId', '=', $clientresult)->where('CompanyNum', '=', $CompanyNum)->where('TypeHeader', '=', '300')->whereIn('PayStatus', array(0, 1, 2))->orderBy('id', 'ASC')->get();
    } else {
        $Docs = DB::table('docs')->where('ClientId', '=', $clientresult)->where('CompanyNum', '=', $CompanyNum)->where('TypeHeader', '=', '305')->whereIn('PayStatus', array(0, 1, 2))->orderBy('id', 'ASC')->get();
    }

    $resultcount = count($Docs);

    foreach ($Docs as $k => $Doc) {

        if ($Doc->BalanceAmount > '0') {
            $totalfix2 = $Doc->BalanceAmount;
        } else {
            $totalfix2 = $Doc->Amount;
        }

        $newidclient = $Doc->id . ':' . $totalfix2 . ':1';

        if ($Doc->TypeHeader == '305') {
            $ItemText = 'חשבונית מס';
        } else {
            $ItemText = 'חשבונית עסקה';
        }
?>
     <div class="custom-control custom-checkbox mb-3">
         <input name="invoicenum[]" id="invoicenum-<?= $k ?>" type="checkbox" value="<?= $newidclient ?>" data-weight="<?= $totalfix2 ?>" class="custom-control-input CloseCheckBoxPayment">
         <label class="custom-control-label" for="invoicenum-<?= $k ?>">
             <?= $Doc->Company ?> :: <?= $ItemText ?> מספר <?= $Doc->TypeNumber ?> :: <?= $totalfix2 ?>
         </label>
     </div>

    <input name="invoicenum[]" id="invoicenum" type="hidden" data-weight="0" value="0">
    <input name="newclientid" id="newclientid" type="hidden" data-weight="0" value="<?= $newidclient ?>">
  <?php
    }
    ?>
    <input type="hidden" value="0" id="FinalinvoiceId" name="FinalinvoiceId"/>
    <input name="invoicenum[]" id="invoicenum" type="hidden" data-weight="0" value="0">

    <?php
    if ($resultcount == '1') {
        ?>
        <div style='font-weight:bold;'><strong>סך החשבוניות שנבחרו:</strong> <span id='total'
                                                                                   style='font-weight:bold; color:red;'>0</span>
        </div>

        <script type='text/javascript'>
            (function () {
                var totalEide = document.getElementById('TotalHide2');
                var totalEide2 = document.getElementById('TotalHide7');
                var totalEide8 = document.getElementById('TotalHide8');
                var NikuyMsBamakorHTML = document.getElementById('NikuyMsBamakorHTML');
                var cleanmas = document.getElementById('cleanmas');
                var totalHide = document.getElementById('totalHide');
                var totalEl = document.getElementById('total'),
                    total = 0,
                    totalNikoy = document.getElementById('NikuyMsBamakor').value,
                    checkboxes = document.AddDocs['invoicenum[]'],
                    handleClick = function () {
                        total += parseFloat(this.getAttribute('data-weight'), 10) * (this.checked ? 1 : -1);
                        $('#Finalinvoicenum').val(parseFloat(total).toFixed(2));
                        $('#TrueFinalinvoicenum').val(parseFloat(total).toFixed(2));
                        if (total == '0' || total == '0.00') {
                            $('#ShowPaymentDiv').hide();
                        } else {
                            $('#ShowPaymentDiv').show();
                        }
                        var values = [];
                        $.each($("input[name='invoicenum[]']:checked"), function () {
                            values.push($(this).val());
                            // or you can do something to the actual checked checkboxes by working directly with  'this'
                            // something like $(this).hide() (only something useful, probably) :P
                        });
                        $('#FinalinvoiceId').val(values);


                        //document.getElementById('amount1').value ='';
                        document.getElementById('TotalFinal').innerHTML = parseFloat(total).toFixed(2);
                        totalEl.innerHTML = parseFloat(total).toFixed(2);
                        totalEide2.innerHTML = parseFloat(total - (totalNikoy * total / 100)).toFixed(2);
                        totalEide8.innerHTML = parseFloat(totalNikoy * total / 100).toFixed(2);
                        NikuyMsBamakorHTML.innerHTML = parseFloat(totalNikoy).toFixed(2);
                        totalEide.value = parseFloat(total).toFixed(2);
                        cleanmas.value = parseFloat(totalNikoy * total / 100).toFixed(2);
                        totalHide.value = parseFloat(total).toFixed(2);

                    },
                    i, l
                ;

                for (i = 0, l = checkboxes.length; i < l; ++i) {
                    checkboxes[i].onclick = handleClick;
                }
            }());


        </script>
        <?php

    } else { ?>
        <div style='font-weight:bold;'><strong>סך החשבוניות שנבחרו:</strong> <span id='total'
                                                                                   style='font-weight:bold; color:red;'>0</span>
        </div>

        <script type='text/javascript'>
            (function () {
                var totalEide = document.getElementById('TotalHide2');
                var totalEide2 = document.getElementById('TotalHide7');
                var totalEide8 = document.getElementById('TotalHide8');
                var NikuyMsBamakorHTML = document.getElementById('NikuyMsBamakorHTML');
                var cleanmas = document.getElementById('cleanmas');
                var totalHide = document.getElementById('totalHide');
                var totalEl = document.getElementById('total'),
                    total = 0,
                    totalNikoy = document.getElementById('NikuyMsBamakor').value,
                    checkboxes = document.AddDocs['invoicenum[]'],
                    handleClick = function () {
                        total += parseFloat(this.getAttribute('data-weight'), 10) * (this.checked ? 1 : -1);
                        $('#Finalinvoicenum').val(parseFloat(total).toFixed(2));
                        $('#TrueFinalinvoicenum').val(parseFloat(total).toFixed(2));
                        if (total == '0' || total == '0.00') {
                            $('#ShowPaymentDiv').hide();
                        } else {
                            $('#ShowPaymentDiv').show();
                        }
                        var values = [];
                        $.each($("input[name='invoicenum[]']:checked"), function () {
                            values.push($(this).val());
                            // or you can do something to the actual checked checkboxes by working directly with  'this'
                            // something like $(this).hide() (only something useful, probably) :P
                        });
                        $('#FinalinvoiceId').val(values);

                        //document.getElementById('amount1').value ='';
                        document.getElementById('TotalFinal').innerHTML = parseFloat(total).toFixed(2);

                        totalEide2.innerHTML = parseFloat(total - (totalNikoy * total / 100)).toFixed(2);
                        totalEide8.innerHTML = parseFloat(totalNikoy * total / 100).toFixed(2);
                        totalEl.innerHTML = parseFloat(total).toFixed(2);
                        NikuyMsBamakorHTML.innerHTML = parseFloat(totalNikoy).toFixed(2);
                        totalEide.value = parseFloat(total).toFixed(2);
                        cleanmas.value = parseFloat(totalNikoy * total / 100).toFixed(2);
                        totalHide.value = parseFloat(total).toFixed(2);
                    },
                    i, l
                ;
                for (i = 0, l = checkboxes.length; i < l; ++i) {
                    checkboxes[i].onclick = handleClick;
                }
            }());


        </script>
        <?php
    }
    exit;
}
