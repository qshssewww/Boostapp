<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../office/Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../office/Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../office/Classes/ClassZoom.php';
require_once __DIR__ . '/../../office/Classes/MeetingStaffRuleAvailability.php';
require_once __DIR__ . '/../../office/Classes/ClassOnline.php';
require_once __DIR__ . '/../../office/Classes/Client.php';
require_once __DIR__ . '/../../office/Classes/AppNotification.php';
require_once __DIR__ . '/../../office/Classes/Utils.php';

class SaveStudioDateController extends BaseController
{
    /**
     * @param array $data
     * @return bool
     */
    public function updateMeeting(array $data): bool
    {
        /** @var ClassStudioDate $meeting */
        $meeting = ClassStudioDate::find($data['id']);

        return $this->json($meeting->updateMeeting($data));
    }

    /**
     * @param array $data
     * @return bool
     */
    public function createMeeting(array $data): bool
    {
        return $this->json(ClassStudioDate::createMeeting($data));
    }

    /**
     * @param array $data
     * @return bool
     */
    public function deleteBlockEvent(array $data): bool
    {
        $blockId = $data['id'] ?? null;
        return $this->json(ClassStudioDate::deleteBlockEvent($blockId));
    }

    /**
     * @param array $data
     * @return bool
     */
    public function fixMeetings(array $data): bool
    {
        return $this->json(ClassStudioDate::fixMeetings($data));
    }

    /**
     * Create or update all the properties that related to 'Class'
     * @param $data array
     * @return bool
     */
    public function saveClass(array $data): bool
    {
        return $this->json(ClassStudioDate::saveClass($data));
    }

    /**
     * Get class data for class edit
     * @param $id
     * @return bool
     */
    public function getClassData($id): bool
    {
        $CompanyNum = Auth::user()->CompanyNum;
        $data = ClassStudioDate::getClassById($id, $CompanyNum);
        if ($data) {
            if ($data->ClassType == 2)
                $data->LastClassDate = ClassStudioDate::getLastClass($data->GroupNumber, $CompanyNum)->StartDate;
            if ($data->is_zoom_class == 1)
                $data->zoomData = (ClassZoom::getByClassId($data->id, $CompanyNum))->toArray();
            if ($data->onlineClassId) {
                /** @var ClassOnline $onlineClass */
                $onlineClass = ClassOnline::find($data->onlineClassId);
                $data->onlineSendType = $onlineClass->sendType;
                $data->onlineSendTime = $onlineClass->sendTime;
                $data->onlineSendTimeType = $onlineClass->sendTimeType;
            }

            try {
                $tagId = TagsStudio::getTagByLessonId($id)->tags_id ?? 0;
                $translation = Tags::find($tagId)->translation_id ?? 0;
                $key = TranslationKeys::find($translation)->key ?? '';
                $data->tag = lang($key);
                $data->tagId = $tagId;
                $data->tags = TagsService::getFavoriteAndOtherCategoriesTags($CompanyNum);
            } catch (Exception $e) {
//                $data->tag = lang('tag_treatment_lessoncategory_personal');
//                $data->tagId = 99;
            }

            return $this->json(["data" => $data->toArray()]);
        }
        return $this->json(["data" => null]);
    }

    /**
     * @param $clientId
     * @param $classTypeId
     * @return bool
     */
    public function getMatchingMembership($clientId, $classTypeId): bool
    {
        /** @var Client $client */
        $client = Client::find($clientId);
        if ($client) {
            return $this->json([
                'status' => 1,
                'data' => $client->getMatchingActivity($classTypeId)
            ]);
        } else {
            return $this->json([
                'status' => 0,
                'data' => null
            ]);
        }
    }

    /**
     * @param $userId
     * @param $date
     * @return bool
     */
    public function getUserAvailability($userId = null, $date): bool
    {
        $availableTimeArr = [];
        if (empty($userId)) {
            return $this->json(['status' => 1, 'data' => null]);
        }

        $GuideAvailabilities = MeetingStaffRuleAvailability::getCoachWeekAvailability($userId, $date, $date);
        foreach ($GuideAvailabilities as $GuideAvailability) {
            $availableTimeArr = array_merge($availableTimeArr,
                Utils::createTimeRange($GuideAvailability->StartTime,
                    $GuideAvailability->EndTime, '+5 minutes')
            );
        }

        $unavailableTimeArr = [];
        $DayClasses = ClassStudioDate::getClassesByDateAndGuide($userId, $date);
        foreach ($DayClasses as $DayClass) {
            $unavailableTimeArr = array_merge($unavailableTimeArr,
                Utils::createTimeRange($DayClass->StartTime,
                    $DayClass->EndTime, '+5 minutes')
            );
        }

        $availableTimeArr = array_diff($availableTimeArr, $unavailableTimeArr);
        return $this->json([
            'status' => 1,
            'data' => !empty($availableTimeArr) ? $availableTimeArr : 0
        ]);
    }

    public function getTagsCategories() {
        try {
            $CompanyNum = Auth::user()->CompanyNum;
            $tags = TagsService::getFavoriteAndOtherCategoriesTags($CompanyNum);
            return $this->json([
                'status' => 1,
                'data' => $tags
            ]);
        } catch (Exception $e) {
            return $this->json([
                'status' => 0,
                'data' => $e->getMessage()
            ]);
        }
    }

}