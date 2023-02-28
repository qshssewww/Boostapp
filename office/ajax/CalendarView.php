<?php
ini_Set("max_execution_time",0);
require_once '../../app/init.php';

require_once '../Classes/Company.php';

require_once '../Classes/ClassCalendar.php';
require_once '../Classes/ClassSettings.php';
require_once '../Classes/Section.php';
require_once '../Classes/Users.php';
require_once '../Classes/ClassesType.php';
require_once '../Classes/Brand.php';
require_once '../Classes/calendar.php';

header('Content-Type: application/json');
if(Auth::guest()) {
    exit;
}
$company = Company::getInstance("branch");
$CompanyNum = $company->__get("CompanyNum");

$ClassCalendar = new ClassCalendar();
$Section = new Section();
$Users = new Users();
$ClassType = new ClassesType();
$Brand = new Brand();
$ClassSettings = new ClassSettings();
$calendar = new calendar();
if (!empty($_POST["fun"])) switch ($_POST["fun"]) {
    case "GetClassesByStudioAndFloor":
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
        }
        if (empty($_POST['Floor'])) {
            echo json_encode(array("Message" => "Floor requeired", "Status" => "Error"));
        } else {
            $Classes = $ClassCalendar->GetClassesByStudioAndFloor($_POST["CompanyNum"], $_POST["Floor"]);
            echo json_encode(array('Classes' => $Classes, "Status" => "Success"));
        }
        break;
    case "GetClassesByStudioByDate":

        $res = $ClassCalendar->getCalendarData($_POST);
        echo $res;

        break;
    case "GetBranches":
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
        } else {
            $Brands = $Brand->getAllByCompanyNum($_POST['CompanyNum']);
            echo json_encode(array('Branches' => $Brands, "Status" => "Success"));
        }
        break;
    case "GetRoomsByBranch":
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
        }
        if (empty($_POST['Branch'])) {
            echo json_encode(array("Message" => "Branch requeired", "Status" => "Error"));
        } else {
            $Sections = $Section->GetRoomsByBranch($_POST['CompanyNum'], $_POST['Branch']);
            echo json_encode(array('Rooms' => $Sections, "Status" => "Success"));
        }
        break;
    case "GetCoaches":
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
        } else {
            $Coaches = $Users->getCoachers($_POST['CompanyNum']);
            echo json_encode(array('Coaches' => $Coaches, "Status" => "Success"));
        }
        break;
    case "GetClassType":
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
        } else {
            $ClassesType = $ClassType->getAllClassTypes($_POST);
            echo json_encode(array('ClassesType' => $ClassesType, "Status" => "Success"));
        }
        break;
    case 'GetTypeOfView':
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
        } else {
            $TypeOfView = $ClassSettings->TypeOfView($_POST['CompanyNum']);
            echo json_encode(array('TypeOfView' => $TypeOfView, "Status" => "Success"));
        }
        break;
    case "GetSplitView":
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
        } else {
            $SplitView = $ClassSettings->SplitView($_POST['CompanyNum']);
            echo json_encode(array('SplitView' => $SplitView, "Status" => "Success"));

        }
        break;
    case 'GetClassesByFilter':
        $FilterData = [];
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['CompanyNum'] = $_POST['CompanyNum'];
        }

        if (empty($_POST['Date'])) {
            $FilterData['StartDate'] = date('Y-m-d', strtotime('-3 days'));
            $FilterData['EndDate'] = date('Y-m-d', strtotime('+3 days'));
        } else {
            $FilterData['DateSelected'] = date('Y-m-d', strtotime($_POST['Date']));
            $FilterData['StartDate'] = date('Y-m-d', strtotime('-3 days', strtotime($_POST['Date'])));
            $FilterData['EndDate'] = date('Y-m-d', strtotime('+3 days', strtotime($_POST['Date'])));
        }
        if (empty($_POST['Branch'])) {
            echo json_encode(array("Message" => "Branch requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['Brands'] = $_POST['Branch'];
        }
        if (empty($_POST['Rooms'])) {
            echo json_encode(array("Message" => "Rooms requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['Rooms'] = $_POST['Rooms'];
        }
        if (empty($_POST['Coaches'])) {
            echo json_encode(array("Message" => "Coaches requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['Coaches'] = $_POST['Coaches'];
        }

        if (empty($_POST['ClassesTypes'])) {
            echo json_encode(array("Message" => "ClassesTypes requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['ClassType'] = $_POST['ClassesTypes'];
        }


        if (empty($_POST['SelectAllCoaches'])) {
            echo json_encode(array("Message" => "SelectAllCoaches requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['SelectAllCoaches'] = $_POST['ClassesTypes'];
        }
        if (empty($_POST['SelectAllRooms'])) {
            echo json_encode(array("Message" => "SelectAllRooms requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['SelectAllRooms'] = $_POST['ClassesTypes'];
        }
        if (empty($_POST['SelectAllClassType'])) {
            echo json_encode(array("Message" => "SelectAllClassType requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['SelectAllClassType'] = $_POST['ClassesTypes'];
        }
        if (empty($_POST['UserId'])) {
            echo json_encode(array("Message" => "UserId requeired", "Status" => "Error"));
            break;
        } else {
            $FilterData['UserId'] = $_POST['ClassesTypes'];
        }


        $Classes = $ClassCalendar->GetClassesByfilter($FilterData);
        $ClassCalendar->SaveFilterState($FilterData);
        echo json_encode(array('Classes' => $Classes, "Status" => "Success"));

        break;
    case "SaveFilterState":


        if (!isset($_POST['StartDate'])) {
            echo json_encode(array("Message" => "StartDate requeired", "Status" => "Error"));
            break;
        } elseif (!isset($_POST['EndDate'])) {
            echo json_encode(array("Message" => "EndDate requeired", "Status" => "Error"));
            break;
        } elseif (!isset($_POST['Locations'])) {
            echo json_encode(array("Message" => "Locations requeired", "Status" => "Error"));
            break;
        } elseif (!isset($_POST['Coaches'])) {
            echo json_encode(array("Message" => "Coaches requeired", "Status" => "Error"));
            break;
        } elseif (!isset($_POST['Classes'])) {
            echo json_encode(array("Message" => "Classes requeired", "Status" => "Error"));
            break;
        } elseif (!isset($_POST['ViewState'])) {
            echo json_encode(array("Message" => "view requeired", "Status" => "Error"));
            break;
        }
        unset($_POST['fun']);
        $FilterData = $_POST;
        $FilterData['CompanyNum'] = $CompanyNum;
        $FilterData['UserId'] = Auth::user()->id;
        $id = $ClassCalendar->SaveFilterState($FilterData);
        echo json_encode(array('SaveFilterState' => $id, "Status" => "Success"));
        break;
    case 'GetLastFilterForUser':
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
            break;
        }
        if (empty($_POST['UserId'])) {
            echo json_encode(array("Message" => "UserId requeired", "Status" => "Error"));
            break;
        } else {
            $FilterState = $ClassCalendar->GetLastFilterForUser($_POST['CompanyNum'], $_POST['UserId']);
            echo json_encode(array('FilterState' => $FilterState, "Status" => "Success"));
        }
        break;
    case "GetMissions":
        if (empty($_POST['Date'])) {
            $StartDate = date('Y-m-d', strtotime('-3 days'));
            $EndDate = ate('Y-m-d', strtotime('+3 days'));
        } else {
            $StartDate = date('Y-m-d', strtotime('-3 days', strtotime($_POST['Date'])));
            $EndDate = date('Y-m-d', strtotime('+3 days', strtotime($_POST['Date'])));
        }
        if (empty($_POST['CompanyNum'])) {
            echo json_encode(array("Message" => "CompanyNum requeired", "Status" => "Error"));
            break;
        } else {
            $OpenMessions = $calendar->GetOpenMissionCurrentLateDay($_POST['CompanyNum'], $StartDate, $EndDate);
            echo json_encode(array('OpenMessions' => $OpenMessions->MissionsCurrentDay, "Status" => "Success"));
        }
        break;
    default:
        echo json_encode(array("Message" => "No Found Function", "Status" => "Error"));
        break;
} else {
    echo json_encode(array("Message" => "No Function", "Status" => "Error"));
}
