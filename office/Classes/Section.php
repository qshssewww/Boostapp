<?php

require_once "Company.php";

/**
 * @property $id
 * @property $Title
 * @property $eventColor
 * @property $Status
 * @property $double
 * @property $Floor
 * @property $CompanyNum
 * @property $Brands
 * @property $Private
 * @property $MaxClient
 * Class Section
 */
class Section extends \Hazzard\Database\Model
{
    protected $table = "boostapp.sections";

    public const STATUS_ACTIVE = 0;
    public const STATUS_OFF = 1;

    /**
     * @param $data
     * @return mixed
     */
    public static function insertNewSection($data)
    {
        $insertedId = DB::table(self::getTable())->insertGetId([
                "Brands" => $data["Brands"],
                "Title" => $data["Title"],
                "outdoor" => $data["Outdoor"],
                "SpaceType" => $data["SpaceType"] ?? 0,
                "CompanyNum" => Company::getInstance()->CompanyNum,
            ]
        );
        return $insertedId;
    }

    /**
     * @param $id
     * @param $data
     * @return int
     */
    public function editSection($id, $data)
    {
        self::where("id", "=", $id)->update($data);

        $res = self::where("id", "=", $id)->get();

        if (count($res) > 0) {
            return $res;
        }

        return false;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSectionById($id)
    {
        return DB::table($this->table)->where("id", $id)->first();
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function getSectionByIds($ids)
    {
        return DB::table($this->table)->whereIn("id", $ids)->get();
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function getCalendarsByCompanyNum($CompanyNum)
    {
        $res = DB::table($this->table)
            ->select(
                $this->table . '.id',
                $this->table . '.Title',
                $this->table . '.Status',
                $this->table . '.outdoor',
                $this->table . '.Brands',
                $this->table . '.SpaceType',
                'boostapp.class_type.Price',
                'boostapp.tags_section.tags_id',
            )
            ->leftjoin("boostapp.class_type", $this->table . ".id", "=", "boostapp.class_type.SectionId")
            ->leftjoin("boostapp.tags_section", $this->table . ".id", "=", "boostapp.tags_section.sections_id")
            ->where($this->table . '.CompanyNum', '=', $CompanyNum)
            ->get();
        foreach ($res as $space) {
            if (isset($space->Price)) {
                $space->Price = (int)$space->Price;
            }
            if (isset($space->tags_id)) {
                $space->tagId = (int)$space->tags_id;
            }
        }
        return $res;
    }


    /** return the number of 'Calendars' in the company
     * @param $CompanyNum
     * @return mixed
     */
    public static function countActive($CompanyNum)
    {
        return
            self::where('CompanyNum', '=', $CompanyNum)
                ->where('Status', '=', 0)
                ->count();
    }

    /**
     * Return the number of 'Calendars' in the company in specified branch
     * @param $CompanyNum
     * @param $Branch
     * @return mixed
     */
    public static function countActiveByBranch($CompanyNum, $Branch)
    {
        return
            self::where('CompanyNum', '=', $CompanyNum)
                ->where('Brands', '=', $Branch)
                ->where('Status', '=', 0)
                ->count();
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function getCalendarsByCompanyNumByBrandsOrder($CompanyNum)
    {
        $res = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', 0)
            ->orderBy('Brands', 'ASC')
            ->get();
        return $res;
    }

    /**
     * @param $CompanyNum
     * @param $id
     * @param $display
     * @return mixed
     */
    public function hideSection($CompanyNum, $id, $display)
    {
        $res = DB::table($this->table)
            ->where("id", "=", $id)
            ->where('CompanyNum', '=', $CompanyNum)
            ->update(array("Status" => $display));
        return $res;
    }

    /**
     * @param $CompanyNum
     * @param $Branch
     * @return mixed
     */
    public function GetRoomsByBranch($CompanyNum, $Branch)
    {
        return
            DB::table($this->table)
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('Status', '=', 0)
                ->where('Brands', '=', $Branch)
                ->get();
    }

    /**
     * @param $CompanyNum
     * @param $Branch
     * @return mixed
     */
    public function GetAllRoomsByBranch($CompanyNum, $Branch)
    {
        return
            DB::table($this->table)
                ->where('CompanyNum', $CompanyNum)
                ->where('Brands', $Branch)
                ->get();
    }

    /**
     * Check if section is taken for particular time
     * @param $dateArr array of dates
     * @param $StartTime string
     * @param $EndTime string
     * @param null $GroupNumber string
     * @return mixed
     */
    public function isOccupied($dateArr, $StartTime, $EndTime, $GroupNumber = null)
    {
        $query =
            DB::table('classstudio_date')
                ->where('Floor', '=', $this->id)
                ->where('CompanyNum', '=', $this->CompanyNum)
                ->whereIn('StartDate', $dateArr)
                ->where('EndTime', '!=', '00:00:00')
                ->where('Status', '!=', '2')->where(function ($q) use ($StartTime, $EndTime) {
                    $q->where('StartTime', '>=', $StartTime)->where('EndTime', '<=', $EndTime)
                        ->orWhere('StartTime', '<', $EndTime)->where('EndTime', '>', $StartTime);
                });

        if ($GroupNumber) {
            $query = $query->where('GroupNumber', '!=', $GroupNumber);
        }

        return $query->first();
    }

    /**
     * @param $dateArr
     * @param $StartTime
     * @param $EndTime
     * @param $GroupNumber
     * @return mixed
     */
    public function getOccupied($dateArr, $StartTime, $EndTime, $GroupNumber = null)
    {
        $res = [];
        foreach ($dateArr as $date) {
            $query = ClassStudioDate::where('Floor', '=', $this->id)
                ->where('CompanyNum', '=', $this->CompanyNum)
                ->where('StartDate', '=', $date)
                ->where('EndTime', '!=', '00:00:00')
                ->where('Status', '!=', '2')
                ->where(function ($q) use ($StartTime, $EndTime) {
                    $q->where('StartTime', '>=', $StartTime)->where('EndTime', '<=', $EndTime)
                        ->orWhere('StartTime', '<', $EndTime)->where('EndTime', '>', $StartTime);
                });

            if ($GroupNumber) {
                $query = $query->where('GroupNumber', '!=', $GroupNumber);
            }

            $ClassStudioDates = $query->orderBy('StartTime')->get();
            foreach ($ClassStudioDates as $ClassStudioDate) {
                $res[] = $ClassStudioDate->toArray();
            }
        }
        return $res;
    }

    //get all brands and calendars
    /**
     * @param $companyNum
     * @return Section[]
     */
    public static function getAllBrandAndCalendars($companyNum): array
    {
        return self::select('brands.id', 'brands.BrandName', 'sections.Title', 'sections.id AS sectionsId')
            ->leftJoin("boostapp.brands", "sections.Brands", "=", "brands.id")
            ->where('sections.CompanyNum', '=', $companyNum)
            ->where('sections.Status', '=', 0)
            ->where(function ($q) {
                $q->where('brands.Status', '!=', 1)
                    ->orWhereNull('brands.Status');
            })
            ->get();
    }

    /**
     * @param $companyNum
     * @param int $sectionId
     * @return string|null
     */
    public static function getSectionName($companyNum, int $sectionId): ?string
    {
         return self::where('CompanyNum',"=",$companyNum)
            ->where('id', "=" , $sectionId)
            ->Pluck('Title') ?? '';
    }

    /**
     * @param $companyNum
     * @return int
     */
    public static function getFirstFloor($companyNum): int
    {
        return self::where('CompanyNum',"=",$companyNum)
                ->where('Status', self::STATUS_ACTIVE)
                ->Pluck('id') ?? 0;
    }

}
