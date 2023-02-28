<?php header('Content-Type: text/plain; charset=utf-8');

require_once '../../office/Classes/Uploads.php';
require_once '../../app/controllers/responses/BaseResponse.php';

class FileUploadService {

    const AUTHORIZED_DOCS_TYPES = ['application/pdf'];
    const AUTHORIZED_IMAGE_TYPES = ['image/jpeg', 'image/png'];

    const CATEGORY_FILE_IMAGES = 1;
    const CATEGORY_FILE_DOCUMENTS = 2;


    /**
     * isFileHasErrors function
     * @param array $File
     * @return string|null
     */
    public static function isFileHasErrors(array $File): ?string{
        if(!isset($File['error']) || is_array($File['error'])){
            switch ($File['error']) {
                case UPLOAD_ERR_NO_FILE : return lang('no_file_received');
                case UPLOAD_ERR_INI_SIZE : return 'File exceeds the maximum fil size by php.ini';
                case UPLOAD_ERR_FORM_SIZE : return lang('file_upload_size_error');
                case UPLOAD_ERR_PARTIAL : return lang('file_error_partial');
                default: return lang('error_no_info');
            }
        } else return null;
    }
    

    /**
     * uploadImageFile function
     * @param array $File
     * @param integer $clientId
     * @param integer $maxSize write in bytes, 5242880(5MB) by defualt
     * @return BaseResponse
     */
    public static function uploadImageFile(array $File, int $clientId, int $maxSize = 5242880): BaseResponse{
        $response = new BaseResponse();
        if(self::checkFileAuthorizedType($File, self::AUTHORIZED_IMAGE_TYPES)){
            if(self::CheckFileMaxSize($File, $maxSize)){
                $newFileName = self::GenerateFileName($File, $clientId);
                $fileTransfer = self::uploadFile($File, $newFileName);

                if($fileTransfer){
                    $Upload = new Upload();
                    $Upload->__set('client_id', $clientId);
                    $Upload->__set('file_name', $newFileName);
                    $Upload->__set('display_name', self::setSafeDisplayName($File['name']));
                    $Upload->__set('type', strtoupper(pathinfo($File["name"],PATHINFO_EXTENSION)));
                    $Upload->__set('category', self::CATEGORY_FILE_IMAGES);

                    if($Upload->save()){
                        $response->setMessage(lang('file_upload_success'));
                    } else $response->setError(lang('error_oops_something_went_wrong'));
                    
                }else $response->setError(lang('error_oops_something_went_wrong'));
            }else $response->setError(lang('file_upload_size_error')); // file max size exceeded
        }else $response->setError(lang('file_Incompatible')); // file type not allowed
        
        return $response;
    }


        /**
     * uploadDocumentFile function
     * @param array $File
     * @param integer $clientId
     * @param integer $maxSize write in bytes, 5242880(5MB) by defualt
     * @return BaseResponse
     */
    public static function uploadDocumentFile(array $File, int $clientId, int $maxSize = 5242880): BaseResponse{
        $response = new BaseResponse();
        if(self::checkFileAuthorizedType($File, self::AUTHORIZED_DOCS_TYPES)){
            if(self::CheckFileMaxSize($File, $maxSize)){
                $newFileName = self::GenerateFileName($File, $clientId);
                $fileTransfer = self::uploadFile($File, $newFileName);

                if($fileTransfer){
                    $Upload = new Upload();
                    $Upload->__set('client_id', $clientId);
                    $Upload->__set('file_name', $newFileName);
                    $Upload->__set('display_name', self::setSafeDisplayName($File['name']));
                    $Upload->__set('type', strtoupper(pathinfo($File["name"],PATHINFO_EXTENSION)));
                    $Upload->__set('category', self::CATEGORY_FILE_DOCUMENTS);
                    $Upload->save();
                    
                    if($Upload->save()){
                        $response->setMessage(lang('file_upload_success'));
                    } else $response->setError(lang('error_oops_something_went_wrong'));

                }else $response->setError(lang('error_oops_something_went_wrong'));
            }else $response->setError(lang('file_upload_size_error')); // file max size exceeded
        }else $response->setError(lang('file_Incompatible')); // file type not allowed
        
        return $response;
    }


    /**
     * uploadFile function
     * @param array $File
     * @param string $fileName
     * @param string|null $target_dir
     * @return string|null
     */
    private static function uploadFile(array $File, string $fileName, ?string $target_dir = null): ?string{
        $target_dir = $target_dir ?? "../files/uploads/";
        $target_file = $target_dir . $fileName;
        if(!is_dir($target_dir)) mkdir($target_dir);
        $fileTransfer = move_uploaded_file($File['tmp_name'], $target_file);
        $target_dir = $target_dir === "../files/uploads/" ? preg_replace("/../", $target_dir, "office") : $target_dir;
        return $fileTransfer ? $target_file : null;
    }


    /**
     * checkFileAuthorizedType function
     * @param array $File
     * @param array $types use this class AUTHORIZED consts
     * @return boolean
     */
    private static function checkFileAuthorizedType(array $File, array $types): bool{
        return in_array($File['type'], $types);
    }


    /**
     * getFileTypeCategory function
     * @param array $File
     * @return int|null
     */
    public static function getFileTypeCategory(array $File): ?int{
        $extension = strtolower(pathinfo(basename($File["name"]),PATHINFO_EXTENSION));
        if(in_array($extension, ['gif', 'png', 'jpg', "jpeg", 'GIF', 'PNG', 'JPG', "JPEG"])){
            return self::CATEGORY_FILE_IMAGES;
        }
        elseif(in_array($extension, ['pdf', 'doc', 'docs', 'xls', 'xlsx', 'PDF', 'DOC', 'DOCS', 'XLS', 'XLSX'])){
            return self::CATEGORY_FILE_DOCUMENTS;
        }
        else return null;
    }


    /**
     * CheckFileMaxSize function
     * @param array $File
     * @param int|null $maxSize write in bytes, 5242880(5MB) by defualt
     * @return boolean
     */
    private static function CheckFileMaxSize(array $File, ?int $maxSize = null): bool{
        $maxSize = $maxSize ? $maxSize : 5242880; // 5MB
        $ifImageValidation = self::getFileTypeCategory($File) === FileUploadService::CATEGORY_FILE_IMAGES ? getimagesize($File['tmp_name']) : true;
        return $ifImageValidation && $File["size"] > 0 && $File["size"] <= $maxSize;
    }


    /**
     * GenerateFileName function
     * @param array $File
     * @param int $clientId
     * @return string|null
     */
    private static function GenerateFileName(array $File, int $clientId): ?string{
        if(isset($File['name'])){
            $extension = strtolower(pathinfo($File["name"],PATHINFO_EXTENSION));
            $fileName = "$clientId"."_".uniqid().".$extension";
            return $fileName;
        }else return null;
    }


    /**
     * setSafeDisplayName function
     * @param string|null $fileName
     * @return string
     */
    private static function setSafeDisplayName(?string $fileName = null): string{
        $fileName = explode(".", $fileName)[0]; // file name without extention
        $displayName = !empty($fileName) ? preg_replace("/[`~;!@#$%^&*{}<>\[\]]/", "", $fileName) : null;
        $displayName = empty($fileName) ? lang('file_single')." ".uniqid() : $displayName;
        $displayName = preg_replace("/[_-]/", " ", $displayName);
        $displayName = mb_substr($displayName, 0, 48);
        return $displayName;        
    }
}

