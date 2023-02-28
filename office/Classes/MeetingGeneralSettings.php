<?php

/**
 * @property $CompanyNum
 * @property $SendReminder Send reminders to clients before the meeting begins (Yes = 1/No = 0)
 * @property $TypeReminder Scheduling the reminder Type (days = 1 or hours = 0 before the meeting)
 * @property $TimeReminder Scheduling the reminder Time (number of days or hours)
 * @property $CloseWithoutInvoice Close the meeting without invoice
 * @property $SlotBlockType
 * @property $SlotBlockValue Time increments for setting up a meeting within an hour
 * @property $AllowFullPrePayment Allow full payment in advance
 * @property $PreOrder
 * @property $PreOrderType
 * @property $PreOrderTime How long before allow to make an appointment?
 * @property $AutoApproval Schedule appointments in an external order
 * @property $AssociateCoach Assignment of a coach by external invitation
 * @property $CreatedDate
 * @property $EditDate
 * @property $AllowsCoincideSection
 *
 * Class MeetingGeneralSettings
 */
class MeetingGeneralSettings extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_general_settings';

    public const SEND_REMINDER_ON = 1;
    public const SEND_REMINDER_OFF = 0;

    public static $updateRules = [
        'id' => 'required|integer',
        'CompanyNum' => 'integer',
        'SendReminder' => 'integer|between:0,1',
//        'TypeReminder' => 'integer|between:0,1',
        'TypeReminder' => 'integer|between:0,0',
        'TimeReminder' => 'integer|between:1,24',
        'SlotBlockTyp' => 'integer|between:0,1',
        'SlotBlockVal' => 'integer',
        'PreOrder' => 'integer|between:0,1',
        'PreOrderType' => 'integer|between:0,1',
        'PreOrderTime' => 'integer|between:0,12',
        'AutoApproval' => 'integer|between:0,1',
        'AllowFullPrePayment' => 'integer|between:0,1',
        'AssociateCoach' => 'integer|between:0,1',
        'AllowsCoincideSection' => 'integer|between:0,1',
    ];

    /**
     * @param $companyNum
     * @return self
     */
    public static function getByCompanyNum($companyNum)
    {
        return self::where('CompanyNum', '=', $companyNum)->first();
    }

    /**
     * @return string interval for the notification (how much time before class)
     */
    public function getReminderInterval()
    {
        $interval = '-' . $this->TimeReminder;
        if ($this->TypeReminder == 0) {
            $interval .= ' hours';
        } else if ($this->TypeReminder == 1) {
            $interval .= ' days';
        }
        return $interval;
    }
}
