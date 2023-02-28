<?php

// require "../server/ImgPicker.php";
require_once  "Company.php";

class FilesUpload
{
    private $StudioUrl;

    private $CompanyNum;

    private $pageCount;

    private $file;

    private $upload_dir;

    private $upload_url;

    public function __construct($file)
    {
        $company = Company::getInstance();
        $this->CompanyNum = $company->__get("CompanyNum");
        $this->StudioUrl = $company->__get("StudioUrl");
        $pages = DB::table('payment_pages')->where('CompanyNum', $this->CompanyNum)->count();
        $this->pageCount = $pages++;
        $this->file = $file;
    }

    public function uploadDocFile(){
        $this->upload_dir = dirname(__DIR__).'/files/docs/';
        $this->upload_url = '/office/files/docs/';
        $target_file = $this->upload_dir . basename($this->file["name"]);
        $ext = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $checkUpload = $this->checkIfDocs($ext);
        $name = $this->StudioUrl.'-'. $this->pageCount.'~pageDoc.' .$ext;
        if($checkUpload === true){
            return $this->uploadFile($name);
        }
        else{
            return false;
        }
    }

    public function uploadItemImage(){
        $this->upload_dir = dirname(__DIR__).'/files/items/';
        $this->upload_url = '/office/files/items/';
        $target_file = $this->upload_dir . basename($this->file["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $checkUpload = $this->checkIfImage($imageFileType);
        $name = $this->StudioUrl.'-'. $this->pageCount.'~pageImg.' .$imageFileType;
        if($checkUpload === true){
            return $this->uploadFile($name);
        }
        else{
            return false;
        }
    }

    private function checkIfImage($imageFileType){
        $allowed = array('gif', 'png', 'jpg', "jpeg", 'GIF', 'PNG', 'JPG', "JPEG");
        if(!in_array($imageFileType, $allowed)){
            return false;
        }
        $check = getimagesize($this->file["tmp_name"]);
        if($check !== false) {
            $uploadOk = true;
        } else {
            return false;
        }
        return $uploadOk;
    }

    private function checkIfDocs($ext){
        $allowed = array('pdf', 'docx', 'doc', "txt", "odt", 'PDF', 'DOCX', 'DOC', "TXT", "ODT");
        if(!in_array($ext, $allowed)){
            return false;
        }
        return true;
    }

    private function uploadFile($name){
        if(!is_dir($this->upload_dir)){
            mkdir($this->upload_dir);
        }
        if (move_uploaded_file($this->file["tmp_name"], $this->upload_dir . $name)) {
            return $this->upload_dir . $name;
        }
        else{
            return false;
        }
    }
}