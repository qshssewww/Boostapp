<?php
require_once '../../app/init.php';
require_once '../Classes/ClientActivities.php';

if (empty($_POST['activities_ids'])) {
    echo '<div class="text-info py-10"> '.lang('not_found_receipt_refund').' </div>';
    exit;
} else {
    $activitiesIdsArr = (array)$_POST['activities_ids'];
    $client = new Client((int)$_POST['client_id']);
    $studioSettings = Company::getInstance(false);

    $activitiesArr = [];
    $docsidsArr = $docsList = [];
    foreach ($activitiesIdsArr as $key => $activityId) {
        $activityDocs = [];

        $activity = ClientActivities::find($activityId);

        if ($activity) {
            $activitiesArr[] = $activity;
            if (!empty($activity->__get("ReceiptId"))) {
                $reciepts = json_decode($activity->__get("ReceiptId"), true);
                foreach ($reciepts['data'] as $index => $val) {
                    if ($val['DocId'] != "0") {
                        $docsidsArr[] = $val['DocId'];
                    }
                }

                $activityDocs = DB::table('docs')->whereIn('id', $docsidsArr)->where('ClientId', '=', $client->__get('id'))->where('TypeShva', '=', $studioSettings->TypeShva)
                    ->where('Refound', '=', 0)->whereIn('TypeHeader', array(320, 400))->get();
            }
        }

        foreach ($activityDocs as $d) {
            $alreadyExists = false;
            foreach ($docsList as $clientActivityId => $docs) {
                if (in_array($d->id, array_column($docs, 'id'))) {
                    $alreadyExists = true;
                    break;
                }
            }

            if (!$alreadyExists) {
                $docsList[$activityId][] = $d;
            }
        }
    }

    if (empty($docsList)) {
        echo '<div class="text-info py-10"> '.lang('not_found_receipt_refund').' </div>';
        exit;
    }

    $i = 0;
    foreach ($docsList as $clientActivityId => $docs) {
        foreach ($docs as $doc) {
            $docs_payment = DB::table('docs_payment')
                ->where('DocsId', $doc->id)
                ->where('CompanyNum', '=', $doc->CompanyNum)
                ->whereNotNull('PayToken')
                ->whereNotNull('YaadCode')
                ->where('TypePayment', '=', 3)
                ->orderBy('Payments', 'DESC')
                ->first();
            if (!empty($docs_payment)) {
                $DocPaymentNotes = $docs_payment->BrandName . ' המסתיים ב-' . $docs_payment->L4digit . ' ב-' . $docs_payment->Payments . ' תשלומים , C: ' . $docs_payment->ACode;
                $amount = $doc->Refound == 2 && $doc->refundAmount > 0 ? abs($doc->Amount) - $doc->refundAmount : $doc->Amount;
                ?>
                <tr data-client-activity-id="<?= $clientActivityId ?>">
                    <th scope="row">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="doc_id-<?php echo $i ?>" name="doc_id"
                                   value="<?php echo $doc->id ?>" data-amount="<?= (-1) * $doc->Amount ?>">
                            <label class="custom-control-label" for="doc_id-<?php echo $i ?>"></label>
                        </div>
                    </th>
                    <td><?php echo $doc->TypeNumber ?></td>
                    <td><?php ?><?php echo $DocPaymentNotes ?></td>
                    <td><?php echo date("d/m/Y", strtotime($doc->UserDate)) ?></td>
                    <td id="amount<?php echo $doc->id ?>"><span class="unicode-plaintext"><?php echo $amount ?></span></td>
                </tr>

                <?php
            }
        $i++;
        }
    }
}
?>
<script>
$(document).ready(function() {
    $('.js-refundMeshulamDiv').hide();

    $('input[name="doc_id"]').on('change', function() {
        if($(this).is(':checked')) {
            $('.js-refundMeshulamDiv').show();
            $('#meshulam_docs_table_lbl').hide();

            let amount = (+$(this).attr('data-amount')).toFixed(2);
            $(document).find('#meshulamRefundAmount').val(amount);
        } else {
            $('.js-refundMeshulamDiv').hide();
        }
    });
});
</script>