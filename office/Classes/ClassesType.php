<?php

require_once "ClassCalendar.php";
require_once "Company.php";
require_once "ItemRoles.php";
require_once __DIR__ . '/../../app/enums/ClassType/EventType.php';

/**
 * @property $id
 * @property $Type
 * @property $Status
 * @property $EventType
 * @property $CompanyNum
 * @property $MeetingTemplateId
 * @property $SectionId
 * @property $Count
 * @property $ClassContent
 * @property $Color
 * @property $Color2
 * @property $memberships
 * @property $duration
 * @property $durationType
 * @property $Price
 * @property $CreatedDate
 * @property $EditDate
 * @property $Favorite
 *
 * Class ClassesType
 */
class ClassesType extends \Hazzard\Database\Model
{

    public const STATUS_ACTIVE = 0;
    public const STATUS_OFF = 1;

    protected $table = "boostapp.class_type";

    /**
     * @var mixed|string
     */
    private static function searchForId($id, $array) {
        foreach ($array as $key => $val) {
            if ($val->ItemId == $id) {
                return $key;
            }
        }
        return null;
    }

    public function getClassCalendarArray()
    {
        $ClassCalendar = new ClassCalendar();
        $this->ClassCalendar = $ClassCalendar->getClassCalendar($this->id, $this->CompanyNum);
    }

    public function setClassTypeObjectById($id)
    {
        $class = DB::table($this->table)->where("id", "=", $id)->first();
        if ($class != null) {
            foreach ($class as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public static function insertNewClassType($data)
    {
        $insertedId = DB::table('boostapp.class_type')->insertGetId($data);
        return $insertedId;
    }
    public function GetClassesTypeByCompanyNum($CompanyNum){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('Status','=',0)->orderBy('Type', 'ASC')
            ->get();
        return $data;
    }

    public function getClassesTypeOnlyLessons($CompanyNum){
        return DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->where('Status','=',0)
            ->where('EventType', '=', 0)
            ->orderBy('Type', 'ASC')
            ->get();
    }

    private function checkClassType($id) {
        $classType = DB::table("boostapp.class_type")->where("id", $id)->first();
        return isset($classType) && $classType->CompanyNum == Company::getInstance()->CompanyNum;
    }

    public function deleteMoveClassType($data) {
        if (!$this->checkClassType($data["id"])) {
            return null;
        }
        if(isset($data["otherId"])) {
            if (!$this->checkClassType($data["otherId"])) {
                return null;
            }
            $res[] = DB::table("boostapp.classstudio_date")->where('ClassNameType','=',$data["id"])->update(array(
                "ClassNameType"=>$data["otherId"]
            ));
        }else{
            $res[] = DB::table('boostapp.classstudio_date')
                ->where('ClassNameType','=',$data["id"])
                ->where('Status',"=","0")
                ->update(array('Status' => '2', 'displayCancel' => '1'));
        }
        $res[] = DB::table("boostapp.class_type")->where("id","=",$data["id"])->update(array(
            "Status" => 1,
            "EditDate" => date('Y-m-d H:i:s')
        ));
        return count($res);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function editClassType(array $data = []):bool
    {
        try {
            if(!isset($data['id']) || count($data) <= 1) {
                return false;
            }
            $data['EditDate'] = date('Y-m-d H:i:s');
            $memberships = $data["memberships"] ?? [];
            unset($data["id"], $data["memberships"]);

            foreach ($data as $k => $value) {
                $this->$k = $value;
            }

            $this->save();
            $MembershipsInDB = $this->getMembershipsIdByClassType();
            $membershipsArrayFromDb = [];
            if($MembershipsInDB) {
                foreach($MembershipsInDB as $membership){
                    $membershipsArrayFromDb[] = $membership->ItemId;
                }
            }
            $itemsToDelete = array_diff($membershipsArrayFromDb,$memberships);
            $itemsToInsert = array_diff($memberships,$membershipsArrayFromDb);
            foreach($itemsToInsert as $membership){
                DB::table('boostapp.items_roles')
                    ->where("ItemId",'=',$membership)
//                    ->where('GroupId', '=', Auth::user()->CompanyNum.$membership.'-1')
                    ->where('Class', 'NOT LIKE', '%'.EventType::ALL_CLASSES.'%')->update(
                        array(
                            "Class"=>DB::raw('CONCAT(Class,",'.$this->id.'")')
                        )
                    );
            }
            foreach($itemsToDelete as $membership){
                $MembershipClasses = DB::table('boostapp.items_roles')
                    ->where("ItemId",'=',$membership)
                    ->get();
                foreach ($MembershipClasses as $MembershipClass){
                    $lessons = explode(',', $MembershipClass->Class);
                    if (in_array($this->id, $lessons)) {
                        DB::table('boostapp.items_roles')
                            ->where('id', '=', $MembershipClass->id)
                            ->where('Class', 'NOT LIKE', '%BA999%')->update(
                            array(
                                "Class"=>DB::raw("TRIM(BOTH ',' FROM REPLACE(CONCAT(',', Class, ','), ',$this->id,', ','))")
                            )
                        );
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get the classType id and returns the Event type code
     * @param $classTypeId
     * @return string
     */
    public static function getEventTypeCode($classTypeId):string
    {
        $eventType = self::getEventType($classTypeId);
        $code = EventType::code($eventType);
        if($code === null) {
            $code = EventType::ALL_CLASSES;
        }
        return $code;
    }

    /**
     * Gets the classType id and returns the Event type (EventType Enum)
     * @param $classTypeId
     * @return int | null
     */
    public static function getEventType($classTypeId): ?int
    {
        return self::where('id','=',$classTypeId)
            ->pluck('EventType');
    }

    /**
     * @param int $eventType
     * @param bool $includeBA999
     * @return array
     */
    public function getMembershipsIdByClassType($eventType = EventType::EVENT_TYPE_CLASSES, bool $includeBA999 = false): array
    {
        $companyNum = Auth::user()->CompanyNum;
        $results = self::getMemberships($companyNum, $includeBA999);
        $filtered = array();
        $code = EventType::code($eventType);
        foreach ($results as $res){
            $lessons = explode(',', $res->Class);
            if (in_array($this->id, $lessons) || in_array($code, $lessons)){
                $filtered[] = $res;
            }
        }
        return $filtered;
    }


    public function getAllClassTypes($data) {
        $res = DB::table('boostapp.class_type')
            ->where("Status", "=", "0")
            ->where("EventType", "=", "0")
            ->where('CompanyNum',"=",$data["CompanyNum"])
            ->get();
        return $res;
    }

    public function getAllClassTypesArr($companyNum){
        $classArr = array();
        $classTypes = DB::table('boostapp.class_type')
            ->select('id')
            ->where('CompanyNum', $companyNum)
            ->get();
        foreach ($classTypes as $type) {
            array_push($classArr, $type->id);
        }
        return $classArr;
    }

    public function getColors() {
        $colors = DB::table('boostapp.colors')->where("calendar", "=", 1)->get();
        return $colors;
    }

    //todo need to move to items_roles Model
    /**
     * Get all unique items with class limit
     * @param $companyNum
     * @param bool $includeBA999
     * @return array
     */
    public static function getMemberships($companyNum, bool $includeBA999 = false): array {
        $q = DB::table('items_roles')
            ->join('items', 'items.id', '=', 'items_roles.ItemId')
            ->leftJoin('membership_type', function ($join) {
                $join->on('membership_type.id', '=', 'items.MemberShip')->on('membership_type.CompanyNum', '=', 'items.CompanyNum');
            })
            ->where("items_roles.Item", "=", "Class")
            ->where('items_roles.CompanyNum', "=", $companyNum)
            ->where('items.isPaymentForSingleClass', '=', 0)
            ->where("items.Status", "=", 0)
            ->where("items.Disabled", '=', '0')
            ->where("membership_type.Status", 0)
            ->where('items.Department', "!=", 4);
        if(!$includeBA999) {
            $q = $q->where('items_roles.Class', 'NOT LIKE', '%'.EventType::ALL_CLASSES.'%');
        }
        return $q->select('items_roles.ItemId','items_roles.Class', 'items.ItemName', 'items.ItemPrice', 'items.Department')
            ->groupBy('items_roles.ItemId')
            ->get();
    }

    /**
     * @param int $eventType
     * @return int
     */
    public function countItemsLink(int $eventType = EventType::EVENT_TYPE_CLASSES): int
    {
        return count($this->getMembershipsIdByClassType($eventType));
    }

    public function getClassTypesByIds($classesType){
        return DB::table("boostapp.class_type")->whereIn("id",$classesType)->get();
    }

    /**
     * @param $classesType
     * @return mixed
     */
    public function getClassTypesWithFullNameByIds($classesType)
    {
        $classTypeArray = DB::table("boostapp.class_type")
            ->select($this->table . ".*", "mt.TemplateName")
            ->leftJoin('boostapp.meeting_templates as mt', 'class_type.MeetingTemplateId', '=', 'mt.id')
            ->whereIn("class_type.id", $classesType)
            ->get();
        if (!empty($classTypeArray)) {
            foreach ($classTypeArray as $classType) {
                try {
                    if ($classType->MeetingTemplateId) {
                        $classType->Type = $classType->TemplateName . ' | ' . explode("|", $classType->Type)[0];
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        return $classTypeArray;
    }

    public function getClassTypeById($id){
        return DB::table("boostapp.class_type")->where("id",$id)->first();
    }


    /**
     * Adding new class type and associating it to all the provided memberships
     * @param $data array Containing the matching values to the columns in `class_type`
     * and key 'memberships' if the class type should be linked to memberships (array of membership ids)
     * @return mixed id of the inserted class type
     */
    public function insertSingleClassType(array $data) {
        $memberships = empty($data["memberships"]) ? [] : $data["memberships"];
        unset($data["memberships"]);
        $res = DB::table('boostapp.class_type')->insertGetId($data);
        foreach($memberships as $member){
            DB::table('boostapp.items_roles')->where("ItemId",'=',$member)->where('GroupId', '=', $data['CompanyNum'].$member.'-1')->update(
                array(
                    "Class"=>DB::raw('CONCAT(Class,",'.$res.'")')
                )
            );
        }
        return $res;
    }


    /**
     * @param $meetingTemplateId
     * @return ClassesType[]
     */
    public static function getAllByMeetingTemplateId($meetingTemplateId):array
    {
        return self::where('MeetingTemplateId','=',$meetingTemplateId)
            ->where('EventType','=','1')
            ->where('Status','=', '0')
            ->orderByRaw('CAST(duration as UNSIGNED)')
            ->get();
    }

    public function getAllClassTypeForSelect($companyNum){
        return DB::table($this->table)
            ->leftJoin('meeting_templates as mt', $this->table.'.MeetingTemplateId', '=','mt.id')
            ->leftJoin('meeting_category as mc', 'mt.CategoryId', '=','mc.id')
            ->where($this->table.'.CompanyNum', $companyNum)
            ->where($this->table.'.Status', '=' ,0)
            ->where(function($query) {
                $query->where('mt.Status', '!=', '0')->orWhereNull('mt.Status');
            })
            ->where(function($query) {
                $query->where('mc.Status', '!=', '0')->orWhereNull('mc.Status');
            })
            ->orderBy($this->table.'.EventType')
            ->orderBy('mc.id')
            ->orderBy('mt.id')
            ->select($this->table.'.id',$this->table.'.Type',$this->table.'.EventType',$this->table.'.CompanyNum',
                $this->table.'.MeetingTemplateId',$this->table.'.SectionId', 'mt.TemplateName','mc.id as category_id', 'mc.CategoryName')
            ->get();
    }




    //validation role of create new
    public static $createRules =[
        'MeetingTemplateId' => 'required|exists:boostapp.meeting_templates,id',
        'Status' => 'integer|between:0,1',
        'EventType' => 'integer|between:0,2',
        'durationType' => 'integer|between:0,1',
        'duration' => 'required|numeric',
        'Price' => 'required|numeric',
        'Type' => 'required|min:1|max:100',
        'CompanyNum' => 'integer'
    ];


    public static function updateOrCreateClassTypeBySectionId($id, array $data, $companyNum) {
        $fullDateNow = date('Y-m-d H:i:s');
        if(!empty($data)) {
            $data['EditDate'] = $fullDateNow;
        }
        $isForUpdate = self::where('SectionId', $id)
            ->where('CompanyNum',$companyNum)
            ->exists();
        if($isForUpdate) {
            self::where('SectionId', $id)
                ->where('CompanyNum',$companyNum)
                ->update($data);
        } else if(isset($data['EventType']) && $data['EventType'] === 2) {
            $data['CompanyNum'] = $companyNum;
            $data['CreatedDate'] = $fullDateNow;
            $data += ['SectionId' => $id];
            self::insertGetId($data);
        }
    }


    public static function getByType ($lessonTypeId) {
        $typeText = self::find($lessonTypeId)->Type ?? null;
        if ($typeText) {
            $arrayOfObj = self::where('Type','like', '%'.$typeText.'%')->select('id')->get();
            $arrayOfTypesId = [];
            foreach ($arrayOfObj as $type) {
                $arrayOfTypesId[] = $type;
            }
            return $arrayOfTypesId;
        }
        return [];
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

}
