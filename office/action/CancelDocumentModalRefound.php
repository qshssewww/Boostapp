<?php require_once '../../app/init.php'; ?>

<?php

$TypePayment = array(
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

$TashType = array(
    "בתשלום רגיל" => "1",
    "" => "2",
    "בתשלומי קרדיט" => "3",
    "בחיוב נדחה" => "4",
    "באחר" => "5"
);


$CompanyNum = Auth::user()->CompanyNum;

$TypeId = $_POST['TypeId'];
$DocId = $_POST['DocId'];
$TypeHeader = $_POST['TypeHeader'];

$DocsInfo = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeHeader', '=', $TypeHeader)->where('TypeNumber', '=', $DocId)->where('TypeDoc', '=', $TypeId)->first();

?>

<input type="hidden" name="DocsId" value="<?php echo $DocsInfo->id; ?>">

<div class="row">
    <div class="col-4">לקוח: <?php echo $DocsInfo->Company; ?></div>
    <div class="col-4">מס מסמך: <?php echo $DocsInfo->TypeNumber; ?></div>
    <div class="col-4">ת.מסמך: <?php echo with(new DateTime($DocsInfo->UserDate))->format('d/m/Y'); ?></div>
</div>

<?php if ($TypeHeader == '320') { ?>

    <table class="table" dir="rtl">

        <thead>
        <tr>
            <td colspan="5">פירוט פריטים</td>
        </tr>
        <tr>
            <td>#</td>
            <td>פריט</td>
            <td>מחיר</td>
            <td>כמות</td>
            <td>סה"כ</td>
        </tr>
        </thead>

        <tbody>
        <?php
        $i = '1';
        $DocsListsInfo = DB::table('docslist')->where('CompanyNum', '=', $CompanyNum)->where('DocsId', '=', $DocsInfo->id)->get();
        $DocsListsSum = DB::table('docslist')->where('CompanyNum', '=', $CompanyNum)->where('DocsId', '=', $DocsInfo->id)->sum('Itemtotal');

        foreach ($DocsListsInfo as $DocsListInfo) {

            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $DocsListInfo->ItemName; ?></td>
                <td><?php echo $DocsListInfo->ItemPrice; ?></td>
                <td><?php echo $DocsListInfo->ItemQuantity; ?></td>
                <td><?php echo $DocsListInfo->Itemtotal; ?></td>
            </tr>
            <?php ++$i;
        } ?>
        </tbody>

        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><?php echo $DocsListsSum; ?></td>
        </tr>
        </tfoot>

    </table>

<?php } ?>

<table class="table" dir="rtl">

    <thead>
    <tr>
        <td colspan="5">פירוט תקבולים</td>
    </tr>
    <tr>
        <td>#</td>
        <td>סוג</td>
        <td>פרטים</td>
        <td>ת.פרעון</td>
        <td>סה"כ</td>
    </tr>
    </thead>

    <tbody>
    <?php
    $i = '1';
    $DocsListsInfo = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->where('DocsId', '=', $DocsInfo->id)->get();
    $DocsListsSum = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->where('DocsId', '=', $DocsInfo->id)->sum('Amount');
    foreach ($DocsListsInfo as $DocsListInfo) {

        if ($DocsListInfo->TypePayment == '1') {
            $DocPaymentNotes = '';
        } elseif ($DocsListInfo->TypePayment == '2') {
            $DocPaymentNotes = 'מספר המחאה ' . @$DocsListInfo->CheckNumber . ' קוד בנק ' . @$DocsListInfo->CheckBankCode . ' מספר חשבון ' . @$DocsListInfo->CheckBank . ' מספר סניף ' . @$DocsListInfo->CheckBankSnif;
        } elseif ($DocsListInfo->TypePayment == '3') {
            $DocPaymentNotes = @$DocsListInfo->BrandName . ' המסתיים ב-' . @$DocsListInfo->L4digit . ' ב-' . @$DocsListInfo->Payments . ' תשלומים ' . array_search(@$DocsListInfo->tashType, $TashType) . ', מס׳ אישור: ' . @$DocsListInfo->ACode;
        } elseif ($DocsListInfo->TypePayment == '4') {
            $DocPaymentNotes = 'מספר אסמכתא ' . @$DocsListInfo->BankNumber;
        } elseif ($DocsListInfo->TypePayment == '5') {
            $DocPaymentNotes = '';
        } elseif ($DocsListInfo->TypePayment == '6') {
            $DocPaymentNotes = '';
        } elseif ($DocsListInfo->TypePayment == '7') {
            $DocPaymentNotes = '';
        } elseif ($DocsListInfo->TypePayment == '8') {
            $DocPaymentNotes = '';
        } elseif ($DocsListInfo->TypePayment == '9') {
            $DocPaymentNotes = '';
        } else {
            $DocPaymentNotes = 'ללא פירוט';
        }


        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $TypePayment[$DocsListInfo->TypePayment]; ?></td>
            <td><?php echo $DocPaymentNotes; ?></td>
            <td><?php echo with(new DateTime($DocsListInfo->CheckDate))->format('d/m/Y'); ?></td>
            <td><?php echo $DocsListInfo->Amount; ?></td>
        </tr>
        <?php ++$i;
    } ?>
    </tbody>

    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo $DocsListsSum; ?></td>
    </tr>
    </tfoot>


</table>


<div class="alertb alert-warning">שים לב! בעת לחיצה על ביטול מסמך הלקוח יזוכה לפי פירוט התקבולים.<br>
    פעולה זו אינה ניתנת לשחזור.
</div>


<div class="ip-modal-footer">
    <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'>לא/סגור</button>
    <div class="ip-actions">
        <button type="submit" class="btn btn-danger text-white ip-close ip-closePopUp">כן, בטל מסמך</button>
    </div>
    </form>
</div>
