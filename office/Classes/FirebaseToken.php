<?php


class FirebaseToken extends \Hazzard\Database\Model
{
    const DEFAULT_TOKEN = "AAAAfalH6QE:APA91bH2R0r1DpGic-YvizEQMbGCjIIJthZMpg7zs1U3a0PQFZx_ogmZHv5hyy89gFNEmcqkBJrSVWOkI_7ZfEeSkUDSzDXHEURYqFOhuIENY4wCsuE26ypBmolz9k_c8XJ42og-m0td";

    protected $table = 'boostapp.firebase_token';

    public static function getTokenByProject($projectNum) {
        $token = self::where('project_num', $projectNum)->pluck('token');
        return $token ?? self::DEFAULT_TOKEN;
    }

}