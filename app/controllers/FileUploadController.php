<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../office/Classes/Uploads.php';
require_once __DIR__ . '/../../office/services/FileUploadService.php';
require_once __DIR__ . '/../../app/controllers/responses/BaseResponse.php';

class FileUploadController extends BaseController{

    
    /**
     * ClientDocumentUpload function
     * @param int $clientId
     * @return void
     */
    public function ClientDocumentUpload(int $clientId){
        $controllerResponse = array();
        if(!empty($_FILES)){
            foreach($_FILES as $File){
                if(!$fileError =  FileUploadService::isFileHasErrors($File)){
                    $fileCategory = FileUploadService::getFileTypeCategory($File);

                    if($fileCategory === FileUploadService::CATEGORY_FILE_IMAGES){ // images
                        $uploadResponse = FileUploadService::uploadImageFile($File, $clientId, 3145728);
                        array_push($controllerResponse, $uploadResponse);
                    }

                    if($fileCategory === FileUploadService::CATEGORY_FILE_DOCUMENTS){ // documents
                        $uploadResponse = FileUploadService::uploadDocumentFile($File, $clientId);
                        array_push($controllerResponse, $uploadResponse);
                    }

                    if(empty($fileCategory)){
                        $uploadResponse = new BaseResponse();
                        $uploadResponse->setError(lang('file_Incompatible'));
                        array_push($controllerResponse, $uploadResponse);    
                    }

                }else{
                    $uploadResponse = new BaseResponse();
                    $uploadResponse->setError($fileError);
                    array_push($controllerResponse, $uploadResponse);
                }
            }
        }else{
            $response = new BaseResponse();
            $response->setError(lang('no_file_received'));
            array_push($controllerResponse, $response);
        }
        echo json_encode($controllerResponse);
    }
}