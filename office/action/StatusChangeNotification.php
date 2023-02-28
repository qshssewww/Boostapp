<?php

require_once '../../app/init.php';

$Acts = $_REQUEST['Act'];

$segments = explode(':', $Acts);

$EventId = array_shift ($segments);
$NewStatus = array_shift ($segments);

///  קבלת סטטוס ישן

$Clients = DB::table('clientreminder')->where('id', '=', $EventId)->first(); 

/// בצע פעולה לפי ססטוס ישן
if ($NewStatus=='0')
{
	
			DB::table('notification')
		   ->where('id', $EventId)
           ->update(array('Status' => '0'));
				
	
}
else if ($NewStatus=='1')
{

	
	$CloseDate = date('Y-m-d H:i:s');
	$CloseUser = Auth::user()->id;
	
			DB::table('notification')
		   ->where('id', $EventId)
           ->update(array('Status' => '1','CloseUser' => $CloseUser ,'CloseDate' => $CloseDate));
				
				
}



/// שלח הודעת עדכון פעולה
echo 'בוצע בהצלחה';

?>