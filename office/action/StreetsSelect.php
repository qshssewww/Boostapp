<?php
require_once '../../app/initcron.php';
if (@$_REQUEST['CityId'] != '') {
$Streets = DB::table('street')->where('CityId','=', $_REQUEST['CityId'])->where('Street','like', '%'.@$_REQUEST['q'].'%')->select('id', 'Street as text')->get();
	
if (count($Streets) == '0') {
$Streets[] = ['id' => '99999999', 'text' => 'ללא רחוב'];
}

echo '{"results": '.json_encode($Streets).'}';
}
else {
echo '{"results": []}';
}
?>