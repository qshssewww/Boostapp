<?php

// FOR LOCAL TESTS
//require_once '../app/init.php';

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/GoogleCalendarSyncIssues.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClientGoogleAPI.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/StudioBoostappLogin.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/GoogleCalendarService.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");

$endTS = time() + 10 * 60; // +10 min - recommended deadline

try {
    // get all issues grouped by user
    $errors = GoogleCalendarSyncIssues::getAllGrouped();
    $backoffIter = 0;

    while (!empty($errors)) {
        // for each client
        foreach ($errors as $ClientId => $client) {
            $client_db = ClientGoogleAPI::getByClientId($ClientId);
            $CalendarId = StudioBoostappLogin::findByClientId($ClientId)->GoogleCalendarId ?? null;
            $optParams = ['quotaUser' => $ClientId];

            // client not connected to Google Calendar API - delete all errors
            if (!$client_db || $client_db->status == 0 || !$CalendarId) {
                unset($errors[$ClientId]);
                GoogleCalendarSyncIssues::deleteByClientId($ClientId);
                continue;
            }

            $service = GoogleCalendarService::getServiceByClientId($ClientId);
            if (!$service) {
                // temporary error - next client
                continue;
            }

            // for each problem
            foreach ($client as $key => $error) {
                $success = true;
                $resync = false;
                $limit = false;
                $classes = [];

                // get classes array to sync
                switch ($error->type) {
                    case GoogleCalendarSyncIssues::TYPE_INIT_SYNC:
                        $classes = (new ClassStudioAct)->getClasses4FullSync($ClientId, $error->dateTS);

                        break;

                    case GoogleCalendarSyncIssues::TYPE_SINGLE_CLASS:
                        $details = json_decode($error->details);
                        $classes[] = (new ClassStudioAct)->getSingleClass4Sync($details->classActId);

                        break;

                    case GoogleCalendarSyncIssues::TYPE_CALENDAR_COLOR:
                        try {
                            $calendarListEntry = $service->calendarList->get($CalendarId, $optParams);

                            // set parameters
                            $calendarListEntry->setBackgroundColor('#00c736');
                            $calendarListEntry->setForegroundColor('#ffffff');
                            $service->calendarList->update($CalendarId, $calendarListEntry, [
                                'quotaUser' => $ClientId,
                                'colorRgbFormat' => true
                            ]);
                        } catch (Exception $ex) {
                            $success = false;
                        }

                        break;

                    case GoogleCalendarSyncIssues::TYPE_TEMPORARY_STATUS:
                        // just reset status to synced
                        ClientGoogleAPI::setStatusSyncedByClientId($ClientId);

                        break;
                }

                // sync each class
                foreach ($classes as $class) {
                    if (!isset($class)) continue;
                    try {
                        if (!in_array($class->Status, [1, 2, 6, 10, 11, 12, 15, 16, 21, 22, 23])) {
                            // required min length 5 - add leading zeroes
                            $eventId = str_pad($class->id, 5, '0', STR_PAD_LEFT);

                            try {
                                $event = $service->events->get($CalendarId, $eventId, $optParams);
                            } catch (Exception $ex) {
                                $message = json_decode($ex->getMessage());
                                // check if not found
                                if (isset($message->error->code) && $message->error->code == 404) {
                                    // event not synced, no actions needed
                                    $event = false;
                                } elseif (isset($message->error->code)
                                    && $message->error->code == 403
                                    && $message->error->message == "The user must be signed up for Google Calendar.") {
                                    // business account - most likely suspended
                                    $resync = true;
                                    break;
                                } else {
                                    throw $ex;
                                }
                            }

                            if ($event && $event->getStatus() != "cancelled") {
                                $event->setStatus("cancelled");
                                $service->events->update($CalendarId, $event->getId(), $event, $optParams);
                            }
                        } else {
                            // add/update event
                            if (!GoogleCalendarService::insertEventByClassInfo($CalendarId, $class, $service, true)) {
                                $resync = true;
                                break;
                            }
                        }
                    } catch (Exception $ex) {
                        $message = json_decode($ex->getMessage());
                        // check if limit exceeded
                        if (isset($message->error->code) && ($message->error->code == 403 || $message->error->code == 429)) {
                            $limit = true;
                        }

                        // only for multiple sync change line to cron table and variables
                        if ($error->type == GoogleCalendarSyncIssues::TYPE_INIT_SYNC) {
                            GoogleCalendarSyncIssues::updateDateById($error->id, $class->start_date);
                            $error->dateTS = strtotime($class->start_date);
                            $client[$key] = $error;
                        }

                        $success = false;
                        break;
                    }
                }

                if ($resync) {
                    unset($errors[$ClientId]);
                    GoogleCalendarSyncIssues::deleteByClientId($ClientId);
                    try {
                        $calendarName = $service->calendarList->get($CalendarId)->getSummary();

                        // read credentials
                        $clientGoogle = new Google\Client();

                        // FOR LOCAL TESTS
//                        $clientGoogle->setAuthConfig(__DIR__ . '/../app/config/client_credentials.json');

                        $clientGoogle->setAuthConfig($_SERVER['DOCUMENT_ROOT'] . '/app/config/client_credentials.json');

                        GoogleCalendarService::initCalendarSync($ClientId, $clientGoogle, $calendarName, $CalendarId);
                    } catch (Exception $ex) {
                        // any problem during resync = reconnect
                        ClientGoogleAPI::setStatusReconnectByClientId($ClientId);
                    }
                    break;
                }
                if ($success) {
                    // on successful finish - delete problem line
                    GoogleCalendarSyncIssues::deleteById($error->id);
                    unset($client[$key]);
                    // check if client can be deleted from array
                    if (empty($client)) {
                        unset($errors[$ClientId]);
                        break;
                    }
                }
                if ($limit || $endTS < time()) {
                    break;
                }
            }
            // check endTS to exit from outer for
            if ($endTS < time()) break;
        }
        // exponential backoff time in seconds for everyone
        $backoff = pow(2, $backoffIter) + rand(0, 1);

        if ($backoffIter++ >= 6) $backoff = 64;

        // before each wait check endTS
        if ($endTS < time() + $backoff) break;

        sleep($backoff);
    }

    $Cron->end();
} catch (Exception $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if (isset($GetClientActivity)) {
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientActivity), JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
