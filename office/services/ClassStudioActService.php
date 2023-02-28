<?php

require_once __DIR__ . '/LoggerService.php';
require_once __DIR__ . '/../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../Classes/ClientActivities.php';


/**
 * Class ClassStudioActService
 */
class ClassStudioActService extends Utils
{
    public const VALIDATION_ARRAY = [
        'classTypeId' => 'required|exists:boostapp.class_type,id',
        'classId' => 'required|exists:boostapp.classstudio_date,id',
        'clientId' => 'required|exists:boostapp.client,id',
        'activityId' => 'required|exists:boostapp.client_activities,id',
        'deviceId' => 'integer',
        'status' => 'integer|between:0,5',
        'regularClassId' => 'integer',
        'popup' => 'integer|between:0,1',
        'overrideStatus' => 'integer|between:0,1',
    ];


    /**
     * @param array $data
     * @return array
     */
    public static function assignClientToClass(array $data): array
    {
        $validation = self::validate($data);
        if ($validation->fails()) {
            return ['Error' => $validation->messages()->first(), 'status' => self::ERROR_STATUS];
        }
        try {
            //TODO-change sub to Service fun
            $data = (object)$data;
            $response = require '../../office/Classes/subClasses/assignClientToClass.php';
        } catch (Exception $e) {
            if(isset($response['ClientActivityId'])) {
                return ['Error' => $e->getMessage(), 'status' => self::SUCCESS_STATUS];
            }
            return ['Error' => $e->getMessage(), 'status' => self::ERROR_STATUS];
        }
        if(isset($response['Status'], $response['actId']) && $response['Status'] === 'Success') {
            return ["Status" => self::SUCCESS_STATUS, "actId" => $response['actId']];
        }
        return ['Error' => $response['Message'], 'status' => self::ERROR_STATUS];
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function validate($data)
    {
        return Validator::make($data, self::VALIDATION_ARRAY);
    }


    /**
     * @param ClientActivities $ClientActivity
     * @return ClassStudioAct[]|null
     */
    public static function getActByClientActivityId(ClientActivities $ClientActivity): ?array
    {
        return ClassStudioAct::where('CompanyNum', $ClientActivity->CompanyNum)
            ->where('ClientId', $ClientActivity->ClientId)
            ->where('ClientActivitiesId', $ClientActivity->id)
            ->get();
    }





}
