<?php

require_once '../../app/init.php';

$userid = Auth::user()->id;
$time = date('Y-m-d G:i:s');
$option = $_POST['option'];

$segments = explode(':', $option);

$Status = array_shift ($segments);
$ListId = array_shift ($segments);	


	DB::table('leads')
        ->where('id', $ListId)
        ->update(array('Status' => $Status));



?>
