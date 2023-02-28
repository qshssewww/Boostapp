<?php
require_once __DIR__ . '/MeetingGroupOrdersToAct.php';
/**
 * @property $id
 * @property $CompanyNum
 * @property $ClientId
 * @property $CountMeetingsToOrder
 * @property $CreateDate
 */
class MeetingGroupOrders extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_group_orders';

    public static function createMeetingGroupOrder(int $companyNum, int $clientId, array $actIds){

        foreach ($actIds as $actId) {
            $meetingGroupOrderToAct = MeetingGroupOrdersToAct::where('ClassStudioActId', $actId)->first();

            if ($meetingGroupOrderToAct) {
                return $meetingGroupOrderToAct->MeetingGroupOrdersId;
            }
        }

        $meetingGroupOrderId = MeetingGroupOrders::insertGetId([
            'CompanyNum' => $companyNum,
            'ClientId' => $clientId,
            'CountMeetingsToOrder' => count($actIds)
        ]);

        foreach ($actIds as $actId) {
            MeetingGroupOrdersToAct::insert([
                'MeetingGroupOrdersId' => $meetingGroupOrderId,
                'ClassStudioActId' => $actId,
            ]);
        }

        return $meetingGroupOrderId;
    }
}
