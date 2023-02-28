<?php 

require_once '../app/init.php';
require_once "Classes/Video.php";
require_once "Classes/Company.php";
require_once "Classes/VideoFolder.php";

if (Auth::guest()) exit;
if (Auth::check()){
    if (Auth::userCan('31')) {
        $postdata = file_get_contents("php://input");
        $newVideo = json_decode($postdata);
        $companyNum = Company::getInstance()->__get("CompanyNum");

        $affected;
        $result;
        if(!is_numeric($newVideo->folderId)){
            $videoFolder= new VideoFolder();
            $videoFolderId=$videoFolder->addVideoFolder((object)[
                "name"=>$newVideo->folderId,
                "CompanyNum"=>$companyNum,
                "display"=>1,
                "showForAll"=>1
            ]);
            $newVideo->folderId=$videoFolderId;
        }
        if($newVideo->id !== ''):
            $affected = Video::updateVideo($newVideo);
            $result = ['operation' => 'update' , 'result' => $affected ];
        else:
            $newVideo->companyNum = $companyNum;
            $affected = Video::addVideo($newVideo);
            $result = ['operation' => 'insert' , 'result' => $affected ];
        endif;

        echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }
}
  
?>