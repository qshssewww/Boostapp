<?php
include_once __DIR__ . "/../Classes/ClassStudioAct.php";
include_once __DIR__ . "/../Classes/ClientGoogleAPI.php";
include_once __DIR__ . "/../Classes/GoogleCalendarSyncIssues.php";

class GoogleCalendarService
{

    /**
     * @param $ClientId
     * @param $client
     * @param $calendarName
     * @param $calendarId
     * @return void
     */
    public static function initCalendarSync($ClientId, $client, $calendarName, $calendarId = null)
    {
        $optParams = ['quotaUser' => $ClientId];
        // getting service
        $service = new Google_Service_Calendar($client);

        $client_db = ClientGoogleAPI::getByClientId($ClientId);
        if (!$client_db) {
            ClientGoogleAPI::setStatusReconnectByClientId($ClientId);
            return;
        }
        // set tokens from DB
        self::setAccessTokenToClientFromDB($ClientId, $client, $client_db->token);

        // used for resync - during logout it should be deleted
        if ($calendarId) {
            try {
                $service->calendars->delete($calendarId, $optParams);
                GoogleCalendarSyncIssues::deleteByClientId($ClientId);
                StudioBoostappLogin::updateGoogleCalendarId($ClientId, null);
            } catch (Exception $ex) {
                // exception during old calendar deletion
                // no actions needed
            }
        }

        // create new calendar
        $calendar = new Google_Service_Calendar_Calendar();
        $calendar->setSummary($calendarName);
        $calendar->setTimeZone('Asia/Jerusalem');

        try {
            $createdCalendar = $service->calendars->insert($calendar, $optParams);
            $calendarId = $createdCalendar->getId();

            // update in DB
            StudioBoostappLogin::updateGoogleCalendarId($ClientId, $calendarId);
        } catch (Exception $ex) {
            ClientGoogleAPI::setStatusReconnectByClientId($ClientId);
            return;
        }

        try {
            $calendarListEntry = $service->calendarList->get($calendarId, $optParams);

            // set parameters
            $calendarListEntry->setBackgroundColor('#00c736');
            $calendarListEntry->setForegroundColor('#ffffff');
            $service->calendarList->update($calendarId, $calendarListEntry, [
                'quotaUser' => $ClientId,
                'colorRgbFormat' => true
            ]);
        } catch (Exception $ex) {
            GoogleCalendarSyncIssues::insertCalendarColorFix($ClientId);
        }

        // get all classes to sync
        $classes = (new ClassStudioAct)->getClasses4Sync($ClientId, [1, 2, 6, 10, 11, 12, 15, 16, 21, 22, 23]);

        // sync each class
        foreach ($classes as $class) {
            try {
                // add class to calendar
                self::insertEventByClassInfo($calendarId, $class, $service);
            } catch (Exception $ex) {
                $message = json_decode($ex->getMessage());
                // check if limit exceeded
                if (isset($message->error->code) && ($message->error->code == 403 || $message->error->code == 429)) {
                    ClientGoogleAPI::setStatusLimitReachedByClientId($ClientId);
                }

                GoogleCalendarSyncIssues::insertSync($ClientId, $class->start_date);
                break;
            }
        }
    }

    /**
     * @param $classInfo
     * @return array|void
     */
    public static function getParamArrayFromClassInfo($classInfo)
    {
        $startTS = strtotime($classInfo->start_date);

        // skip past events
        if ($startTS < time()) return;

        $endTS = strtotime($classInfo->end_date);

        // required min length 5 - add leading zeroes
        $eventId = str_pad($classInfo->id, 5, '0', STR_PAD_LEFT);

        $description = '';
        if ($classInfo->BrandName) {
            $description = "מיקום : " . $classInfo->BrandName . "\n";
        }
        $description .= "מאמן : " . $classInfo->GuideName . "\n";
        if ($classInfo->Remarks && $classInfo->RemarksStatus == '0') {
            $description .= "תוכן שיעור : " . "\n" . $classInfo->Remarks . "\n";
        }

        $description .= "\n" . 'שינויים וביטול השיעור ניתן לבצע דרך אפליקציית המתאמנים';

        $eventArray = [
            'id' => $eventId,
            'summary' => $classInfo->ClassName . " - " . $classInfo->GuideName,
            'description' => $description,
            'start' => array(
                'dateTime' => date(DATE_RFC3339, $startTS),
                'timeZone' => 'Asia/Jerusalem',
            ),
            'end' => array(
                'dateTime' => date(DATE_RFC3339, $endTS),
                'timeZone' => 'Asia/Jerusalem',
            ),
        ];
        if ($classInfo->BrandName) {
            $eventArray['location'] = $classInfo->BrandName;
        }

        return $eventArray;
    }

    /**
     * @param $calendarId
     * @param $classInfo
     * @param $service
     * @return boolean false - if resync
     * @throws Exception
     */
    public static function insertEventByClassInfo($calendarId, $classInfo, $service, $cron = false)
    {
        $optParams = ['quotaUser' => $classInfo->ClientId];

        $eventArray = self::getParamArrayFromClassInfo($classInfo);
        if (!$eventArray) return;

        $eventId = $eventArray['id'];

        try {
            $event = $service->events->get($calendarId, $eventId, $optParams);

            // check if event already was cancelled - event_id blocked - resync needed
            if ($event->getStatus() == "cancelled") {
                if ($cron) return false;
                throw new Exception("Can't update cancelled event");
            }

            // set visible (default)
            $event->setStatus("confirmed");

            // set all parameters
            $event->setSummary($eventArray['summary']);
            $event->setDescription($eventArray['description']);

            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($eventArray['start']['dateTime']);
            $start->setTimeZone($eventArray['start']['timeZone']);
            $event->setStart($start);

            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($eventArray['end']['dateTime']);
            $end->setTimeZone($eventArray['end']['timeZone']);
            $event->setEnd($end);

            if (isset($eventArray['location'])) {
                $event->setLocation($eventArray['location']);
            }

            $service->events->update($calendarId, $event->getId(), $event, $optParams);
            return true;
        } catch (Exception $ex) {
            $message = json_decode($ex->getMessage());
            // check if limit exceeded
            if (isset($message->error->code) && $message->error->code == 404) {
                // create new if not exists
                $event = new Google_Service_Calendar_Event($eventArray);
                $service->events->insert($calendarId, $event, $optParams);
                return true;
            } elseif (isset($message->error->code)
                && $message->error->code == 403
                && $message->error->message == "The user must be signed up for Google Calendar.") {
                // business account - most likely suspended
                return false;
            }

            throw $ex;
        }
    }

    /**
     * @param $ClientId
     * @param $calendarId
     * @param $classActId
     * @param bool $cron
     * @return void
     * @throws Exception
     */
    public static function addToCalendarByClassActId($ClientId, $calendarId, $classActId, $cron = false)
    {
        if (!$calendarId) return;

        // get service
        $service = self::getServiceByClientId($ClientId);

        if ($service) {
            // get class info
            $classInfo = (new ClassStudioAct())->getSingleClass4Sync($classActId);

            try {
                // add/update event
                self::insertEventByClassInfo($calendarId, $classInfo, $service, $cron);
            } catch (Exception $ex) {
                if ($cron) throw $ex;

                GoogleCalendarSyncIssues::insertSingle($ClientId, $classInfo->start_date, $classActId);
            }
        } else {
            $ClassDate = (new ClassStudioAct($classActId))->__get('ClassDate');
            GoogleCalendarSyncIssues::insertSingle($ClientId, $ClassDate, $classActId);
        }
    }

    /**
     * @param $ClientId
     * @param $calendarId
     * @param $eventId
     * @param bool $cron
     * @return void
     * @throws Exception
     */
    public static function removeFromCalendar($ClientId, $calendarId, $eventId, $cron = false)
    {
        if (!$calendarId) return;

        // get service
        $service = self::getServiceByClientId($ClientId);

        $optParams = ['quotaUser' => $ClientId];

        if ($service) {
            // remove event
            try {
                $event = $service->events->get($calendarId, $eventId, $optParams);

                if ($event) {
                    $event->setStatus("cancelled");
                    $service->events->update($calendarId, $event->getId(), $event, $optParams);
                }
                return;
            } catch (Exception $ex) {
                // action needed only for cron
                if ($cron) throw $ex;
            }
        }

        $classActId = ltrim($eventId, '0');
        $ClassDate = (new ClassStudioAct($classActId))->__get('ClassDate');

        GoogleCalendarSyncIssues::insertSingle($ClientId, $ClassDate, $classActId);
    }

    /**
     * @param $ClientId
     * @return Google_Service_Calendar|null
     */
    public static function getServiceByClientId($ClientId)
    {
        $client_db = ClientGoogleAPI::getByClientId($ClientId);

        // check if data saved
        if ($client_db && $client_db->status != 0) {
            try {
                // read credentials
                $client = new Google\Client();
                $client->setAuthConfig(__DIR__ . '/../../app/config/client_credentials.json');

                // set tokens from DB
                if (!GoogleCalendarService::setAccessTokenToClientFromDB($ClientId, $client, $client_db->token)) {
                    return null;
                }

                return new Google_Service_Calendar($client);
            } catch (Exception $ex) {
                return null;
            }
        }

        return null;
    }

    /**
     * @param $ClientId
     * @param $client
     * @param $token_db
     * @return bool
     */
    public static function setAccessTokenToClientFromDB($ClientId, $client, $token_db)
    {
        // set tokens from DB
        $client->setAccessToken(json_decode($token_db, true));

        // refresh if token not valid
        if ($client->isAccessTokenExpired()) {
            try {
                $token = $client->fetchAccessTokenWithRefreshToken();
            } catch (Exception $ex) {
                $token['error'] = true;
            }

            $scopes = [];
            if (isset($token['scope'])) {
                $scopes = explode(" ", $token['scope']);
            }
            // check scope
            if (isset($token['error']) || !in_array(Google\Service\Calendar::CALENDAR, $scopes)) {
                // we can't access events - reconnect needed
                ClientGoogleAPI::setStatusReconnectByClientId($ClientId);
                GoogleCalendarSyncIssues::deleteByClientId($ClientId);
                return false;
            }

            // update in DB
            ClientGoogleAPI::updateTokenByClientId($ClientId, $token);
        }
        return true;
    }

    /**
     * @param $actId
     * @param $arr
     * @param bool $isNew
     * @return void
     */
    public static function checkChangedAndSync($actId, $arr, $isNew = false)
    {
        // is new, status or details changed - sync needed
        if ($isNew
            || isset($arr['Status'])
            || isset($arr['ClassName'])
            || isset($arr['ClassDate'])
            || isset($arr['ClassStartTime'])
            || isset($arr['ClassEndTime'])) {
            self::updateCreateIfVisible($actId);
        }
    }

    /**
     * @param $id
     * @param $arr
     * @return void
     */
    public static function checkClassDateChangedAndSync($id, $arr)
    {
        // check ClassStudioDate fields change
        if (isset($arr['GuideName'])
            || isset($arr['Remarks'])
            || isset($arr['RemarksStatus'])
            || isset($arr['Floor'])
            || isset($arr['ClassName'])
            || isset($arr['start_date'])
            || isset($arr['end_date'])) {

            $acts = (new ClassStudioAct())->getAllActsByClassId($id);

            foreach ($acts as $act) {
                self::updateCreateIfVisible($act->id);
            }
        }
    }

    /**
     * @param $actId
     * @return void
     */
    public static function updateCreateIfVisible($actId)
    {
        /** @var ClassStudioAct $ClassAct */
        $ClassAct = ClassStudioAct::find($actId);

        $CalendarId = StudioBoostappLogin::findByClientId($ClassAct->FixClientId)->GoogleCalendarId ?? null;
        if (!in_array($ClassAct->Status, [1, 2, 6, 10, 11, 12, 15, 16, 21, 22, 23])) {
            // required min length 5 - add leading zeroes
            $eventId = str_pad($actId, 5, '0', STR_PAD_LEFT);

            // delete from calendar if synced
            GoogleCalendarService::removeFromCalendar($ClassAct->FixClientId, $CalendarId, $eventId);
        } else {
            // add to calendar if synced
            GoogleCalendarService::addToCalendarByClassActId($ClassAct->FixClientId, $CalendarId, $actId);
        }
    }

    /**
     * @param $ClientId
     * @return void
     */
    public static function disconnectClient($ClientId)
    {
        $client_db = ClientGoogleAPI::getByClientId($ClientId);

        // disconnect client from Google Calendar API
        if ($client_db) {
            $client = new Google\Client();
            // set tokens from DB
            if (GoogleCalendarService::setAccessTokenToClientFromDB($ClientId, $client, $client_db->token)) {
                // token is active, we can delete calendar if it exists
                $CalendarId = StudioBoostappLogin::findByClientId($ClientId)->GoogleCalendarId ?? null;
                if ($CalendarId) {
                    // delete calendar and record from DB
                    try {
                        $service = new Google_Service_Calendar($client);
                        $service->calendars->delete($CalendarId);
                        StudioBoostappLogin::updateGoogleCalendarId($ClientId, null);
                    } catch (Exception $ex) {
                        // don't need to do nothing, just prevent other catch actions
                    }
                }

                // revoke token
                try {
                    $client->revokeToken();
                } catch (Exception $ex) {
                    // don't need to do nothing, we just try to revoke
                }
            }

            // remove record from DB
            ClientGoogleAPI::deleteByClientId($ClientId);
        }

        // remove from cron table
        GoogleCalendarSyncIssues::deleteByClientId($ClientId);
    }

}
