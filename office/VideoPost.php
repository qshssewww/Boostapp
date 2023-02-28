<?php
require_once '../app/init.php';
require_once "Classes/VideoFolder.php";
require_once "Classes/Video.php";
require_once "Classes/Company.php";
require_once "Classes/Users.php";
header('Content-Type: text/html; charset=utf-8');
if (Auth::guest()) exit;

if (Auth::check()) {
    if (Auth::userCan('31')) {
        $company = Company::getInstance(false);
        $folderObj = new VideoFolder();
        $folders = $folderObj->getCompanyFolders($company->__get("CompanyNum"));
        $data = array(
            "data" => array()
        );
        if ($folders) {
            foreach ($folders as $folder) {
                /**
                 * @var $folder VideoFolder
                 */
                $show = $folder->__get('display');
                if ($show != 1) {
                    continue;
                }
                $videos = $folder->getFolderVideos();
                if ($videos == null) {
                    continue;
                }
                $arrData = array();
                $arrData[] = '<div class="folderRow" data-value="' . $folder->__get("id") . '"><strong>' . $folder->__get("name") . '</strong></div>';
                $arrData[] = "";
                $arrData[] = "";
                $arrData[] = "";
                $arrData[] = "";
                $arrData[] = "";
                $arrData[] = "";
                $arrData[] = "<div><i class='folderIcon fas fa-chevron-up'></i></div>";
                array_push($data["data"], $arrData);
                foreach ($videos as $video) {
                    /**
                     * @var $video Video
                     */
                    $datetime = new DateTime($video->__get("date"));
                    $guide = Users::find($video->__get("guide"));
                    $statusCheck = ($video->__get("display") == 1) ? "checked" : "";
                    $arrData = array();
                    $arrData[] = '<i class="fas fa-grip-vertical"></i>';
                    $arrData[]='<label class="switch" >
                                    <input class="toggleDisplay" type="checkbox" '.$statusCheck.'>
                                    <span class="slider round"></span>
                                </label>';
                    $arrData[] =  '<div class="videoRow" data-folder="' . $folder->__get("id") . '" data-video="' . $video->__get("id") . '">' . $video->__get("name") . '</div>';
                    $arrData[] =  ($guide && $guide->UploadImage) ?
                        '<div class="d-flex"><img class="imgBoxImageGuide" src="https://login.boostapp.co.il/camera/uploads/large/' . $guide->UploadImage . '"/> <div class="pis-10">' . $guide->display_name.'</div></div>' :
                        '<div class="d-flex"><img class="imgBoxImageGuide" src="/office/assets/img/default-avatar.png"/> <div class="pis-10">' . ($guide->display_name ?? '') .'</div></div>';
                    $arrData[] = '<i class="far fa-calendar-alt"></i> ' . $datetime->format('d-m-Y');
                    $arrData[] =  $video->__get("time");
                    $arrData[] = '<a class="fa-link-a" href="' . $video->__get("externalLink") . '" target="_blank"><i class="fas fa-link"></i></a>';
                    $arrData[] = '<div class="library-dots-btn" data-id="' .  $video->__get("id") . '">
                                        <i class="fas fa-ellipsis-h"></i>
                                        <div class="rowBox">
                                            <div id="rowBoxEdit" class="rowBox-item">עריכה</div>
                                            <div id="rowBoxPause" class="rowBox-item">השהיה</div>
                                            <div id="rowBoxDel" class="rowBox-item">מחיקה</div>
                                        </div>
                                   </div>';
                    array_push($data["data"], $arrData);
                }
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
