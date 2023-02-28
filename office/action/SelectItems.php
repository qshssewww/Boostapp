<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;

$Items = DB::table('items')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->select('id as id', 'ItemName as text')->get();
echo '{"results": '.json_encode($Items).'}';

?>