<?php require_once '../../app/init.php'; ?>

<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('automation')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ItemId)->first();


?>

<div class="form-group">
    <label>בחר מנוי</label>
    <select class="form-control" name="Value" required>
        <option value="">בחר</option>
        <?php

        $Activities = DB::table('items')
            ->where('CompanyNum', '=', $CompanyNum)
            ->whereIn('Department', array(1, 2, 3))
            ->where('Status', '=', 0)
            ->where('isPaymentForSingleClass', 0)
            ->orderBy('Department', 'ASC')
            ->get();

        foreach ($Activities as $Activitie) {
            $membership_type = DB::table('membership_type')
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('id', '=', $Activitie->MemberShip)
                ->first();

            if (!$membership_type || $Activitie->MemberShip == 'BA999') {
                $Type = lang('no_membership_type');
            } else {
                $Type = $membership_type->Type;
            }

            ?>
            <option value="<?php echo $Activitie->id ?>" <?php if ($Items->Value == $Activitie->id) {
                echo 'selected';
            } else {
            } ?> ><?php echo $Type; ?> :: <?php echo $Activitie->ItemName; ?> -
                ₪<?php echo $Activitie->ItemPrice; ?></option>
        <?php } ?>
    </select>
</div>

<div class="form-group" dir="rtl">
    <label>אופי חישוב תוקף</label>
    <select name="VaildType" id="VaildType" class="form-control" style="width:100%;" data-placeholder="בחר">
        <option value="0" <?php if ($Items->VaildType == '0') {
            echo 'selected';
        } else {
        } ?> >לפי תאריך הרכישה/תאריך תחילת המנוי
        </option>
        <option value="2" <?php if ($Items->VaildType == '2') {
            echo 'selected';
        } else {
        } ?> >לפי תוקף מנוי קודם
        </option>
        <option value="5" <?php if ($Items->VaildType == '5') {
            echo 'selected';
        } else {
        } ?> >לפי תאריך שיעור ראשון
        </option>
    </select>
</div>


<div class="form-group">
    <label>סטטוס </label>
    <select name="Status" id="Status" class="form-control" style="width:100%;" data-placeholder="בחר סטטוס">
        <option value="0" <?php if ($Items->Status == '0') {
            echo 'selected';
        } else {
        } ?>>מוצג
        </option>
        <option value="1" <?php if ($Items->Status == '1') {
            echo 'selected';
        } else {
        } ?>>מוסתר
        </option>

    </select>

</div>