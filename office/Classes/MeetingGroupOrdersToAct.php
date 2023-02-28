<?php

require_once __DIR__ . '/ClassStudioAct.php';

/**
 * @property $id
 * @property $MeetingGroupOrdersId
 * @property $ClassStudioActId
 * @property $CreateDate
 * @property $EditDate
 *
 * Class MeetingGroupOrdersToAct
 */
class MeetingGroupOrdersToAct extends \Hazzard\Database\Model
{
    protected $table = 'meeting_group_orders_to_act';

    private $_classStudioAct;

    /**
     * @return \Hazzard\Database\Model|ClassStudioAct|null
     */
    public function classStudioAct()
    {
        if (!$this->_classStudioAct) {
            $this->_classStudioAct = ClassStudioAct::find($this->ClassStudioActId);
        }

        return $this->_classStudioAct;
    }
}
