<?php

require_once __DIR__. '/Client.php';
require_once 'MeetingGeneralSettings.php';

/**
 * @property $id
 * @property $Status
 * @property $GeneralMeetingSettingId
 * @property $TypeGroupCustomers
 * @property $LevelId
 * @property $MinMeetingAmount
 * @property $ClientStatus
 * @property $IsPercentage
 * @property $AfterPurchaseChargeStatus
 * @property $AfterPurchaseChargeAmount
 * @property $ManualChargeStatus
 * @property $ManualChargeAmount
 * @property $ManualChargeTime
 * @property $ManualChargeTimeType
 * @property $NotArriveChargeStatus
 * @property $NotArriveChargeAmount
 * Class MeetingCancellationPolicy
 */
class MeetingCancellationPolicy extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_cancellation_policy';

    const TYPE_GROUP_CUSTOMERS_LEVEL = '0';
    const TYPE_GROUP_CUSTOMERS_ENTRIES = '1';
    const TYPE_GROUP_CUSTOMERS_STATUS = '2';
    const TYPE_GROUP_CUSTOMERS_DEFAULT = '3';

    const CANCELLATION_POLICY_VALUE_STATUS_FREE = 0;
    const CANCELLATION_POLICY_VALUE_STATUS_MANUALLY = 1;
    const CANCELLATION_POLICY_VALUE_STATUS_FULL = 2;

    const TIME_TYPE_HOURS = '1';
    const TIME_TYPE_DAYS = '2';

    /**
     * return array of time types when the key is the time type id
     * @return string[]
     */
    public static function getTimeTypes(): array
    {
        return [
            self::TIME_TYPE_HOURS => 'Hours',
            self::TIME_TYPE_DAYS => 'Days',
        ];
    }

    /**
     * @return string
     */
    public function getManualChargeInterval(): string
    {
        return '-'.$this->ManualChargeTime . ' ' . self::getTimeTypes()[$this->ManualChargeTimeType];
    }


    /**
     * @param $generalMeetingSettingId
     * @return MeetingCancellationPolicy[]
     */
    public static function getAllByGeneralMeetingSettingId($generalMeetingSettingId){
        return self::where('GeneralMeetingSettingId','=',$generalMeetingSettingId)
            ->where('Status', '=', '1')
            ->orderBy('TypeGroupCustomers')
            ->orderBy('MinMeetingAmount', 'desc')
            ->get();
    }

    public function clone(): MeetingCancellationPolicy{
        $NewMeetingCancellationPolicyArray = $this->toArray();
        unset($NewMeetingCancellationPolicyArray['id']);
        return new MeetingCancellationPolicy($NewMeetingCancellationPolicyArray);
    }

    /**
     * @param Client $client
     * @return MeetingCancellationPolicy
     */
    public static function getPolicyForClient(Client $client)
    {
        $settings = MeetingGeneralSettings::getByCompanyNum($client->__get('CompanyNum'));
        $policies = self::getAllByGeneralMeetingSettingId($settings->id);
        foreach ($policies as $policy) {
            if ($policy->TypeGroupCustomers == self::TYPE_GROUP_CUSTOMERS_LEVEL) {
                $ranks = $client->ranks();
                foreach ($ranks as $rank) {
                    if ($rank->RankId == $policy->LevelId) {
                        return $policy;
                    }
                }
            } elseif ($policy->TypeGroupCustomers == self::TYPE_GROUP_CUSTOMERS_ENTRIES) {
                if ($client->entranceCount() >= $policy->MinMeetingAmount) {
                    return $policy;
                }
            } elseif ($policy->TypeGroupCustomers == self::TYPE_GROUP_CUSTOMERS_STATUS) {
                if ($client->__get('Status') == $policy->ClientStatus) {
                    return $policy;
                }
            } elseif ($policy->TypeGroupCustomers == self::TYPE_GROUP_CUSTOMERS_DEFAULT) {
                return $policy;
            }
        }
    }

    public function chargeStatusWhenAmountZero() {
        if($this->ManualChargeAmount == 0 && $this->ManualChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_MANUALLY) {
            $this->ManualChargeStatus = $this::CANCELLATION_POLICY_VALUE_STATUS_FREE;
        }
        if($this->AfterPurchaseChargeAmount == 0 && $this->AfterPurchaseChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_MANUALLY) {
            $this->AfterPurchaseChargeStatus = $this::CANCELLATION_POLICY_VALUE_STATUS_FREE;
        }
        if($this->NotArriveChargeAmount == 0 && $this->NotArriveChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_MANUALLY) {
            $this->NotArriveChargeStatus = $this::CANCELLATION_POLICY_VALUE_STATUS_FREE;
        }

        if($this->ManualChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_FREE) {
            $this->ManualChargeAmount = 0;
        }
        if($this->AfterPurchaseChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_FREE) {
            $this->AfterPurchaseChargeAmount = 0;
        }
        if($this->NotArriveChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_FREE) {
            $this->NotArriveChargeAmount = 0;
        }

        if($this->ManualChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_FULL) {
            $this->ManualChargeAmount = 100;
        }
        if($this->AfterPurchaseChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_FULL) {
            $this->AfterPurchaseChargeAmount = 100;
        }
        if($this->NotArriveChargeStatus == $this::CANCELLATION_POLICY_VALUE_STATUS_FULL) {
            $this->NotArriveChargeAmount = 100;
        }
    }

    /**
     * @param Client $Client
     * @return string|null
     */
    public static function getPolicyIdForClient(Client $Client): ?string
    {
        $Policy = self::getPolicyForClient($Client);
        return $Policy->id ?? null;
    }


    public static $updateDefaultRules =[
        'id' => 'required|integer',
        'Status' => 'integer',
        'GeneralMeetingSettingId' => 'integer',
        'TypeGroupCustomers' => 'integer|between:0,3'
    ];

    public static $updateRules =[
        'id' => 'integer',
        'Status' => 'integer',
        'GeneralMeetingSettingId' => 'integer',
        'TypeGroupCustomers' => 'integer|between:0,3',
        'LevelId' => 'integer',
        'MinMeetingAmount' => 'integer',
        'ClientStatus' => 'integer|between:0,3',
    ];

    public static $createRules =[
        'id' => 'integer',
        'Status' => 'integer|between:0,1',
        'GeneralMeetingSettingId' => 'required|exists:boostapp.meeting_general_settings,id|integer',
        'TypeGroupCustomers' => 'required|integer|between:0,3',
        'LevelId' => 'required_if:TypeGroupCustomers,'.self::TYPE_GROUP_CUSTOMERS_LEVEL.'|exists:boostapp.clientlevel,id|integer',
        'MinMeetingAmount' => 'required_if:TypeGroupCustomers,'.self::TYPE_GROUP_CUSTOMERS_ENTRIES.'|integer|between:0,100',
        'ClientStatus' => 'required_if:TypeGroupCustomers,'.self::TYPE_GROUP_CUSTOMERS_STATUS.'|integer|between:0,2',
        'IsPercentage' => 'required|integer|between:0,1',
        'AfterPurchaseChargeStatus' => 'required|integer|between:0,2',
        'AfterPurchaseChargeAmount' => 'required_if:AfterPurchaseChargeStatus,1|numeric',
        'ManualChargeStatus' => 'required|integer|between:0,2',
        'ManualChargeAmount' => 'required_if:ManualChargeStatus,1|numeric',
        'ManualChargeTime' => 'integer|between:0,100',
        'ManualChargeTimeType' => 'integer|between:0,2',
        'NotArriveChargeStatus' => 'required|integer|between:0,2',
        'NotArriveChargeAmount' => 'required_if:NotArriveChargeStatus,1|numeric',
    ];

}