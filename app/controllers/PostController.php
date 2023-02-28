<?php
require_once __DIR__ . '/BaseController.php';

class PostController extends BaseController
{
    public function toDataJson($data){
        $this->asJson();
        return json_encode(array('data' => $data), JSON_UNESCAPED_UNICODE);
    }
}