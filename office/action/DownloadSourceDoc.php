<?php
require_once '../../app/init.php';
require_once '../services/receipt/DocsService.php';
require_once '../services/LoggerService.php';

$companyNum = Auth::user()->CompanyNum ?? 0;
$docId = $_GET['docId'] ?? null;
if($docId === null) {
    echo 'param not valid';
    return;
//    throw new ErrorException('docs not valid');
}
try {
    $response = DocsService::downloadDocPdf($docId, true);
    if($response) {
        echo 'ההורדה בוצע בהצלחה! שים לב לא ניתן להוריד מקור בשנית!';
    } else {
        echo 'הפעולה נכשלה נסה שוב, או פנה לצוות תמיכה';
    }
} catch (Exception $e) {
    LoggerService::error($e, LoggerService::CATEGORY_DOCS);
    echo 'הפעולה נכשלה נסה שוב, או פנה לצוות תמיכה';
}
return;
?>




