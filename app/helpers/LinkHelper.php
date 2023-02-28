<?php

/**
 * Class LinkHelper
 */
class LinkHelper
{

    /**
     * @return string
     */
    public static function getPrefixUrlByHttpHost(): string
    {
        return isset($_SERVER["HTTP_HOST"]) ?
            $_SERVER["HTTP_HOST"] === "localhost:8000" ? "http://localhost:8000" : "https://" . $_SERVER['HTTP_HOST']
            : App::url(); //default
    }

    /**
     * @return string
     */
    public static function getAppPrefixUrlByHttpHost(): string
    {
        $response = isset($_SERVER["HTTP_HOST"]) ?
            $_SERVER["HTTP_HOST"] === "localhost:8000" ? "http://localhost:8000" : "https://" . $_SERVER['HTTP_HOST']
            : App::url(); //default
        return str_replace(array('login', 'biz'), 'app', $response);
    }

    /**
     * @param $url
     * @return bool|string
     */
    public static function getTinyUrl($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }




}