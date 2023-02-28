<?php
require_once '../../app/initcron.php';

if (@$_REQUEST['q'] != '') {
$Cities = DB::table('cities')->where('City','like', '%'.@$_REQUEST['q'].'%')->select('CityId as id', 'City as text')->get();
echo '{"results": '.json_encode($Cities).'}';
}

else {
echo '{"results": []}';
}
?>