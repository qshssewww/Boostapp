<?php


class EncryptDecrypt
{
    /** @var $key string */
    private $key;

    private $iv;

    private $method;

    public function __construct()
    {
        $this->key = "Cornflakes";
        $this->iv = 2404199709041989;
        $this->method = "AES128";
    }

    public function encryption($text){
        $encryptStr = openssl_encrypt($text,$this->method,$this->key,false,$this->iv);
        $encryptStr = base64_encode($encryptStr);
        if($encryptStr != false) {
            return $encryptStr;
        }
        return null;
    }
    public function decryption($text){
        $text = base64_decode($text);
        $decryptStr = openssl_decrypt($text,$this->method,$this->key,false,$this->iv);
        if($decryptStr != false) {
            return $decryptStr;
        }
        return null;
    }
}