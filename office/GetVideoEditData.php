<?php 

require_once '../app/init.php';
require_once "Classes/VideoFolder.php";
require_once "Classes/Video.php";
require_once "Classes/Users.php";
require_once "Classes/Company.php";


if (Auth::guest()) exit;
if (Auth::check()){
    if (Auth::userCan('31')) {

        $companyNum = Company::getInstance()->__get("CompanyNum");
        $postdata = file_get_contents('php://input');
        $video_id = json_decode($postdata)->id;
        
        $video = new Video();
        if(!empty($video_id)){
            $video = $video->getVideo($video_id);
        }

        $videoFolder = new VideoFolder();
        $videoFolder->__set('CompanyNum',$companyNum);
        $videoFolders = $videoFolder->getFolders();

        $users = new Users();
        $users->__set('CompanyNum',$companyNum);
        $coachers = $users->getCoachers();

        $data = [
            'video' => $video,
            'videoFolders' => $videoFolders,
            'coachers' => $coachers,
        ];

        echo json_encode($data,JSON_UNESCAPED_UNICODE);

    }
}


       


?>