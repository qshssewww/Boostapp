<?php

require_once '../../app/init.php';
require_once '../Classes/Token.php';

if (!isset($_POST['TokenId'])) {
    throw new InvalidArgumentException('Wrong Token ID');
}

$TokenId = $_POST['TokenId'];

$tokenModel = Token::getById($TokenId);
if (!$tokenModel) {
    throw new InvalidArgumentException('Wrong Token ID');
}

$CardTokef = $tokenModel->Tokef;
if ($tokenModel->Type == '0' && ((int)mb_substr($CardTokef, 2) <= 12)) {
    $Month = mb_substr($CardTokef, 2);
    $Year = '20' . mb_substr($CardTokef, 0, 2);
} else {
    $Month = mb_substr($CardTokef, 0, 2);
    $Year = '20' . mb_substr($CardTokef, 2);
}

?>

<input type="hidden" name="Type" value="<?php echo $tokenModel->Type; ?>">
<!--<div class="form-group">
    <label for="NewMonth">חודש</label>
    <select name="NewMonth" id="NewMonth" class="form-control" required>
        <option value="">בחר חודש</option>
        <option value="01" <?php /*if (@$Month=='01'){ echo 'selected'; } */?>>01</option>
        <option value="02" <?php /*if (@$Month=='02'){ echo 'selected'; } */?>>02</option>
        <option value="03" <?php /*if (@$Month=='03'){ echo 'selected'; } */?>>03</option>
        <option value="04" <?php /*if (@$Month=='04'){ echo 'selected'; } */?>>04</option>
        <option value="05" <?php /*if (@$Month=='05'){ echo 'selected'; } */?>>05</option>
        <option value="06" <?php /*if (@$Month=='06'){ echo 'selected'; } */?>>06</option>
        <option value="07" <?php /*if (@$Month=='07'){ echo 'selected'; } */?>>07</option>
        <option value="08" <?php /*if (@$Month=='08'){ echo 'selected'; } */?>>08</option>
        <option value="09" <?php /*if (@$Month=='09'){ echo 'selected'; } */?>>09</option>
        <option value="10" <?php /*if (@$Month=='10'){ echo 'selected'; } */?>>10</option>
        <option value="11" <?php /*if (@$Month=='11'){ echo 'selected'; } */?>>11</option>
        <option value="12" <?php /*if (@$Month=='12'){ echo 'selected'; } */?>>12</option>
    </select>
</div>-->

<!--<div class="form-group">
    <label>שנה</label>
    <select name="NewYear" class="form-control" required>
        <?php
/*
        $starting_year = date('Y');
        $ending_year = date('Y', strtotime('+9 years'));;

        for ($starting_year; $starting_year <= $ending_year; $starting_year++) {
            echo '<option value="' . $starting_year . '" ' . ($Year == $starting_year ? ' selected' : '') . ' >' . $starting_year . '</option>';
        }
        */?>

    </select>
</div>-->

<div class="form-group">
    <label>סטטוס </label>
    <select name="Status" id="Status" class="form-control" style="width:100%;" data-placeholder="בחר סטטוס">
        <option value="0" <?php if ($tokenModel->Status == '0') {
            echo 'selected';
        } ?>>פעיל
        </option>
        <option value="1" <?php if ($tokenModel->Status == '1') {
            echo 'selected';
        } ?>>בוטל
        </option>

    </select>

</div>