<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;

$ChatLasts = DB::table('chat')->where('CompanyNum' ,'=', $CompanyNum)->where('Status', '=', '0')->where('Notification', '=', '0')->where('SendFrom', '=', '1')->orderBy('id', 'ASC')->limit(7)->get();
$i = '1';
$Count = count($ChatLasts);

echo '[';
foreach ($ChatLasts as $ChatLast) {
    
if (!empty($ChatLast)) {
    
$UserInfo = DB::table('client')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=', @$ChatLast->ToUserId)->first();

if(!$UserInfo) {
    continue;
}
$ChatMessageContent = array('id' => $UserInfo->id, 'name' => $UserInfo->CompanyName, 'message' => $ChatLast->Content, 'photo' => 'https://pikmail.herokuapp.com/'.$UserInfo->Email.'?size=85');
$ChatMessageContents = json_encode($ChatMessageContent);
    
echo $ChatMessageContents;
    
if ($i != @$Count) {echo ",";}
    
$i++;
		$datenow = date('Y-m-d H:i:s');
		DB::table('chat')
        ->where('id', @$ChatLast->id)
        ->where('CompanyNum' ,'=', $CompanyNum)
        ->update(array('Notification' => '1', 'NotificationTime' => $datenow));
    
}
}
echo ']';
?>