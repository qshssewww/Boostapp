<?php
require_once '../../../app/init.php';
require_once '../../services/LoggerService.php';
require_once '../../services/clubmembership/LogService.php';
require_once "../../Classes/ClubMemberships.php";
require_once "../../Classes/MembershipType.php";

header('Content-Type: application/json');
const ERROR = 0;
const SUCCESS = 1;

if (Auth::guest()) exit;

$ClubMemberships = new ClubMemberships();
$companyNum = Company::getInstance()->__get('CompanyNum');

//Dealing with errors
function catchErrors($e, $data = null, $errorEchoMassage = null): array
{
    LoggerService::error($e, LoggerService::CATEGORY_CLUB_MEMBERSHIPS);
    if ($data) {
        LoggerService::info($data, LoggerService::CATEGORY_CLUB_MEMBERSHIPS);
    }
    return array("Message" => $errorEchoMassage ?? $e->getMessage(), "Status" => ERROR);
}

if (!Auth::userCan('31')) {
    echo json_encode(array("Message" => lang('page_role_admin'), "Status" => ERROR));
} elseif (!empty($_POST["fun"])) {
    switch ($_POST["fun"]) {
        /**************** Club Memberships ****************/
        // Change ClubMembership disables state
        case "ChangeClubMembershipsDisabled":
            $id = (int)($_POST['id'] ?? 0);
            if ($id == 0 || !isset($_POST['Status'])) {
                echo json_encode(array("data" => ['id' => $id], "Status" => ERROR));
                break;
            }

            $Status = (int)$_POST['Status'];

            (new ClubMemberships())->changeStatus($id, $Status);
            (new Item())->changeDisabledByClubMembershipsId($id, ($Status == 2 ? 1 : 0));

            echo json_encode(array("data" => ['id' => $id, 'Status' => $Status], "Status" => SUCCESS));
            break;
        //Get information from DB about club membership
        case "GetClubMembershipsData":
            unset($_POST["fun"]);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id required", "Status" => ERROR));
            } elseif (!is_numeric($_POST['id'])) {
                echo json_encode(array("Message" => "id must be number", "Status" => ERROR));
            } else {
                try {
                    $clubMembership = $ClubMemberships::find(($_POST['id']));
                    $clubMembership->setClubMembershipsFullData();
                    echo json_encode(array("data" => $clubMembership->returnArray(), "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error get Club Membership');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;
        //Create new club membership
        case "AddClubMemberships":
            unset($_POST["fun"]);
            $data = $_POST['data'] ?? [];
            if (empty($data)) {
                echo json_encode(array("Message" => "data required", "Status" => ERROR));
            } elseif (empty($data['clubMemberships'])) {
                echo json_encode(array("Message" => "clubMemberships required", "Status" => ERROR));
            } elseif (empty($data['items'])) {
                echo json_encode(array("Message" => "items required", "Status" => ERROR));
            } elseif (empty($data['itemsRoles'])) {
                echo json_encode(array("Message" => "itemsRoles required", "Status" => ERROR));
            } else {
                $logId = '';
                $clubMembershipsId = '';
                try {
                    //create clubMemberships
                    $data['clubMemberships']['CompanyNum'] = $companyNum;
                    if (!empty($data['clubMemberships']['BrandsId']) && $data['clubMemberships']['BrandsId'] === 'BA999') {
                        unset($data['clubMemberships']['BrandsId']);
                    }
                    $membershipId = $data['clubMemberships']['MemberShipTypeId'] ?? MembershipType::getDefaultMembershipId();
                    if($membershipId) {
                        $data['clubMemberships']['MemberShipTypeId'] = $membershipId;
                    }
                    if(isset($data['clubMemberships']['ClubMemberShipName'])) {
                        $data['clubMemberships']['ClubMemberShipName'] = trim($data['clubMemberships']['ClubMemberShipName']);
                    }

                    $logId = $ClubMemberships->createNewClubMemberships($data['clubMemberships']);
                    $clubMembershipsId = $ClubMemberships->id;

                    // add values to itemsLimit
                    if (!empty($data['itemsLimit'])) {
                        $itemLimit = $data['itemsLimit'];
                        unset($itemLimit['id']);
                        $itemLimit['ClubMembershipsId'] = $clubMembershipsId;
                        $itemLimit['CompanyNum'] = $companyNum;
                        $itemLimit['UserId'] = Auth::user()->id;
                    }

                    // add values to itemsRoles
                    foreach ($data['itemsRoles'] as &$itemsRole) {
                        unset($itemsRole['id']);
                        $itemsRole['CompanyNum'] = $companyNum;
                        $itemsRole['ClubMembershipsId'] = $clubMembershipsId;
                        $itemsRole['ChangeUserId'] = Auth::user()->id;
                        $itemsRole['UserId'] = Auth::user()->id;
                        $itemsRole['Dates'] = date('Y-m-d H:i:s');
                    }

                    // add values to item
                    foreach ($data['items'] as $item) {
                        unset($item['id']);
                        $item['CompanyNum'] = $companyNum;
                        $item['ClubMembershipsId'] = $clubMembershipsId;
                        $item['MemberShip'] = $membershipId ?? 'BA999';
                        $item['Brands'] = $ClubMemberships->BrandsId ?? 'BA999';
                        $item['UserId'] = Auth::user()->id;
                        $item['Dates'] = date('Y-m-d H:i:s');
                        if (!empty($item['Content'])) {
                            $item['Content'] = htmlspecialchars($item['Content']);
                        }
                        $item['ItemPriceVat'] = $item['ItemPrice'] - ($item['ItemPrice'] * 0.17);
                        $item['Vat'] = 17;
                        $itemId = $ClubMemberships->addItemToDb($item);
                        LogService::addNewClubMembershipObject($logId, $clubMembershipsId, LogService::TYPE_ITEM, $itemId, $itemId, $item);
                        //add item limit to db
                        if (!empty($itemLimit)) {
                            $itemLimit['itemId'] = $itemId;
                            $ilID = $ClubMemberships->addItemLimitToDb($itemLimit);
                            // add to separate log
                            LogService::addNewClubMembershipObject($logId, $clubMembershipsId,
                                LogService::TYPE_ITEM_LIMIT, $ilID, $itemId, $itemLimit);
                        }

                        //add item role to db
                        foreach ($data['itemsRoles'] as $role) {
                            $role['ItemId'] = $itemId;
                            $role['GroupId'] = $companyNum . $itemId . '-' . $role['optionNumber'];
                            $irID = $ClubMemberships->addItemRolesToDb($role);
                            // add to separate log
                            LogService::addNewClubMembershipObject($logId, $clubMembershipsId,
                                LogService::TYPE_ITEM_ROLE, $irID, $itemId, $role);
                        }
                    }

                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error in add Club Membership');
                    if ($logId && $clubMembershipsId)
                        LogService::logError($logId, $clubMembershipsId, [
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'code' => $e->getCode(),
                        ]);
                    echo json_encode($resp);
                    exit();
                }
                echo json_encode(array('method' => 'add', "Status" => SUCCESS));
                break;
            }
            break;
        //update the club membership
        case "EditClubMemberships":
            unset($_POST["fun"]);
            $data = $_POST['data'] ?? [];
            if (empty($data)) {
                echo json_encode(array("Message" => "data required", "Status" => ERROR));
            } elseif (!isset($data['clubMembershipId'])) {
                echo json_encode(array("Message" => "clubMembershipId required", "Status" => ERROR));
            } elseif (empty($data['itemsRoles'])) {
                echo json_encode(array("Message" => "itemsRoles required", "Status" => ERROR));
            } else {
                $logId = '';
                $clubMembershipsId = '';
                try {
                    $clubMembership = $ClubMemberships::find($data['clubMembershipId']);
                    $clubMembershipsId = $clubMembership->id ?? '';
                    //edit clubMember ships basic data
                    if(isset($data['clubMemberships']['ClubMemberShipName'])) {
                        $data['clubMemberships']['ClubMemberShipName'] = trim($data['clubMemberships']['ClubMemberShipName']);
                    }

                    $logId = LogMovementService::ClubMembershipLog(LogMovementService::ACTION_UPDATE, $clubMembership->ClubMemberShipName, 0);
                    if (!empty($data['clubMemberships'])) {
                        $clubMembershipsData = $data['clubMemberships'];
                        $clubMembership->editClubMemberships($clubMembershipsData);
                        // add to separate log
                        LogService::updateClubMembership($logId, $clubMembershipsId, $data['clubMemberships'] ?? new stdClass());
                    }

                    //edit items basic data for all items (status 0 and 1)
                    if (!empty($data['itemsGeneralData'])) {
                        $itemsGeneralData = $data['itemsGeneralData'];
                        if (!empty($itemsGeneralData['Content'])) {
                            $itemsGeneralData['Content'] = htmlspecialchars($itemsGeneralData['Content']);
                        }
                        $clubMembership->editAllItems($itemsGeneralData, $logId, $clubMembership->ClubMemberShipName ?? '');
                    }

                    // add values to itemsLimit
                    if (!empty($data['itemsLimit'])) {
                        $itemLimit = $data['itemsLimit'];
                        unset($itemLimit['id']);
                        $itemLimit['ClubMembershipsId'] = $clubMembership->id;
                        $itemLimit['CompanyNum'] = $companyNum;
                        $itemLimit['UserId'] = Auth::user()->id;
                    } else {
                        $itemLimit = null;
                    }
                    //edit item blocks data
                    if (!empty($data['items'])) {
                        $items = $data['items'];
                        if (isset($data['updateAllItems'])) {
                            $clubMembership->compareAndEditItems($items, $logId);
                        } else {
                            // only update change fields
                            $clubMembership->editItems($items, $logId, $clubMembership->ClubMemberShipName ?? '');
                        }
                    }
                    $allItemsId = $clubMembership->getAllItemsIdByClubMemberships();
                    $clubMembership->editItemsLimit($itemLimit, $allItemsId, $logId);

                    // add values to items Role
                    $itemsRoles = $data['itemsRoles'];
                    $clubMembership->editItemRoles($itemsRoles, $allItemsId, $logId);


                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error in EditClubMemberships');
                    if ($logId && $clubMembershipsId)
                        LogService::logError($logId, $clubMembershipsId, [
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'code' => $e->getCode(),
                        ]);
                    echo json_encode($resp);
                    exit();
                }


                echo json_encode(array('method' => 'update', "Status" => SUCCESS));
                break;
            }
            break;

        default:
            echo json_encode(array("Message" => "No Found Function", "Status" => ERROR));
            break;
    }
} else {
    echo json_encode(array("Message" => "No Function", "Status" => ERROR));
}

