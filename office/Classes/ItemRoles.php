<?php

require_once "Company.php";
require_once "ClassCalendar.php";
require_once "ClassesType.php";
require_once "Utils.php";

class ItemRoles extends Utils
{
    const GROUP_VALUE_CLASS = 'Class';
    const GROUP_VALUE_MAX = 'Max';
    const GROUP_VALUE_TIME = 'Time';
    const GROUP_VALUE_ITEM = 'Item';


    /**
     * @var $id int
     */
    protected $id;

    /**
     * @var $CompanyNum int
     */
    protected $CompanyNum;
    /**
     * @var $CompanyNum int
     */
    protected $ClubMembershipsId;

    /**
     * @var $ItemId int
     */
    protected $ItemId;

    /**
     * @var $Class string
     */
    protected $Class;

    /**
     * @var $Group string
     */
    protected $Group;

    /**
     * @var $Item string
     */
    protected $Item;

    /**
     * @var $Value string
     */
    protected $Value;

    /**
     * @var $Dates DateTime
     */
    protected $Dates;

    /**
     * @var $UserId int
     */
    protected $UserId;

    /**
     * @var $ChangeDate DateTime
     */
    protected $ChangeDate;

    /**
     * @var $ChangeUserId int
     */
    protected $ChangeUserId;

    /**
     * @var $GroupId string
     */
    protected $GroupId;

    private $table;

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    public function __construct($itemId = null)
    {
        $this->table = "items_roles";
    }

    public function getItemRolesByClassNameAndItemId($companyNum, $itemId, $classNameType) {
        if (empty($classNameType)) {
            return [];
        }

        $res = DB::table($this->table)
            ->where("CompanyNum", $companyNum)
            ->where("ItemId", $itemId)
            ->whereRaw(DB::raw("FIND_IN_SET('$classNameType', Class) > 0"))->get();
        return $res;
    }

    public function getItemRoles($itemId)
    {
        $roles = DB::table("items_roles")->where("itemId", "=", $itemId)->get();
        return $roles;
    }

    public function insertItemRole($data, $ItemsId)
    {
        $user = Auth::user();
        $userId = $user->id;
        $company = Company::getInstance();

        foreach (array_reverse($data["classes"]) as $key => $classBlock) {
            $groupId = $company->__get('CompanyNum') . $ItemsId . "-" . ($key + 1);
            if ($classBlock['classes'] == "all") {
                $classesIds = "BA999";
            } else {
                $classesIds = implode(',', $classBlock['classes']);
            }
            foreach ($classBlock as $limitType => $class) {
                if ($class && $class!="" && $class != 'false') {
                    if ($limitType == "classes") {
                        $groupName = "Class";
                        $itemName = "Class";
                        $value = "";
                        //insert
                        DB::table('items_roles')->insert(
                            array(
                                "CompanyNum" => $company->__get("CompanyNum"),
                                "ItemId" => $ItemsId,
                                "Class" => $classesIds,
                                "Group" => $groupName,
                                "Item" => $itemName,
                                "Value" => $value,
                                "UserId" => $userId,
                                "Dates" => date('Y-m-d H:i:s'),
                                "GroupId" => $groupId
                            )
                        );
                    }
                    if ($limitType == "days") {
                        $groupName = "Day";
                        $itemName = "Days";
                        $value = $this->daysToString($class);
                        //insert
                        DB::table('items_roles')->insert(
                            array(
                                "CompanyNum" => $company->__get("CompanyNum"),
                                "ItemId" => $ItemsId,
                                "Class" => $classesIds,
                                "Group" => $groupName,
                                "Item" => $itemName,
                                "Value" => $value,
                                "UserId" => $userId,
                                "Dates" => date('Y-m-d H:i:s'),
                                "GroupId" => $groupId
                            )
                        );
                    }
                    if ($limitType == "hours"  || $limitType == "extraHours") {
                        $groupName = "Time";
                        $itemName = "Time";
                        $value = array(
                            'data' => array(
                                array(
                                    'FromTime' => $class['from'],
                                    'ToTime' => $class['to']
                                )
                            )
                        );
                        //insert
                        DB::table('items_roles')->insert(
                            array(
                                "CompanyNum" => $company->__get("CompanyNum"),
                                "ItemId" => $ItemsId,
                                "Class" => $classesIds,
                                "Group" => $groupName,
                                "Item" => $itemName,
                                "Value" => json_encode($value, JSON_UNESCAPED_UNICODE),
                                "UserId" => $userId,
                                "Dates" => date('Y-m-d H:i:s'),
                                "GroupId" => $groupId
                            )
                        );
                    }

                    if ($limitType == "maximum") {
                        $groupName = "Max";
                            foreach ($class as $singleMaxLimit) {
                                $itemName = $this->typeToTimeUnit($singleMaxLimit['type']);
                                $value = $singleMaxLimit['number'];
                                //insert
                                DB::table('items_roles')->insert(
                                    array(
                                        "CompanyNum" => $company->__get("CompanyNum"),
                                        "ItemId" => $ItemsId,
                                        "Class" => $classesIds,
                                        "Group" => $groupName,
                                        "Item" => $itemName,
                                        "Value" => $value,
                                        "UserId" => $userId,
                                        "Dates" => date('Y-m-d H:i:s'),
                                        "GroupId" => $groupId
                                    )
                                );
                            }
                    }
                    if ($limitType == "register") {
                        $groupName = "Item";
                        $itemName = "StandBy";
                        $value = array(
                            "data" => array(
                                array(
                                    "StandByCount" => $class["number"], //first input
                                    "StandByVaild_Type" => $class["type"], //First DDl
                                    "StandByTime" => $class["timingNumber"], //second input
                                    "StandByTimeVaild_Type" => $class["timingType"] , //open div = true
                                    "StandByOption" => 1//second ddl
                                )
                            )
                        );
                        DB::table('items_roles')->insert(
                            array(
                                "CompanyNum" => $company->__get("CompanyNum"),
                                "ItemId" => $ItemsId,
                                "Class" => $classesIds,
                                "Group" => $groupName,
                                "Item" => $itemName,
                                "Value" => json_encode($value, JSON_UNESCAPED_UNICODE),
                                "UserId" => $userId,
                                "Dates" => date('Y-m-d H:i:s'),
                                "GroupId" => $groupId
                            )
                        );
                    }
                    if ($limitType == "string") {
                        $role_name = DB::table('items_roles_names')->where("groupId","=",$groupId)->first();
                        if($role_name) {
                            DB::table('items_roles_names')->where("id","=",$role_name->id)->update(
                                array(
                                    "GeneratedString" => $class,
                                    "GroupId" => $groupId,
                                    "ItemId" => $ItemsId
                                )
                            );
                        }
                        else{
                            DB::table('items_roles_names')->insert(
                                array(
                                    "GeneratedString" => $class,
                                    "GroupId" => $groupId,
                                    "ItemId" => $ItemsId
                                )
                            );
                        }
                    }
                }
            }
        }
    }

    public function insertItemRoleObj($ItemRole)
    {
        $itemArr = $this->createArrayFromObj($ItemRole);
        $itemId = DB::table($this->table)->insertGetId($itemArr);
        return $itemId;
    }


    public function checkItemRolesClasses($itemId, $classes){
        $roles = $this->getItemRoles($itemId);
        foreach ($roles as $role){
            if($role->Group == "Class"){
                if($role->Class == "BA999"){
                    continue;
                }
                else{
                    $roleClasses = explode(",",$role->Class);
                    foreach ($classes as $key => $class){
                        if(!in_array($class->ClassNameType,$roleClasses)){
                            unset($classes[$key]);
                        }
                    }
                }
            }
            else if($role->Group == "Time"){
                $times = json_decode($role->Value,true);
                foreach ($classes as $key => $class){
                    $classTime = new DateTime($class->StartTime);
                    $startTime = new DateTime($times["data"][0]["FromTime"]);
                    $endTime = new DateTime($times["data"][0]["ToTime"]);
                    if(!($startTime <= $classTime && $endTime >= $classTime)){
                        unset($classes[$key]);
                    }
                }
            }
        }
        return $classes;
    }

//    private function createArrayFromObj($ItemRole = null)
//    {
//        if ($ItemRole == null) {
//            $ItemRole = $this;
//        }
//        $itemArr = array();
//        foreach ($ItemRole as $key => $value) {
//            if ($value != null && $key != "table") {
//                $itemArr[$key] = $value;
//            }
//        }
//        return $itemArr;
//    }

    public function getItemRoleByClassId($classId)
    {
        $role = DB::table($this->table)->where("Class", "=", $classId)->first();
        if ($role != null) {
            foreach ($role as $key => $value) {
                $this->__set($key, $value);
            }
        }
        return $this;
    }

    public function getItemRolesByItemId($itemId) {
        return DB::table($this->table)->where("itemId", $itemId)->get();
    }

    public function isZoom($classId)
    {
        $class = new ClassCalendar($classId);
        if ($class->__get("is_zoom_class") == "1") {
            return true;
        }
        return false;
    }

    private function daysToString($days)
    {
        $newArr = [];
        foreach ($days as $day) {
            switch ($day) {
                case 0:
                    $newArr[] = "ראשון";
                    break;
                case 1:
                    $newArr[] = "שני";
                    break;
                case 2:
                    $newArr[] = "שלישי";
                    break;
                case 3:
                    $newArr[] = "רביעי";
                    break;
                case 4:
                    $newArr[] = "חמישי";
                    break;
                case 5:
                    $newArr[] = "שישי";
                    break;
                case 6:
                    $newArr[] = "שבת";
                    break;
            }
        }
        $string = implode(',', $newArr);
        return $string;
    }

    private function typeToTimeUnit($type)
    {
        $timeUnit = '';
        switch ($type) {
            case 1:
                $timeUnit .= "Day";
                break;
            case 2:
                $timeUnit .= "Week";
                break;
            case 3:
                $timeUnit .= "Month";
                break;
            case 4:
                $timeUnit .= "Year";
                break;
        }
        return $timeUnit;
    }

    function deleteItemRole($itemId)
    {
        DB::table($this->table)->where('ItemId', '=', $itemId)->delete();
    }

    function deleteItemRoleByid($id)
    {
        DB::table($this->table)->where('id', '=', $id)->delete();
    }
    function generateRolesString($itemRoles){
        $string = "";
        $classType = new ClassesType();
        $classes = array();
        foreach ($itemRoles as $itemRole){
            if(empty($classes)){
                if(isset($itemRole->Class) && $itemRole->Class != "BA999") {
                    $classesIds = explode(",", $itemRole->Class);
                    $classes = $classType->getClassTypesByIds($classesIds);
                }
                else if(isset($itemRole->Class) && $itemRole->Class == "BA999"){
                    $string .= "כל השיעורים";
                }
                foreach ($classes as $class){
                    $string .= $class->Type . ", ";
                }
                $string = str_replace("'", "`", $string);
            }

            if($itemRole->Group == "Max"){
                if($itemRole->Item == "Day"){
                    $string .= " עד " . $itemRole->Value ." הרשמות ביום, ";
                }
                else if($itemRole->Item == "Week"){
                    $string .= " עד " . $itemRole->Value ." הרשמות בשבוע, ";
                }
                else if($itemRole->Item == "Month"){
                    $string .= " עד " . $itemRole->Value ." הרשמות בחודש, ";
                }
                else if($itemRole->Item == "Year"){
                    $string .= " עד " . $itemRole->Value ." הרשמות בשנה, ";
                }
            }
            else if($itemRole->Group == "Day"){
                $string .= " בימים " . $itemRole->Value;
            }
            else if($itemRole->Group == "Time"){
                $times = json_decode($itemRole->Value);
                $string .= " בין השעות " . $times->data[0]->FromTime . "-" . $times->data[0]->ToTime . ",";

            }
            else if($itemRole->Group == "Item") {
                $data = json_decode($itemRole->Value);
                $string .= " הרשמות על בסיס מקום פנוי - ";
                if (isset($data->data[0]->StandByCount)) {
                    $string .= " " . $data->data[0]->StandByCount;
                    if ($data->data[0]->StandByVaild_Type == 1) {
                        $string .= "  הרשמות ביום, ";
                    } else if ($data->data[0]->StandByVaild_Type == 2) {
                        $string .= "  הרשמות בשבוע, ";
                    } else if ($data->data[0]->StandByVaild_Type == 3) {
                        $string .= "  הרשמות בחודש, ";
                    } else if ($data->data[0]->StandByVaild_Type == 4) {
                        $string .= "  הרשמות בשנה, ";
                    }
                    if ($data->data[0]->StandByTimeVaild_Type == 1) {
                        $string .= " כ- " . $data->data[0]->StandByTime;
                        $string .= " דקות ";
                    } else if ($data->data[0]->StandByTimeVaild_Type == 2) {
                        $string .= " כ- " . $data->data[0]->StandByTime;
                        $string .= " שעות ";
                    } else if ($data->data[0]->StandByTimeVaild_Type == 3) {
                        $string .= " כ- " . $data->data[0]->StandByTime;
                        $string .= " ימים ";
                    }
                    $string .= "לפני תחילת השיעור. ";
                }
                else if(isset($data->data[0]->StandByCount)){
                    $string .= " " . $data->data[0]->StandByCount;
                    if ($data->data[0]->StandByVaild_Type == 1) {
                        $string .= "  הרשמות ביום, ";
                    } else if ($data->data[0]->StandByVaild_Type == 2) {
                        $string .= "  הרשמות בשבוע, ";
                    } else if ($data->data[0]->StandByVaild_Type == 3) {
                        $string .= "  הרשמות בחודש, ";
                    } else if ($data->data[0]->StandByVaild_Type == 4) {
                        $string .= "  הרשמות בשנה, ";
                    }
                    if ($data->data[0]->StandByTimeVaild_Type == 1) {
                        $string .= " כ- " . $data->data[0]->StandByTime;
                        $string .= " דקות ";
                    } else if ($data->data[0]->StandByTimeVaild_Type == 2) {
                        $string .= " כ- " . $data->data[0]->StandByTime;
                        $string .= " שעות ";
                    } else if ($data->data[0]->StandByTimeVaild_Type == 3) {
                        $string .= " כ- " . $data->data[0]->StandByTime;
                        $string .= " ימים ";
                    }
                    $string .= "לפני תחילת השיעור. ";
                }
            }
        }

        return $string;
    }

    public function getItemsRolesByClubMemberships($ClubMembershipsId, $itemId=null){
        if(!$itemId) {
            $itemIdFirst =  DB::table($this->table)
                ->select('ItemId')
                ->where('ClubMembershipsId','=',$ClubMembershipsId)
                ->first();
            if(!empty($itemIdFirst) && $itemIdFirst->ItemId) {
                $itemId = $itemIdFirst->ItemId;
            } else {
                return [];
            }
        }
        return  DB::table($this->table)
            ->select('id','Class', 'Group',  'Item', 'Value')
            ->where('ClubMembershipsId','=',$ClubMembershipsId)
            ->where('ItemId','=',$itemId)
            ->orderBy('Class', 'DESC')
            ->orderBy('Group', 'ASC')
            ->get();
    }

    public function createNewItemRoles($itemRoles) {
        if(isset($itemRoles['optionNumber'])) {
            unset($itemRoles['optionNumber']);
        }
        return DB::table($this->table)->insertGetId(
            $itemRoles
        );
    }

    public function updateById($id, $data){
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }


    /**
     * @param $companyNum
     * @param $itemId
     * @param $classTypeId
     * @return bool
     */
    public static function isClassTypeMatchToItem($companyNum, $itemId, $classTypeId): bool
    {
        $code = ClassesType::getEventTypeCode($classTypeId);
        return DB::table('boostapp.items_roles')
            ->where('ItemId', $itemId)
            ->where('CompanyNum', $companyNum)
            ->where(function ($q) use ($classTypeId, $code) {
                $q->whereRaw('FIND_IN_SET(' . $classTypeId . ', Class)')
                    ->Orwhere('Class', 'LIKE', "%".$code."%");
            })->exists();
    }


    /**
     * @param $companyNum
     * @param $itemId
     * @param $classTypeId
     * @param null $groupValue - Class|Max|Time|Item
     * @param null $itemValue
     * @return array
     */
    public static function getAllByItemIdAndClassType($companyNum, $itemId, $classTypeId, $groupValue =null, $itemValue=null): array
    {

        $code = ClassesType::getEventTypeCode($classTypeId);
        $query = DB::table('boostapp.items_roles')
            ->where('ItemId', $itemId)
            ->where('CompanyNum', $companyNum);
        if($groupValue) {
            $query->where('Group',$groupValue);
        }
        if($itemValue) {
            $query->where('Item',$itemValue);
        }
        return $query->where(function ($q) use ($classTypeId, $code) {
            $q->whereRaw('FIND_IN_SET(' . $classTypeId . ', Class)')
                ->Orwhere('Class', 'LIKE', "%".$code."%");
        })->get();
    }

    public static function getFirstGroupClassByItemIdAndClassType($companyNum, $itemId, $classTypeId)
    {
        $code = ClassesType::getEventTypeCode($classTypeId);
        return DB::table('boostapp.items_roles')
            ->where('ItemId', $itemId)
            ->where('CompanyNum', $companyNum)
            ->where('Group', 'Class')
            ->where(function ($q) use ($classTypeId, $code) {
                $q->whereRaw('FIND_IN_SET(' . $classTypeId . ', Class)')
                    ->Orwhere('Class', 'LIKE', "%".$code."%");
            })->first();
    }

    public static $createRules =[
        'id' => 'integer',
        'CompanyNum' => 'required|integer',
        'ClubMembershipsId' => 'exists:boostapp.club_memberships,id',
        'ItemId' => 'required|exists:boostapp.items,id',
        'Class' => 'required',
        'Group' => 'required',
        'Item' => 'required',
        'UserId' => 'exists:boostapp.users,id',
        'ChangeUserId' => 'exists:boostapp.users,id',
    ];

    public static $updateRules =[
        'Value' => 'required_if:Group,==,"Class"',
        'ChangeUserId' => 'exists:boostapp.users,id',
    ];




}
    /*
     * function generateStringFromData() {
  let string = "";
  let id = $("#registerLimitLineId").val();
  if (!id || id == "") {
    id = Date.now();
  }
  let classes = $("#registerPopupClassSelect").val();
  if (classes.includes("all")) {
    classes = "all";
    string += "כל השיעורים, ";
  } else {
    $("#registerLimitPopup .select2-selection__choice").each(function () {
      string += `${$(this).attr("title")}, `;
    });
  }

  let maximum = $(".maxLimitLine").length ? true : false;
  if (maximum) {
    let types = [];
    $(".maxLimitLineType").each(function () {
      types.push($(this).val());
    });
    let emptyMax = false;
    $(".maxLimitLineNumber").each(function () {
      if (!$(this).val() || $(this).val == "") {
        emptyMax = true;
      }
    });
    maximum = [];
    $(".maxLimitLine").each(function () {
      maximum.push({
        type: $(this).find(".maxLimitLineType").val(),
        number: $(this).find(".maxLimitLineNumber").val(),
      });
      string += `עד ${$(this).find(".maxLimitLineNumber").val()} הרשמות ${$(
          this
      )
          .find(".maxLimitLineType option:selected")
          .text()}, `;
    });
  }

  let days = $(".hiddenDaysLimit").hasClass("visible");
  if (days) {
    days = getSelectedDays();
    string += `בימים `;
    $("#registerLimitPopup .limitDayLiSelected").each(function () {
      string += `${$(this).html()}, `;
    });
  }

  let hours = $(".hiddenHoursLimit").hasClass("visible");
  if (hours) {
    hours = {
      from: $("#limitFromHour").val(),
      to: $("#limitToHour").val(),
    };
    string += `בין השעות ${$("#limitToHour").val()}-${$(
        "#limitFromHour"
    ).val()}, `;
  }
  let extraHours = $(".extraHoursLimitContainer").is(":visible");
  if (extraHours) {
    let time1 = parseInt($("#limitFromHour").val().replace(":", ""));
    let time2 = parseInt($("#limitToHour").val().replace(":", ""));
    let time3 = parseInt($("#limitFromHour2").val().replace(":", ""));
    let time4 = parseInt($("#limitToHour2").val().replace(":", ""));

    extraHours = {
      from: $("#limitFromHour2").val(),
      to: $("#limitToHour2").val(),
    };
    string += `ובין השעות ${$("#limitToHour2").val()}-${$(
        "#limitFromHour2"
    ).val()}, `;
  }
  let register = $(".hiddenRegisterLimits").hasClass("visible");
  if (register) {
    register = {
      number: $("#registerLimitNumber").val(),
      type: $("#registerLimitType").val(),
      timingNumber: $("#registerTimingInput").val(),
      timingType: $("#registerLimitTimingType").val(),
    };
    string += "הרשמות על בסיס פנוי, ";
    if (register.number) {
      string += `${$("#registerLimitNumber").val()} הרשמות ${$(
          "#registerLimitType option:selected"
      ).text()} `;
    }
    if (register.timingNumber) {
      string += `כ-${$("#registerTimingInput").val()} ${$(
          "#registerLimitTimingType option:selected"
      ).text()} לפני תחילת השיעור, `;
    }
  }
  string = string.slice(0, string.length - 2);
  string += ".";
  let data = {
    classes,
    maximum,
    days,
    hours,
    extraHours,
    register,
    string,
    id: id,
  };
  return data.string;
}
     *  */
