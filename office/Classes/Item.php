<?php

require_once __DIR__ . '/../../app/helpers/TimeHelper.php';

require_once 'ItemRoles.php';
require_once 'ClassesType.php';
require_once 'ClassesType.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $Department
 * @property $MemberShip
 * @property $oldMemberShip
 * @property $ItemName
 * @property $ItemPrice
 * @property $ItemPriceVat
 * @property $Vat
 * @property $CostPrice
 * @property $Supplier
 * @property $Remarks
 * @property $Status
 * @property $Dates
 * @property $UserId
 * @property $Vaild
 * @property $Vaild_Type
 * @property $LimitClass
 * @property $LimitClassMorning
 * @property $LimitClassEvening
 * @property $LimitClassMonth
 * @property $NotificationDays
 * @property $BalanceClass
 * @property $MinusCards
 * @property $StartTime
 * @property $EndTime
 * @property $Vaild_LastCalss
 * @property $CancelLImit
 * @property $ClassSameDay
 * @property $FreezMemberShip
 * @property $FreezMemberShipDays
 * @property $FreezMemberShipCount
 * @property $FreezMemberShipDaysMin
 * @property $OldId
 * @property $LimitType
 * @property $Brands
 * @property $ItemCat
 * @property $Display
 * @property $Payment
 * @property $Content
 * @property $Disabled
 * @property $Image
 * @property $notificationAtEnd
 * @property $membershipStartCount
 * @property $membershipStartDate
 * @property $membershipAllowLateReg
 * @property $membershipAllowRelativeDiscount
 * @property $membershipRelativeDiscount
 * @property $isPaymentForSingleClass
 * @property $isNew
 * @property $order
 * @property $Favorite
 *
 * Class Item
 */
class Item extends \Hazzard\Database\Model
{
    protected $table = "items";

    public const STATUS_ACTIVE = 0;
    public const STATUS_OFF = 1;

    public const DEPARTMENT_PERIODIC = 1;
    public const DEPARTMENT_TICKET = 2;
    public const DEPARTMENT_TRIAL = 3;
    public const DEPARTMENT_PRODUCT = 4;

    public const PAYMENT_STANDING_ORDER = 2;
    public const PAYMENT_REGULAR = 1;

    public const MEMBERSHIP_START_COUNT_FROM_PURCHASE = 1;
    public const MEMBERSHIP_START_COUNT_FROM_PREV_ACTIVITY = 2;
    public const MEMBERSHIP_START_COUNT_FROM_FIRST_LESSON = 3;
    public const MEMBERSHIP_START_COUNT_FROM_DATE = 4;
    public const MEMBERSHIP_START_COUNT_FROM_NEXT_LESSON = 5;

    public const VALID_TYPE_OPTIONS =[
        1 => "day",
        2 => "week",
        3 => "month",
        4 => "year"
    ];


    /**
     * @param $attributes
     */
    public function __construct($attributes = [])
    {
        if (is_numeric($attributes)) {
            $model = self::find($attributes);
            if ($model) {
                $this->fill($model->toArray());
                $this->exists = true;
            }
            $attributes = [];
        }

        parent::__construct($attributes);
    }

    /**
     * @param $data
     * @param $classId
     * @param $CompanyNum
     * @param $edit
     * @return void
     */
    public function createItemFromClasses($data, $classId, $CompanyNum, $edit = false)
    {
        $itemRole = new ItemRoles();
        $this->CompanyNum = $CompanyNum;
        $this->Department = 2;
        $this->ItemName = $data["ClassName"];
        $this->ItemPrice = $data["singleEntryRate"];
        $this->MemberShip = "BA999";
        $this->ItemPriceVat = $this->calcVatItemPrice($data["singleEntryRate"], 17);
        $this->Vat = 17;
        $this->UserId = Auth::user()->id;
        $this->Dates = date('Y-m-d H:i:s');
        $this->BalanceClass = 1;
        $this->Display = 0;
        $this->isPaymentForSingleClass = 1;
        if ($edit == false) {
            $itemId = $this->insertItem();
            $itemRole->__set("CompanyNum", $CompanyNum);
            $itemRole->__set("ItemId", $itemId);
            $itemRole->__set("Class", $classId);
            $itemRole->__set("Group", "Class");
            $itemRole->__set("GroupId", $CompanyNum . $itemId . "-1");
            $itemRole->__set("Item", "Class");
            $itemRole->__set("UserId", Auth::user()->id);
            $itemRole->insertItemRoleObj($itemRole);
        } else if ($edit == true) {
            $this->updateItemFromClasses();
        }
    }

    /**
     * @param $item
     * @return mixed
     */
    public function insertItem($item = null)
    {
        if ($item == null) {
            $item = $this;
        }
        $itemArr = $this->createArrayFromObj($item);
        $itemId = DB::table($this->table)->insertGetId($itemArr);
        return $itemId;
    }

    /**
     * @param $price
     * @param $vat
     * @param $includeTax
     * @return string
     */
    private function calcVatItemPrice($price, $vat, $includeTax = true)
    {
        if ($includeTax) {
            $sub = $price * ($vat / 100);
            $total = $price - $sub;
            return number_format($total, 2);
        } else {
            return $price;
        }
    }

    /**
     * @param $item
     * @return array
     */
    public function createArrayFromObj($item = null)
    {
        if ($item == null) {
            $item = $this;
        }
        $itemArr = array();
        foreach ($item as $key => $value) {
            if ($value !== null && $key != "table") {
                $itemArr[$key] = $value;
            }
        }
        return $itemArr;
    }

    /**
     * @param $itemId
     * @return bool
     */
    public function isZoomClass($itemId)
    {
        $itemRole = new ItemRoles($itemId);
        if (empty($itemRole)) {
            return false;
        } else if (is_array($itemRole->__get("Class")) || strpos($itemRole->__get("Class"), ",")) {
            return false;
        } else {
            return $itemRole->isZoom($itemRole->__get("Class"));
        }
    }

    /**
     * @param $itemId
     * @return void
     */
    public function getItemById($itemId)
    {
        $item = DB::table("items")->where("id", "=", $itemId)->first();
        if ($item != null) {
            foreach ($item as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * @param $itemId
     * @return mixed
     */
    public function getItemPaymentById($itemId)
    {
        return DB::table($this->table)
            ->where("id", "=", $itemId)
            ->pluck('Payment');
    }

    /**
     * @param $item
     * @return void
     */
    public function updateItemFromClasses($item = null)
    {
        if ($item == null) {
            $item = $this;
        }
        $itemArr = $this->createArrayFromObj($item);
        if ($item->id != null) {
            DB::table($this->table)->where('id', $item->id)->update($itemArr);
        }
    }

    /**
     * @param $data
     * @param $item
     * @return void
     */
    public function updateItem($data, $item = null)
    {
        if ($item == null) {
            $item = $this;
        }
        if ($item->id != null) {
            DB::table($this->table)->where('id', $item->id)->update($data);
        }
    }

    /**
     * @param $companyNum
     * @param $departmentArray
     * @return array
     */
    public function getMemberships($companyNum, $departmentArray = [1, 2, 3])
    {
        $res = $this->getItemsByCompanyNum($companyNum, $departmentArray);
        $data = [];
        foreach ($res as $row) {
            $departmentText = "";
            $paymentType = lang("payment_method") . ": " . ($row->Payment == 2 ? lang("permanent_single") : lang("regular"));
            $entries = $row->Department == 2 || $row->Department == 3 ? " || כניסות: " . $row->BalanceClass : "";
            switch ($row->Department) {
                case 1:
                    $departmentText = lang("membership");
                    break;
                case 2:
                    $departmentText = lang("class_tabe_card");
                    break;
                case 3:
                    $departmentText = lang('a_trial');
            }

            $validDurationText = lang("expires_at") . ": ";

            if ($row->Vaild_Type == 0) {
                $validDurationText .= lang("without") . " " . lang("expires_at");
            } else {
                $validDurationText = $row->Vaild . " ";
            }

            switch ($row->Vaild_Type) {
                case 1:
                    $validDurationText .= lang("days");
                    break;
                case 2:
                    $validDurationText .= lang("weeks");
                    break;
                case 3:
                    $validDurationText .= lang("months");
                    break;
            }
            $helpHtml = $departmentText . " || " . $paymentType . "<br/>" . $validDurationText . $entries;

            array_push($data, [
                "id" => $row->id,
                "title" => $row->ItemName,
                "helpHtml" => $helpHtml,
                "itemPrice" => $row->ItemPrice,
                "valid" => $row->Vaild,
                "valid_type" => $row->Vaild_Type,
                "department" => $row->Department
            ]);
        }
        return $data;
    }

    /**
     * @param $companyNum
     * @param $departments
     * @return mixed
     */
    public function getItemsByCompanyNum($companyNum, $departments)
    {
        $res = DB::table($this->table)->where('CompanyNum', $companyNum)
            ->whereIn('Department', $departments)->where('Status', 0)->get();

        return $res;
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function GetItemsByCompany($CompanyNum)
    {
        $Items = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', 0)
            ->where('Department', '!=', 4)
            ->get();
        return $Items;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getRow($id)
    {
        return DB::table($this->table)->where("id", "=", $id)->first();
    }

    //If not exist, create single class item for ClassType

    /**
     * @param $ClassTypeId
     * @return mixed
     */
    public static function getSingleClassItem($ClassTypeId)
    {
        $CompanyNum = Auth::user()->__get('CompanyNum');
        $UserId = Auth::user()->__get('id');
        $ClassType = ClassesType::find($ClassTypeId);

        $SingleClassItem = DB::table(self::getTable())
            ->join('items_roles', 'items_roles.ItemId', '=', self::getTable() . '.id')
            ->where(self::getTable() . '.CompanyNum', $CompanyNum)
            ->where(self::getTable() . '.MemberShip', 'BA999')
            ->where(self::getTable() . '.Department', 2)
            ->where(self::getTable() . '.BalanceClass', 1)
            ->where(self::getTable() . '.isPaymentForSingleClass', 1)
            ->where('items_roles.Class', $ClassTypeId)
            ->select(self::getTable() . ".id")
            ->first();

        if (!empty($SingleClassItem))
            return $SingleClassItem->id;

        $SingleClassItem = DB::table(self::getTable())->insertGetId([
            'CompanyNum' => $CompanyNum,
            'MemberShip' => 'BA999',
            'Department' => 2,
            'BalanceClass' => 1,
            'isPaymentForSingleClass' => 1,
            'UserId' => $UserId,
            'ItemName' => lang('single_class_popup') . '-' . $ClassType->__get('Type')
        ]);

        DB::table('items_roles')->insert([
            'CompanyNum' => $CompanyNum,
            'ItemId' => $SingleClassItem,
            'Class' => $ClassTypeId,
            'Group' => 'Class',
            'Item' => 'Class',
            'UserId' => $UserId,
            'GroupId' => $CompanyNum . $SingleClassItem . '-1'
        ]);

        return $SingleClassItem;
    }

    //If not exist, create single class item for ClassType

    /**
     * @param $ClassTypeId
     * @param $CompanyNum
     * @return mixed
     */
    public static function getSingleClassItemByCron($ClassTypeId, $CompanyNum)
    {
        $UserId = 0;
        $ClassType = ClassesType::find($ClassTypeId);

        $SingleClassItem = DB::table(self::getTable())
            ->join('items_roles', 'items_roles.ItemId', '=', self::getTable() . '.id')
            ->where(self::getTable() . '.CompanyNum', $CompanyNum)
            ->where(self::getTable() . '.MemberShip', 'BA999')
            ->where(self::getTable() . '.Department', 2)
            ->where(self::getTable() . '.BalanceClass', 1)
            ->where(self::getTable() . '.isPaymentForSingleClass', 1)
            ->where('items_roles.Class', $ClassTypeId)
            ->select(self::getTable() . ".id")
            ->first();

        if (!empty($SingleClassItem)) {
            return $SingleClassItem->id;
        }

        $SingleClassItem = DB::table(self::getTable())->insertGetId([
            'CompanyNum' => $CompanyNum,
            'MemberShip' => 'BA999',
            'Department' => 2,
            'BalanceClass' => 1,
            'isPaymentForSingleClass' => 1,
            'UserId' => $UserId,
            'ItemName' => lang('single_class_popup') . '-' . $ClassType->__get('Type')
        ]);

        DB::table('items_roles')->insert([
            'CompanyNum' => $CompanyNum,
            'ItemId' => $SingleClassItem,
            'Class' => $ClassTypeId,
            'Group' => 'Class',
            'Item' => 'Class',
            'UserId' => $UserId,
            'GroupId' => $CompanyNum . $SingleClassItem . '-1'
        ]);

        return $SingleClassItem;
    }

    /**
     * @param $ClassTypeId
     * @param $clientId
     * @return string
     */
    public static function getSingleClassItemLead($ClassTypeId, $clientId)
    {
        $CompanyNum = Auth::user()->__get('CompanyNum');
        $UserId = Auth::user()->__get('id');
        $ClassType = ClassesType::find($ClassTypeId);

        $GetActivityCount = DB::table('client_activities')
            ->where('CompanyNum', $CompanyNum)
            ->where('ClientId', $clientId)
            ->where('Department', '3')
            ->where('Status', '!=', '2')
            ->count();

        if ($GetActivityCount > 4) {
            return "overLimitLeadSubscription";
        }

        $SingleClassItem = DB::table(self::getTable())
            ->join('items_roles', 'items_roles.ItemId', '=', self::getTable() . '.id')
            ->where(self::getTable() . '.CompanyNum', $CompanyNum)
            ->where(self::getTable() . '.MemberShip', 'BA999')
            ->where(self::getTable() . '.Department', 3)
            ->where(self::getTable() . '.BalanceClass', 1)
            ->where(self::getTable() . '.isPaymentForSingleClass', 1)
            ->where('items_roles.Class', $ClassTypeId)
            ->select(self::getTable() . ".id")
            ->first();

        if (!empty($SingleClassItem))
            return $SingleClassItem->id;

        $SingleClassItem = DB::table(self::getTable())->insertGetId([
            'CompanyNum' => $CompanyNum,
            'MemberShip' => 'BA999',
            'Department' => 3,
            'BalanceClass' => 1,
            'isPaymentForSingleClass' => 1,
            'UserId' => $UserId,
            'ItemName' => lang('single_class_popup') . '-' . $ClassType->__get('Type')
        ]);

        DB::table('items_roles')->insert([
            'CompanyNum' => $CompanyNum,
            'ItemId' => $SingleClassItem,
            'Class' => $ClassTypeId,
            'Group' => 'Class',
            'Item' => 'Class',
            'UserId' => $UserId,
            'GroupId' => $CompanyNum . $SingleClassItem . '-1'
        ]);

        return $SingleClassItem;
    }

    /**
     * @param $company
     * @return mixed
     */
    public function getMembershipsWithoutSingle($company)
    {
        return DB::table('boostapp.items')
            ->where("Status", "=", 0)
            ->where("CompanyNum", "=", $company)
            ->where('Department', "!=", 4)
            ->where('isPaymentForSingleClass', '!=', 1)
            ->get();
    }


    /**
     * @param $ClubMembershipsId
     * @return mixed
     */
    public function getItemsByClubMemberships($ClubMembershipsId)
    {
        return DB::table($this->table)
            ->select('id', 'Department', 'ItemPrice', 'Vaild', 'Vaild_Type', 'BalanceClass', 'Payment')
            ->where('ClubMembershipsId', '=', $ClubMembershipsId)
            ->where('Status', '=', 0)
            ->where('Department', '!=', 4)
            ->get();
    }

    /**
     * @param $ClubMembershipsId
     * @param $onlyActive
     * @return mixed
     */
    public function getAllItemsIdByClubMemberships($ClubMembershipsId, $onlyActive = false)
    {
        $query = DB::table($this->table)
            ->select('id')
            ->where('ClubMembershipsId', '=', $ClubMembershipsId)
            ->where('Department', '!=', 4)
            ->where('isPaymentForSingleClass', '=', 0);
        if ($onlyActive) {
            $query->where('Status', '=', 0);
        }
        return $query->get();
    }

    /**
     * @param $company
     * @return mixed
     */
    public function getAllSubItemsByCompanyNum($company)
    {
        $paymentsTable = "boostapp.payment_pages";

        return DB::table($this->table)
            ->leftJoin($paymentsTable, $paymentsTable . '.ItemId', '=', $this->table . '.id')
            ->where($this->table . '.CompanyNum', '=', $company)
            ->whereNotNull($this->table . '.ClubMembershipsId')
            ->where($this->table . '.Status', '=', 0)
            ->where($this->table . '.Department', '!=', 4)
            ->select(
                $this->table . '.id',
                $this->table . '.ClubMembershipsId',
                $this->table . '.Department',
                $this->table . '.BalanceClass',
                $this->table . '.Payment',
                $this->table . '.Vaild',
                $this->table . '.Vaild_Type',
                $this->table . '.ItemPrice',
                $paymentsTable . '.PaymentType')
            ->groupBy($this->table . '.id')
            ->orderBy($this->table . '.id')
            ->get();
    }

    /**
     * @param $id
     * @param $status
     * @return mixed
     */
    public function changeStatus($id, $status)
    {
        return DB::table($this->table)
            ->where('id', '=', $id)
            ->update(['Status' => $status]);
    }


    /**
     * @param int $id
     * @param int $status
     * @return int
     */
    public static function changeFavorite(int $id, int $status) :int
    {
        return self::where('id', '=', $id)
            ->update(['Favorite' => $status]);
    }


    /**
     * @param $id
     * @param $status
     * @return mixed
     */
    public function changeDisabledByClubMembershipsId($id, $status)
    {
        return DB::table($this->table)
            ->where('ClubMembershipsId', '=', $id)
            ->update(['Disabled' => $status]);
    }

    /**
     * @param $ClubMembershipsId
     * @return mixed
     */
    public function getItemsLimitMoreDetails($ClubMembershipsId)
    {
        return DB::table($this->table)
            ->select('Display', 'membershipStartCount', 'membershipStartDate', 'membershipAllowLateReg',
                'membershipAllowRelativeDiscount' ,'membershipRelativeDiscount', 'membershipRelativeDiscount' ,'Content', 'Image' )
             ->where('ClubMembershipsId','=',$ClubMembershipsId)
             ->where('Status', 0)
             ->where('isPaymentForSingleClass', 0)
             ->where('Department','!=',4)
             ->first();
    }

    /**
     * @param $ClubMembershipsId
     * @return mixed
     */
    public function getItemsMoreDetails($ClubMembershipsId)
    {
        return DB::table($this->table)
            ->select('CompanyNum', 'MemberShip', 'Brands', 'Display', 'membershipStartCount', 'membershipStartDate', 'membershipAllowLateReg',
                'membershipAllowRelativeDiscount', 'membershipRelativeDiscount', 'membershipRelativeDiscount', 'Content', 'Image')
            ->where('ClubMembershipsId', '=', $ClubMembershipsId)
            ->where('Department', '!=', 4)
            ->first();
    }


    /**
     * @param $item
     * @return mixed
     */
    public function createNewItem($item)
    {
        return DB::table($this->table)->insertGetId(
            $item
        );
    }

    /**
     * @param $data
     * @return mixed
     */
    public function updateCompanyItems($data)
    {
        $CompanyNum = Auth::user()->__get('CompanyNum');
        return DB::table($this->table)->where("CompanyNum", $CompanyNum)->update($data);
    }

    /**
     * @param $company
     * @param $department
     * @return mixed
     */
    public function getCompanyItemsByDepartmentArray($company, $department = [1, 2, 4])
    {
        return DB::table('boostapp.items')
            ->where("CompanyNum", "=", $company)
            ->where("Status", 0)
            ->whereIn('Department', $department)
            ->where('isPaymentForSingleClass', '!=', 1)
            ->select("id", "ItemName", "ItemPrice")
            ->get();
    }

    /**
     * @var string[]
     */
    public static $createRules = [
        'id' => 'integer',
        'CompanyNum' => 'required|integer',
        'ClubMembershipsId' => 'exists:boostapp.club_memberships,id',
        'ItemName' => 'required|max:256',
        'Department' => 'required|integer|between:1,4',
        'ItemPrice' => 'required|numeric|between:0,999999999',
        'Status' => 'integer|between:0,2',
        'UserId' => 'exists:boostapp.users,id',
        'Vaild' => 'integer',
        'Vaild_Type' => 'integer|between:1,4',
        'LimitClass' => 'integer',
        'BalanceClass' => 'integer',
        'Display' => 'integer|between:0,1',
        'Payment' => 'integer|between:1,2',
        'membershipStartCount' => 'integer|between:1,4',
        'membershipStartDate' => 'date_format:Y-m-d',
        'membershipAllowLateReg' => 'integer|between:0,1',
        'membershipAllowRelativeDiscount' => 'integer|between:0,1',
    ];

    /**
     * @param string $clubMembershipName
     * @param array $paramArray
     * @return string
     */
    public function getNewItemName(string $clubMembershipName = '', $paramArray = []): string
    {
        $Payment = isset($paramArray['Payment']) ? $paramArray['Payment'] : $this->Payment;
        $Department = isset($paramArray['Department']) ? $paramArray['Department'] : $this->Department;
        $Vaild = isset($paramArray['Vaild']) ? $paramArray['Vaild'] : $this->Vaild;
        $Vaild_Type = isset($paramArray['Vaild_Type']) ? $paramArray['Vaild_Type'] : $this->Vaild_Type;
        $BalanceClass = isset($paramArray['BalanceClass']) ? $paramArray['BalanceClass'] : $this->BalanceClass;

        $departmentName = ' - ';
        $validText = '';
        if ((int)$Payment === self::PAYMENT_STANDING_ORDER) {
            $departmentName .= lang('standing_order_cycle_charge');
        } else {
            switch ((int)$Department) {
                case self::DEPARTMENT_PERIODIC:
                    $departmentName .= lang('cycle_membership');
                    break;
                case self::DEPARTMENT_TICKET:
                    $departmentName .= lang('class_tabe_card');
                    break;
                case self::DEPARTMENT_TRIAL:
                    $departmentName .= lang('a_trial');
                    break;
            }
            if ($Vaild && $Vaild > 0) {
                $validText = ' - ' . TimeHelper::convertToHumanFriendlyString($Vaild, $Vaild_Type, 1);
            }
        }
        $balanceName = $BalanceClass ? '(' . $BalanceClass . ')' : '';
        return $clubMembershipName . $departmentName . $balanceName . $validText;
    }

    /**
     * @var string[]
     */
    public static $updateRules = [
        'ItemName' => 'max:256',
        'ItemPrice' => 'numeric|between:1,999999999',
        'Vaild' => 'integer',
        'Vaild_Type' => 'integer|between:1,4',
        'LimitClass' => 'integer',
        'BalanceClass' => 'integer',
        'Display' => 'integer|between:0,1',
        'membershipStartCount' => 'integer|between:1,4',
        'membershipStartDate' => 'date_format:Y-m-d',
        'membershipAllowLateReg' => 'integer|between:0,1',
        'membershipAllowRelativeDiscount' => 'integer|between:0,1',
        'Content' => 'max:2000',
        'Image' => 'max:2000',
    ];


    /**
     * @param null $notificationDays
     * @return mixed
     */
    public function returnMemberShipRule($notificationDays=null) {
        return json_encode(["data" => [array_map('strval', [
            "LimitClass" => $this->LimitClass,
            "NotificationDays" => $notificationDays ?? $this->NotificationDays ?? 0,
            "StartTime" => $this->StartTime,
            "EndTime" => $this->EndTime,
            "CancelLimit" => $this->CancelLImit,
            "ClassSameDay" => $this->ClassSameDay,
            "FreezMemberShip" => $this->FreezMemberShip,
            "FreezMemberShipDays" => $this->FreezMemberShipDays,
            "FreezMemberShipCount" => $this->FreezMemberShipCount,
            "LimitClassMorning" => $this->LimitClassMorning,
            "LimitClassEvening" => $this->LimitClassEvening,
            "LimitClassMonth" => $this->LimitClassMonth
        ])]]);
    }

    /**
     * @param string $startDate
     * @return ?string
     */
    public function geEndDate($startDate=''): ?string
    {
        try {
            $startDate = $startDate === '' ? date('Y-m-d') : $startDate;
            if($this->Vaild) {
                $interval = "+ " . $this->Vaild . " " . self::VALID_TYPE_OPTIONS[$this->Vaild_Type];
                return date("Y-m-d", strtotime($interval, strtotime($startDate)));
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * @param $companyNum
     * @return int - 0 not found
     */
    public static function getGeneralItem($companyNum): int
    {
        return self::where('Department', self::DEPARTMENT_PRODUCT)
            ->where('CompanyNum', $companyNum)
            ->where('ItemName', 'פריט כללי')
            ->pluck('id') ?? 0;
    }


    /**
     * @param $companyNum
     * @return int
     */
    public static function createGeneralItem($companyNum): int
    {

        return DB::table(self::getTable())->insertGetId(
            [
                'CompanyNum' => $companyNum,
                'Department' => self::DEPARTMENT_PRODUCT,
                'MemberShip' => 'BA999',
                'ItemName' => 'פריט כללי',
                'ItemPrice' => '0.00',
                'Status' => self::STATUS_OFF
            ],
        );
    }
}
