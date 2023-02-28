<?php



require_once "Utils.php";


/**
 * @property $id
 * @property $CompanyNum
 * @property $MaxClient
 * @property $MinClient
 * @property $DefaultStatusClass
 * @property $CheckMinClient
 * @property $CheckMinClientType
 * @property $EndClassTime
 * @property $ReminderTime
 * @property $ReminderTimeType
 * @property $ReminderTimeDayBefore
 * @property $CancelTime
 * @property $CancelTimeType
 * @property $CancelTimeDayBefore
 * @property $WatingListPOPUP
 * @property $RegularNum
 * @property $TypeOfView
 * @property $SplitView
 * @property $PermanentRegistration
 * @property $RegistrationExpiredMembers
 * @property $CancelPermanentRegistration
 * @property $CancelMinimum
 * @property $GuideCheck
 *
 * Class ClassSettings
 */
class ClassSettings extends \Hazzard\Database\Model {

    protected $table = "classsettings";

    public function InsertClassSettingsNewData($arrayData){
        // return DB::table($this->table)->updateOrInsert(['CompanyNum' => $arrayData["CompanyNum"]], $arrayData);
        $data = $this->GetClassSettingsByCompanyNum($arrayData["CompanyNum"]);
        if (isset($data)) {
            $this->UpdateClassSettings($arrayData, $arrayData["CompanyNum"]);
            return $data->id;
        } else {
            $idInsert = DB::table($this->table)
                ->insertGetId($arrayData);
            return $idInsert;
        }
    }

    public function GetClassSettingsByCompanyNum($CompanyNum){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->first();
        return $data;
    }

    public function UpdateClassSettings($arrayData,$CompanyNum)
    {
        $affact = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->update($arrayData);
        return $affact;
    }

    public function TypeOfView($CompanyNum){
        $type = DB::table($this->table)
            ->select('TypeOfView',"SplitView")
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();
        return $type;
    }
    public function SplitView ($CompanyNum)
    {
        $SplitView = DB::table($this->table)
            ->select('SplitView')
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();
        return $SplitView;
    }
    public function getWatingPopUp($CompanyNum) {
        $settings = DB::table($this->table)
            ->select('WatingListPOPUP')
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();
        return $settings->WatingListPOPUP ?? 0;
    }

}
