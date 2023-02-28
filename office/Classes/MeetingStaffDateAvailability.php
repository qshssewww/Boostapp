<?php

/**
* @property $id
* @property $RuleAvailabilityId
* @property $Status
* @property $Date
*
* Class MeetingStaffDateAvailability
*/

class MeetingStaffDateAvailability extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_staff_date_availability';

    public function getAllByMeetingStaffRuleId($ruleAvailabilityId) {
        return self::where('RuleAvailabilityId','=',$ruleAvailabilityId)
            ->where('Status','=', '1')
            ->orderBy('Date')
            ->get();
    }
    public function geLastDateByMeetingStaffRuleId($ruleAvailabilityId) {
        return DB::table($this->table)
            ->where('RuleAvailabilityId','=',$ruleAvailabilityId)
            ->max('Date');
    }
    public function geAmountActiveDateByMeetingStaffRuleId($ruleAvailabilityId) {
        return DB::table($this->table)
            ->where('RuleAvailabilityId','=',$ruleAvailabilityId)
            ->where('Status','=',1)
            ->where('Date', '>',  date('Y-m-d'))
            ->count();
    }
    public function updateRuleId($oldRuleId, $newOldId, $startDate , $endDate, $endAmount) {
        //update to new rule id
        $query = self::where('RuleAvailabilityId','=',$oldRuleId)
            ->where('Status','=', '1');
        if ($startDate) {
            $query->where('Date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('Date', '<=', $endDate);
        }
        if ($endAmount) {
            $query->limit($endAmount);
        }
        $query->update(['RuleAvailabilityId' => $newOldId]);
        // remove(change status) all old rule if in the future
        self::where('RuleAvailabilityId','=',$oldRuleId)
            ->where('Status','=', '1')
            ->where('Date', '>=', $startDate)
            ->update(['Status' => 0]);
        // return last date that change
        return self::where('RuleAvailabilityId','=',$newOldId)
            ->where('Status','=', '1')
            ->orderBy('Date','desc')
            ->select('Date')
            ->first();
    }
    public function removeBetweenDate($ruleId,$startDate,$endDate,$endAmount): void
    {
        //update to new rule id
        $query = self::where('RuleAvailabilityId','=',$ruleId)
            ->where('Status','=', '1')
            ->where('Date', '>=', $startDate);
        if ($endDate) {
            $query->where('Date', '<=', $endDate);
        }
        if ($endAmount) {
            $query->limit($endAmount);
        }
        $query->update(['Status' => 0]);
    }

    public static $CreateRules =[
        'id' => 'integer',
        'RuleAvailabilityId' => 'required|exists:boostapp.meeting_staff_rule_availability,id|integer',
        'Status' => 'integer|between:0,1',
        'Date' => 'required|date_format:Y-m-d'
    ];

}