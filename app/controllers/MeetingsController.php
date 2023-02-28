<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../enums/ClassStudioDate/MeetingStatus.php';
require_once __DIR__ . '/../helpers/PhoneHelper.php';
require_once __DIR__ . '/../../office/services/LoggerService.php';
require_once __DIR__ . '/../../office/Classes/Brand.php';
require_once __DIR__ . '/../../office/Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../office/Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../office/Classes/Client.php';
require_once __DIR__ . '/../../office/Classes/ClientActivities.php';
require_once __DIR__ . '/../../office/Classes/Company.php';
require_once __DIR__ . '/../../office/Classes/DocsClientActivities.php';
require_once __DIR__ . '/../../office/Classes/MeetingCancellationPolicy.php';
require_once __DIR__ . '/../../office/Classes/MeetingClient.php';
require_once __DIR__ . '/../../office/Classes/MeetingGeneralSettings.php';
require_once __DIR__ . '/../../office/Classes/MeetingGroupOrdersToAct.php';
require_once __DIR__ . '/../../office/Classes/MeetingTemplates.php';
require_once __DIR__ . '/../../office/Classes/Section.php';
require_once __DIR__ . '/../../office/Classes/Token.php';
require_once __DIR__ . '/../../office/Classes/Users.php';

/**
 * @class MeetingsController
 */
class MeetingsController extends BaseController
{
    /**
     * @param string $type
     * @param null $limit
     * @param null $lastDate
     * @param bool $withSettings
     * @return bool
     */
    public function meetingsData(string $type = MeetingService::TYPE_ALL, $limit = null, $lastDate = null, bool $withSettings = false)
    {
        try {
            $CompanyNum = Auth::user()->CompanyNum;

            $result = [
                'status' => 200,
                'message' => 'success',
                'success' => true,
            ];

            $meetingsData = MeetingService::getMeetingsData($CompanyNum, $type, $limit, $lastDate, $withSettings);

            $result = array_merge($result, $meetingsData);
        } catch (\Throwable $e) {
            LoggerService::error($e);

            $result = [
                'status' => 500,
                'message' => 'error',
                'success' => false,
            ];
        }

        return $this->json($result);
    }

    /**
     * @return bool
     */
    public function meetingsWaitingCount()
    {
        try {
            if(Auth::check()) {
                $CompanyNum = Auth::user()->CompanyNum;
                $result = [
                    'status' => 200,
                    'message' => 'success',
                    'success' => true,
                    'notApprovedMaxCount' => MeetingService::getMeetingsCountByType($CompanyNum, MeetingService::TYPE_NOT_APPROVED),
                ];
            } else {
                $result = [
                    'status' => 500,
                    'message' => 'error',
                    'success' => false,
                ];
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);

            $result = [
                'status' => 500,
                'message' => 'error',
                'success' => false,
            ];
        }

        return $this->json($result);
    }
}
