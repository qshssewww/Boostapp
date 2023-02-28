<?php
/**
 * @property $pageId
 * @property $CompanyNum
 * @property $settings
 * *$
 * Class FBPipelineSettings
 */

class FBPipelineSettings extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.fbpipelinesettings';

    public function getPagesFB($CompanyNum){
        return DB::table($this->table)->where("CompanyNum", $CompanyNum)
            ->select('PageId as id', 'settings')
            ->get();
    }


}

