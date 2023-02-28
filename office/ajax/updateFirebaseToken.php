<?php
require_once '../../app/initcron.php';
require_once '../Classes/Users.php';

if (Auth::check()) {
    $user = Users::find(Auth::user()->id);

    $tokenFirebase = $_REQUEST['tokenFirebase'] ?? $user->tokenFirebase ?? null;

    if (!empty($tokenFirebase)) {
        $user->tokenFirebase = $tokenFirebase;
        $user->save();
    }
}
