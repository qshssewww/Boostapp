<?php


require_once "Utils.php";

/**
 * @property $id
 * @property $class_id
 * @property $template_id
 * @property $membership_type
 * @property $CompanyNum
 * @property $meeting_id
 * @property $single_reg
 * @property $single_price
 * @property $video_link
 * @property $external_video
 * @property $save_video
 * @property $video_folder
 * @property $chat
 * @property $share_video
 * @property $audio
 * @property $password
 * @property $date
 * @property $update_date
 */
class ClassZoom extends \Hazzard\Database\Model
{
    protected $table = "class_zoom";

    /**
     * @param $arrayData
     * @return mixed
     */
    public function InsertClassZoomNewData($arrayData)
    {
        return self::insertGetId($arrayData);
    }

    /**
     * @param $templateId
     * @param $CompanyNum
     * @return mixed
     */
    public function GetClassZoomByTemplateId($templateId, $CompanyNum)
    {
        return self::where('CompanyNum','=',$CompanyNum)
            ->where('template_id','=',$templateId)
            ->first();
    }

    /**
     * @param $classId
     * @param $CompanyNum
     * @return mixed
     */
    public static function getByClassId($classId, $CompanyNum)
    {
        return self::where('CompanyNum', $CompanyNum)
            ->where('class_id', $classId)
            ->first();
    }
}