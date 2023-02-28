<?php

require_once __DIR__ . '/../app/initcron.php';
require_once __DIR__ . '/Classes/Token.php';

$ClientId = $_REQUEST['ClientId'];
$CompanyNum = Auth::user()->CompanyNum;
$ClientInfo = DB::table('client')->where('id', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->first();
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

$TypeShva = $SettingsInfo->TypeShva;
$MeshulamAPI = $SettingsInfo->MeshulamAPI;
$MeshulamUserId = $SettingsInfo->MeshulamUserId;
$LiveMeshulam = $SettingsInfo->LiveMeshulam;

$tokensList = [];
$parent = null;

if ($ClientInfo) {
    $ClientIdForToken = $ClientInfo->id;

    if ($ClientInfo->parentClientId) {
        $parent = DB::table('client')->where('id', $ClientInfo->parentClientId)->select('id', 'CompanyName')->first();

        $ClientIdForToken = $parent->id;
    }

    $tokensList = Token::where('ClientId', '=', $ClientIdForToken)
        ->where('CompanyNum', '=', $CompanyNum)
        ->where('Status', '=', '0')
        ->where('Type', '=', $TypeShva)
        ->where('Private', '=', 0)
        ->get();
}

?>

<div id="ChangeTokenI">
    <select name="CC3" id="CC3" class="form-control input-lg">
        <option value="">בחר טוקן</option>
        <?php

        if ($tokensList) {
            foreach ($tokensList as $token) { ?>
                <option value="<?php echo @$token->id; ?>"><?php if ($token->Type == '0') {
                        echo substr($token->Token, -4) . '****';
                    } else {
                        echo $token->L4digit . '****';
                    } ?> :: <?php if ($token->Type == '0') {
                        echo substr($token->Tokef, -2); ?>/<?php echo substr($token->Tokef, 0, 2);
                    } else {
                        echo substr($token->Tokef, 0, 2); ?>/<?php echo substr($token->Tokef, -2);
                    } ?>
                    <?php echo !empty($parent) ? ' (' . $parent->CompanyName . ')' : ''; ?>
                </option>
            <?php
            }
        }
        ?>
    </select>

</div>
<script>
    <?php if (isset($ClientInfo->CompanyId)) { ?>
        $('#CCId').val('<?php echo $ClientInfo->CompanyId; ?>');
    <?php } else { ?>
        $('#CCId').val('');
    <?php } ?>
</script>
