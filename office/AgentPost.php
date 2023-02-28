<?php require_once '../app/initcron.php';
header('Content-Type: application/json; charset=utf-8');
if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();
$BrandsMain = $SettingsInfo->BrandsMain;

if ($BrandsMain == '0') {
    $OpenTables = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('role_id', '!=', '1')->orderBy('display_name', 'ASC')->get();
} else {
    $OpenTables = DB::table('users')->where('BrandsMain', '=', $BrandsMain)->where('role_id', '!=', '1')->orderBy('display_name', 'ASC')->get();
}
$OpenTableCount = count($OpenTables);

$data = array();

foreach ($OpenTables as $Client) {
    $verifiedMobileIcon = '';
    if($Client->multiUserId) {
        $verifiedMobileIcon = ' <i class="fas fa-shield-check text-success" title="'.lang('verified_main').'"></i></span>';
    }
    $AgentRules = DB::table('roles')->where('id', '=', $Client->role_id)->first();
    $data[] = [
        $Client->id,
        (Auth::userCan('4')) ? '<a href="AgentProfile.php?u='.$Client->id.'"><strong class="text-primary">'.htmlentities($Client->display_name).'</strong></a>' : '<strong class="text-primary">' . htmlentities($Client->display_name) . '</strong>',
        $Client->ContactMobile ? '<span>' . $Client->ContactMobile . $verifiedMobileIcon : '--',
        $Client->email ?: '--',
        $Client->LastActivity ? with(new DateTime($Client->LastActivity))->format('d/m/Y H:i:s') : '--',
        isset($AgentRules->Title) ? htmlentities($AgentRules->Title) : '--',
        (isset($Client->status) && $Client->status == '1') ? '<strong class="text-primary">' . lang('active') . '</span>' : '<strong class="text-danger">' . lang('freezed_status') . '</span>'
    ];
}

echo json_encode(array('data' => $data), JSON_UNESCAPED_UNICODE);