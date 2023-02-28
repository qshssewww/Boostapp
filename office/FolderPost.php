<?php
require_once '../app/init.php';
require_once "Classes/VideoFolder.php";
require_once "Classes/Video.php";
require_once "Classes/VideoLimit.php";
require_once "Classes/Company.php";
require_once "Classes/Users.php";
header('Content-Type: text/html; charset=utf-8');
if (Auth::guest()) exit;

if (Auth::check()){
    if (Auth::userCan('31')){
        //setup for all
        $company = Company::getInstance();
        $membershipTypes = $company->getMembershipTypes();
        $MembershipTypes = [];
        foreach ($membershipTypes as $item){
            $MembershipTypes[] = $item->createArrFromObj();
        }
        $folderObj = new VideoFolder();
        $videoLimit = new VideoLimit();
        $postdata = file_get_contents("php://input");
        $obj = json_decode($postdata);
        function CreateBasicReturnArray($folder = null){
            global $videoLimit;
            global $MembershipTypes;
            if ($folder == null){
                $statusCheck = "checked";
                $showForAll = "checked";
                $folderId = "template";
                $folderName = "<?php echo lang('new_folder_vod') ?>";
                $folderVideoLimits = null;
                $col2Class="dontToggleDisplay";
                $col5='<button class="cancelAdd deleteInputStyle"><i class="fas fa-times" aria-hidden="true"></i></button>';
            }else{
                $statusCheck = ($folder->__get("display") == 1) ? "checked" : "";
                $showForAll = ($folder->__get("showForAll") == 1) ? "checked" : "";
                $folderId = $folder->__get("id");
                $folderName = $folder->__get("name");
                $folderVideoLimits = $videoLimit->getItemByFolderId($folderId);
                $col2Class="toggleDisplay";
                $col5='<button class="deleteFolder deleteInputStyle"><i class="fa fa-trash" aria-hidden="true"></i></button>';
            }
            $arrData = array();
            $arrData["col1"] = '<i class="fas fa-grip-vertical"></i>';
            $arrData["col2"] = '<label class="switch" >
                             <input class="'.$col2Class.' " type="checkbox" ' . $statusCheck . '>
                             <span class="slider round"></span>
                          </label>';
            $arrData["col3"] = '<input type="text" class="folderRow" data-value="' . $folderId . '" value="' . $folderName . '" disabled /><button class="editName deleteInputStyle"> <i class="fas fa-pencil-alt"></i></button>';
            // $arrData["col4"] = '<input name="showForAll" class="showForAll" type="checkbox" ' . $showForAll . ' ><label for="showForAll">מוצג לכולם</label>';
            $arrData["col4"] ='<label class="m-0 checkbox-container link-label">
                                    <div class="name">'.lang('vod_set_permi').'</div>
                                    <input type="checkbox" class="showForAll" ' . $showForAll . '  >
                                <span class="checkmark"></span>
                         </label>';
            $arrData["col5"] = $col5;
            $arrData["id"] = $folderId;
            $arrData['membershipTypes'] = $MembershipTypes;
            $arrData['videoLimits'] = $folderVideoLimits;
            return $arrData;

        }
        //see if input
        if ($obj){
            if ($obj->id != "template"){
                $singleFolder=$folderObj->getVideoFolderObect($obj->id);
                if($singleFolder){
                    $data=CreateBasicReturnArray($singleFolder);
                }else{
                    $data=null;
                }
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            }else{
                $data=CreateBasicReturnArray();
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            }

        }else{
            $folders = $folderObj->getCompanyFolders($company->__get("CompanyNum"));
            $data = array(
                "data" => array()
            );
            if($folders){
                foreach ($folders as $folder){
                    /**
                     * @var $folder VideoFolder
                     */
                    $arrData=CreateBasicReturnArray($folder);
                    array_push($data["data"], $arrData);
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }
}

