<?php

require_once __DIR__ . '/Item.php';
require_once __DIR__ . '/ItemLimit.php';
require_once __DIR__ . '/ItemRoles.php';
require_once __DIR__ . '/../services/LogMovementService.php';
require_once __DIR__ . '/../services/clubmembership/LogService.php';

/**
 * @property $id
 * @property $Status
 * @property $CompanyNum
 * @property $ClubMemberShipName
 * @property $MemberShipTypeId
 * @property $BrandsId
 *
 * Class ClubMemberships
 */
class ClubMemberships extends \Hazzard\Database\Model
{
    const MAX_WORD_LENGTH = 100;

    /**
     * @var string
     */
    protected $table = 'boostapp.club_memberships';
    /**
     * @var
     */
    private $_items;
    private $_itemsRoles;
    private $_itemsLimit;

    /**
     * @param $companyNum
     * @return array
     */
    public function getAllClubMembershipsByCompany($companyNum): array
    {
        $typesTable = 'boostapp.membership_type';
        $brandsTable = 'boostapp.brands';

        return DB::table($this->table)
            ->leftJoin($typesTable, $typesTable . ".id", '=', $this->table . ".MemberShipTypeId")
            ->leftJoin($brandsTable, $brandsTable . ".id", '=', $this->table . ".BrandsId")
            ->where($this->table . '.CompanyNum', '=', $companyNum)
            ->where($this->table . '.Status', '<>', '0')
            ->select($this->table . '.*', $typesTable . '.type', $brandsTable . ".BrandName")
            ->get();
    }

    public function setClubMembershipsFullData(): void
    {
        $this->setItems();
        $this->setItemsLimit();
        $itemId = $this->items()[0]->id ?? null;
        $this->setItemsRoles($itemId);
    }

    /**
     * @return array
     */
    public function items(): array
    {
        if (empty($this->_items)) {
            $this->setItems();
        }
        return $this->_items;
    }

    /**
     * @return array
     */
    public function itemsRoles(): array
    {
        if (empty($this->_itemsRoles)) {
            $this->setItemsRoles();
        }
        return $this->_itemsRoles;
    }

    /**
     * @return array
     */
    public function itemsLimit(): array
    {
        if (empty($this->_itemsLimit)) {
            $this->setItemsLimit();
        }
        return $this->_itemsLimit;
    }


    /**
     *  set items
     */
    public function setItems(): void
    {
        $this->_items = (new Item())->getItemsByClubMemberships($this->id);
    }

    /**
     *  set items
     */
    public function setItemsRoles($itemId=null): void
    {
        $this->_itemsRoles = (new ItemRoles())->getItemsRolesByClubMemberships($this->id, $itemId);

    }

    /**
     *  set items
     */
    public function setItemsLimit(): void
    {
        $this->_itemsLimit = (new Item())->getItemsLimitMoreDetails($this->id);
        $itemsLimitBlocks = (new ItemLimit())->getItemLimitByClubMemberships($this->id);
        $this->_itemsLimit->blocksData = [];
        if (!empty($itemsLimitBlocks)) {
            foreach ($itemsLimitBlocks as $key => $value) {
                $this->_itemsLimit->blocksData[$key] = $value;
            }
        }
    }

    /**
     * return array from this class
     * @return array
     */
    public function returnArray(): array
    {
        $a = array();
        $a['clubMemberships'] = $this->toArray();
        if (!empty($this->_items)) {
            $a['items'] = $this->_items;
        }
        if (!empty($this->_itemsLimit)) {
            $a['itemsLimit'] = $this->_itemsLimit;

        }
        if (!empty($this->_itemsRoles)) {
            $a['itemsRoles'] = $this->_itemsRoles;
        }
        return $a;
    }

    /**
     * return array from this class
     * @return array
     */
    public function returnEditData(): array
    {
        $a = array();
        $a['clubMemberships'] = $this->toArray();
        if (!empty($this->_items)) {
            $a['items'] = $this->_items;
        }
        if (!empty($this->_itemsLimit)) {
            $a['itemsLimit'] = $this->_itemsLimit;
        }
        if (!empty($this->_itemsRoles)) {
            $a['itemsRoles'] = $this->_itemsRoles;
        }
        return $a;
    }

    /**
     * @param $arr
     * @return array
     * Create from obj array
     */
    private function createArrayFromObjArr($arr): array
    {
        $a = array();
        foreach ($arr as $item) {
            $a[] = $item->toArray();
        }
        return $a;
    }

    /**
     * @param $data
     * @return void
     * @throws Exception
     */
    public function editClubMemberships($data)
    {
        foreach ($data as $key => $value) {
            if ($value !== '') {
                $this->$key = $value;
            }
        }

        $updateRules  = self::$updateRules;
        if(isset($data['BrandsId']) && ($data['BrandsId'] === 'BA999' ||  $data['BrandsId'] < 1)) {
            $this->BrandsId = null;
            unset($updateRules['BrandsId']);
        }
        if(isset($data['MemberShipTypeId']) && ($data['MemberShipTypeId'] === 'BA999' ||  $data['MemberShipTypeId'] < 1)) {
            $this->MemberShipTypeId = null;
            unset($updateRules['MemberShipTypeId']);
        }

        $validator = Validator::make($this->getAttributes(), $updateRules);
        if ($validator->passes()) {
            $this->save();
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * @param $data
     * @return void
     * @throws Exception
     */
    public function createNewClubMemberships($data)
    {
        foreach ($data as $key => $value) {
            if ($value !== '') {
                $this->$key = $value;
            }
        }
        $validator = Validator::make($this->getAttributes(), self::$createRules);
        if ($validator->passes()) {
            $this->save();

            $LogId = LogMovementService::ClubMembershipLog(LogMovementService::ACTION_CREATE, $data['ClubMemberShipName'], 0);
            // add details to separate log
            LogService::addNewClubMembership($LogId, $this->id, $data);

            // return log id for other objects
            return $LogId;
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * Validation checker and if correct - adds Item and return id
     * Otherwise throws an error with an explanation of the error
     * @param $itemData
     * @return mixed
     * @throws Exception
     */
    public function addItemToDb($itemData)
    {
        $items = new Item();
        $validator = Validator::make($itemData, $items::$createRules);
        if ($validator->passes()) {
            return $items->createNewItem($itemData);
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * Validation checker and if correct - adds Item and return id
     * Otherwise throws an error with an explanation of the error
     * @param $data
     * @param $LogId
     * @param $itemPrefixName
     * @throws Exception
     */
    public function editAllItems($data, $LogId, $itemPrefixName ='')
    {
        $itemsId = (new Item())->getAllItemsIdByClubMemberships($this->id);
        foreach ($itemsId as $itemId) {
            $tempData = $data;
            unset($tempData['ItemName']);
//            $item = new Item($itemId->id);
            $item = Item::find($itemId->id);
            if(isset($tempData['ItemPrefixName'])) {
                $tempData['ItemName'] = $item->getNewItemName($tempData['ItemPrefixName']);
                unset($tempData['ItemPrefixName']);
            }
            if(isset($tempData['Display']) && $item->Display == $tempData['Display']) {
                unset($tempData['Display']);
            }
            $validator = Validator::make($tempData, $item::$updateRules);
            if ($validator->passes()) {
                $item->updateItem($tempData);
                unset($tempData['MemberShip'],$tempData['ItemName'],$tempData['Brands']);
                if(!empty($tempData)){
                    // add to separate log
                    LogService::updateClubMembershipObject($LogId, $this->id,
                        LogService::TYPE_ITEM, $itemId->id, $itemId->id, $tempData);
                }
            } else {
                throw new Exception(json_encode($validator->errors()->toArray()));
            }
        }
    }

    /**
     * @param $data
     * @param $LogId
     * @param $itemPrefixName
     * @throws Exception
     */
    public function editItems($data, $LogId, $itemPrefixName = '')
    {
        foreach ($data as $itemData) {
            //fix vat price value
            if (isset($itemData['ItemPrice'])) {
                $itemData['ItemPriceVat'] = $itemData['ItemPrice'] - ($itemData['ItemPrice'] * 0.17);
            }
//            $item = new Item($itemData['id']);
            $item = Item::find($itemData['id']);
            unset($itemData['id']);
            $validator = Validator::make($itemData, $item::$updateRules);
            if ($validator->passes()) {
                $itemData['ItemName'] = $item->getNewItemName($itemPrefixName, $itemData);
                $item->updateItem($itemData);
                // add to separate log
                LogService::updateClubMembershipObject($LogId, $this->id,
                    LogService::TYPE_ITEM, $item->__get('id'), $item->__get('id'), $itemData);
            } else {
                throw new Exception(json_encode($validator->errors()->toArray()));
            }
        }
    }

    /**
     * @param $data
     * @param $LogId
     * @throws Exception
     */
    public function compareAndEditItems($data, $LogId)
    {
        $itemObject = new Item();

        try {
            $dbItems = $itemObject->getItemsByClubMemberships($this->id);
            //first loop check if the item data in a db
            foreach ($data as $itemKey => $itemData) {
                unset($itemData['id']);
                unset($itemData['ItemName']);
                if (empty($itemData['BalanceClass'])) {
                    $itemData['BalanceClass'] = '0';
                }
                foreach ($dbItems as $dbKey => $dbItem) {
                    $sameRow = true;
                    foreach ($itemData as $key => $value) {
                        if ($value != $dbItem->$key && $key != 'ItemPrice') {
                            $sameRow = false;
                            break;
                        }
                    }
                    //A match was found taking out of the array and continuing
                    if ($sameRow) {
                        if ($dbItem->ItemPrice !== $itemData['ItemPrice']) {
                            $itemPriceVat = $itemData['ItemPrice'] - ($itemData['ItemPrice'] * 0.17);
//                            (new Item($dbItem->id))->updateItem(["ItemPrice" => $itemData['ItemPrice'],
//                                "ItemPriceVat" => $itemPriceVat]);

                            $itemObj = Item::find($dbItem->id);
                            $itemObj->ItemPrice = $itemData['ItemPrice'];
                            $itemObj->ItemPriceVat = $itemPriceVat;
                            $itemObj->save();
                            // add to separate log
                            LogService::updateClubMembershipObject($LogId, $this->id,
                                LogService::TYPE_ITEM, $dbItem->id, $dbItem->id, [
                                    "ItemPrice" => $itemData['ItemPrice'],
                                    "ItemPriceVat" => $itemPriceVat,
                                ]);
                        }
                        unset($data[$itemKey], $dbItems[$dbKey]);
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception('compareAndEditItems - part 1 - compare');
        }

        try {
            $moreItemData = $itemObject->getItemsMoreDetails($this->id);
            foreach ($data as $itemData) {
                unset($itemData['id']);
                $newItem = array_merge((array)$moreItemData, (array)$itemData);
                $newItem['ClubMembershipsId'] = $this->id;
                $newItem['ItemPriceVat'] = $newItem['ItemPrice'] - ($newItem['ItemPrice'] * 0.17);
                $newItem['Vat'] = 17;
                $newItem['UserId'] = Auth::user()->id;
                $newItem['Dates'] = date('Y-m-d H:i:s');
                $itemID = $this->addItemToDb($newItem);
                // add to separate log
                LogService::addNewClubMembershipObject($LogId, $this->id,
                    LogService::TYPE_ITEM, $itemID, $itemID, $newItem);
            }
        } catch (Exception $e) {
            throw new Exception('compareAndEditItems - part 2 - add new');
        }

        try {
            foreach ($dbItems as $dbItemData) {
                $itemObject->changeStatus($dbItemData->id, '1');
                // add to separate log - delete
                LogService::deleteClubMembershipObject($LogId, $this->id,
                    LogService::TYPE_ITEM, $dbItemData->id, $dbItemData->id);
            }
        } catch (Exception $e) {
            throw new Exception('compareAndEditItems - part 3 - remove old');
        }
    }

    public function getAllItemsIdByClubMemberships(){
        return (new Item())->getAllItemsIdByClubMemberships($this->id, true);
    }

    /**
     * @param $data
     * @param $allItemsId
     * @param $LogId
     * @throws Exception
     */
    public function editItemsLimit($data, $allItemsId, $LogId)
    {
        $itemLimitObject = new ItemLimit();
        if (!$data) {
            $deletedCount = $itemLimitObject->deleteByClubMemberships($this->id);
            if($deletedCount) {
                // add to separate log - delete by club membership id - no information about object id or item id
                foreach ($allItemsId as $itemId) {
                    LogService::deleteClubMembershipObject($LogId, $this->id, LogService::TYPE_ITEM_LIMIT, 0, $itemId->id);
                }
            }
            return;
        }
        $dbItemLimits = $itemLimitObject->getItemLimitByClubMemberships($this->id);
        $noItemLimitFound = true;
        if (!empty($dbItemLimits)) {
            $updateData = $data;
            unset($updateData['ClubMembershipsId'], $updateData['CompanyNum'], $updateData['UserId'], $dbItemLimits->id);
            foreach ($updateData as $key => $value) {
                if ($dbItemLimits->$key == $value) {
                    unset($updateData[$key], $dbItemLimits->$key);
                }
            }
            foreach ($dbItemLimits as $key => $value) {
                if (!isset($updateData[$key]) && $value) {
                    $updateData[$key] = null;
                }
            }
            $noItemLimitFound = false;
        }
        foreach ($allItemsId as $itemId) {
            if (!$noItemLimitFound) {
                $id = $itemLimitObject->getByItemId($itemId->id);
                if ($id) {
                    $validator = Validator::make($updateData, $itemLimitObject::$updateRules);
                    if ($validator->passes()) {
                        $itemLimitObject->updateById($id->id, $updateData);
                        // add to separate log
                        LogService::updateClubMembershipObject($LogId, $this->id,
                            LogService::TYPE_ITEM_LIMIT, $id->id, $itemId->id, $updateData);
                        continue;
                    } else {
                        throw new Exception(json_encode($validator->errors()->toArray()));
                    }
                }
            }
            $data['itemId'] = $itemId->id;
            $ilId = $this->addItemLimitToDb($data);
            // add to separate log
            LogService::addNewClubMembershipObject($LogId, $this->id,
                LogService::TYPE_ITEM_LIMIT, $ilId, $itemId->id, $data);
        }
    }

    /**
     * @param $data
     * @param $allItemsId
     * @param $LogId
     * @throws Exception
     */
    public function editItemRoles($data, $allItemsId, $LogId)
    {
        $itemRoleObject = new ItemRoles();
        foreach ($allItemsId as $itemId) {
            $dbItemsRole = $itemRoleObject->getItemsRolesByClubMemberships($this->id, $itemId->id);
            //need create new records for this item
            if (empty($dbItemsRole)) {
                //add item role to db
                foreach ($data as $role) {
                    $this->preparingAndAddNewItemRoleData($role, $itemId->id, $LogId);
                }
                continue;
            }
            foreach ($data as $itemRoleKey => $itemRoleData) {
                if (!isset($itemRoleData['Value'])) {
                    $itemRoleData['Value'] = null;
                }
                $notChangeFlag = false;
                $editData = [];
                foreach ($dbItemsRole as $dbKey => $dbItem) {
                    if ($itemRoleData['Class'] === $dbItem->Class && $itemRoleData['Group'] === $dbItem->Group
                        && $itemRoleData['Item'] === $dbItem->Item) {
                        if ($itemRoleData['Value'] !== $dbItem->Value) {
                            if (empty($editData[$itemRoleKey])) {
                                $editData = ['Value' => $itemRoleData['Value'],
                                    'ChangeUserId' => Auth::user()->id,
                                    'id' => $dbItem->id,
                                    'dbKey' => $dbKey,
                                ];
                            }
                        } else {
                            unset($dbItemsRole[$dbKey]);
                            $notChangeFlag = true;
                            break;
                        }
                    }
                }
                if ($notChangeFlag) {
                    continue;
                } elseif (!empty($editData)) {
                    $id = $editData['id'];
                    $dbKey = $editData['dbKey'];
                    unset($editData['dbKey'], $editData['id'], $dbItemsRole[$dbKey]);
                    $validator = Validator::make($editData, $itemRoleObject::$updateRules);
                    if ($validator->passes()) {
                        $itemRoleObject->updateById($id, $editData);
                        // add to separate log
                        LogService::updateClubMembershipObject($LogId, $this->id,
                            LogService::TYPE_ITEM_ROLE, $id, $itemId->id, $editData);
                    } else {
                        throw new Exception(json_encode($validator->errors()->toArray()));
                    }
                } else {
                    //add new
                    $this->preparingAndAddNewItemRoleData($itemRoleData, $itemId->id, $LogId);
                }
            }
            //delete all items roles that not in use
            if (!empty($dbItemsRole)) {
                foreach ($dbItemsRole as $role) {
                    $itemRoleObject->deleteItemRoleByid($role->id);
                    // add to separate log
                    LogService::deleteClubMembershipObject($LogId, $this->id,
                        LogService::TYPE_ITEM_ROLE, $role->id, $itemId->id);
                }
            }
        }
    }

    /**
     * @param $itemRoleData
     * @param $id
     * @param $LogId
     * @return void
     * @throws Exception
     */
    private function preparingAndAddNewItemRoleData($itemRoleData, $id, $LogId): void
    {
        $itemRoleData['CompanyNum'] = $this->CompanyNum;
        $itemRoleData['ClubMembershipsId'] = $this->id;
        $itemRoleData['ChangeUserId'] = Auth::user()->id;
        $itemRoleData['UserId'] = Auth::user()->id;
        $itemRoleData['ItemId'] = $id;
        $itemRoleData['GroupId'] = $itemRoleData['CompanyNum'] . $id . '-' . $itemRoleData['optionNumber'] ?? '1';
        $irId = $this->addItemRolesToDb($itemRoleData);
        // add to separate log
        LogService::addNewClubMembershipObject($LogId, $this->id,
            LogService::TYPE_ITEM_ROLE, $irId, $id, $itemRoleData);
    }

    /**
     * Validation checker and if correct - adds ItemLimit and return id
     * Otherwise throws an error with an explanation of the error
     * @throws Exception
     */
    public function addItemLimitToDb($itemLimitData)
    {
        $itemLimit = new ItemLimit();
        $validator = Validator::make($itemLimitData, $itemLimit::$createRules);
        if ($validator->passes()) {
            return $itemLimit->createNewItemLimit($itemLimitData);
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * Validation checker and if correct - adds ItemLimit and return id
     * Otherwise throws an error with an explanation of the error
     * @throws Exception
     */
    public function addItemRolesToDb($itemRolesData)
    {
        $itemRoles = new ItemRoles();
        $validator = Validator::make($itemRolesData, $itemRoles::$createRules);
        if ($validator->passes()) {
            return $itemRoles->createNewItemRoles($itemRolesData);
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * @param $id
     * @param $status
     * @return mixed
     */
    public function changeStatus($id, $status)
    {
        // get name by id
        $name = $this->getNameById($id);

        // check new status and write to log
        if ($status == 2) {
            LogMovementService::ClubMembershipLog(LogMovementService::ACTION_DISABLE, $name, 0);
        } else {
            LogMovementService::ClubMembershipLog(LogMovementService::ACTION_ENABLE, $name, 0);
        }
        // don't need more details

        return DB::table($this->table)
            ->where('id', '=', $id)
            ->update(['Status' => $status]);
    }

    public static $createRules = [
        'id' => 'integer',
        'Status' => 'integer|between:0,2',
        'CompanyNum' => 'required|integer',
        'ClubMemberShipName' => 'required|min:1|max:' . self::MAX_WORD_LENGTH,
        'MemberShipTypeId' => 'exists:boostapp.membership_type,id',
        'BrandsId' => 'exists:boostapp.brands,id',
    ];

    public static $updateRules = [
        'ClubMemberShipName' => 'min:1|max:' . self::MAX_WORD_LENGTH,
        'MemberShipTypeId' => 'exists:boostapp.membership_type,id',
        'BrandsId' => 'exists:boostapp.brands,id',
    ];

    /**
     * @param $id
     * @return mixed
     */
    public function getNameById($id)
    {
        return DB::table($this->table)
            ->where('id', '=', $id)
            ->pluck('ClubMemberShipName');
    }
}
