<?php

class LogMovementService
{
    public static $table = 'boostapp.log';

    const ACTION_CREATE = 0;
    const ACTION_UPDATE = 1;
    const ACTION_DISABLE = 2;
    const ACTION_ENABLE = 3;

    /**
     * @param $Content
     * @param $ClientId
     * @return mixed
     */
    public static function CreateLogMovement($Content, $ClientId)
    {
        return DB::table(self::$table)->insertGetId([
            'UserId' => Auth::user()->id,
            'Text' => $Content,
            'Dates' => date('Y-m-d H:i:s'),
            'ClientId' => $ClientId ?? 0,
            'CompanyNum' => Auth::user()->CompanyNum,
        ]);
    }

    /**
     * @param $Action
     * @param $Name
     * @param $ClientId
     * @return void
     */
    public static function ClubMembershipLog($Action, $Name, $ClientId)
    {
        switch ($Action) {
            case self::ACTION_CREATE:
                $Content = 'הוקמה חברות מועדון חדשה - ';
                break;
            case self::ACTION_UPDATE:
                $Content = 'בוצעו שינויים בחברות מועדון - ';
                break;
            case self::ACTION_DISABLE:
            case self::ACTION_ENABLE:
                $Content = 'עודכן סטטוס בחברות מועדון - ';
                break;
            default:
                $Content = '';
        }

        $Content .= $Name;

        if ($Action == self::ACTION_DISABLE) {
            $Content .= " לסטטוס מושהה";
        } elseif ($Action == self::ACTION_ENABLE) {
            $Content .= " לסטטוס פעיל";
        }

        return self::CreateLogMovement($Content, $ClientId);
    }
}
