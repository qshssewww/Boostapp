<?php

require_once 'app/init.php';

if(!isset($_POST['lang'])){
    $_SESSION['lang'] = "he";
}
else {
    $_SESSION['lang'] = $_POST['lang'];
    if(Auth::check()) {
        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['language' => $_POST['lang']]);
    }
}
