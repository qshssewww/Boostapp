<?php
require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/ClassStudioAct.php';
require_once __DIR__ . '/../services/WhatsAppService.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $classActId
 * @property $ClientId
 * @property $clientPhone
 * @property $template_name
 * @property $template_language
 * @property $template_components
 * @property $Dates
 * @property $Date
 * @property $Time
 * @property $content_type
 * @property $status
 * @property $price_unlimited
 * @property $response
 * @property $workerStatus
 * @property $workerStart
 * @property $workStatusDone
 */
class WhatsAppNotifications extends \Hazzard\Database\Model
{
    protected $table = "boostapp.whatsapp_notifications";

    /**
     * @param ClassStudioAct $classAct
     * @param $Date
     * @param $Time
     * @return mixed
     */
    public static function sendClassReminder(ClassStudioAct $classAct, $Date, $Time)
    {
        /** @var Settings $company */
        $company = Settings::getSettings($classAct->CompanyNum);
        /** @var Client $client */
        $client = Client::find($classAct->ClientId);

        if (!isset($client) || (!$client->ContactMobile && !$client->parentClientId) || !isset($company) || !$company->ContactMobile) {
            return null;
        }

        $body_data = [
            $client->FirstName,
            $classAct->ClassName,
            date('d/m/Y', strtotime($classAct->ClassDate)),
            date('H:i', strtotime($classAct->ClassStartTime)),
            $company->AppName,
        ];

        $button_data = [];
        $button_data[] = [
            'subType' => "url",
            'value' => '972' . substr($company->ContactMobile, -9),
        ];

        return self::insertGetId([
            'CompanyNum' => $classAct->CompanyNum,
            'classActId' => $classAct->id,
            'ClientId' => $classAct->ClientId,
            'clientPhone' => '972' . substr($client->parentClientId ? Client::find($client->parentClientId)->ContactMobile : $client->ContactMobile, -9),
            'template_name' => 'class_update_btn',
            'template_language' => 'he',
            'template_components' => WhatsAppService::composeTemplateComponents([], $body_data, $button_data),
            'Dates' => $Date . " " . $Time,
            'Date' => $Date,
            'Time' => $Time,
            'content_type' => 11,
            'price_unlimited' => $company->WhatsAppPrice ?? '0.18',
        ]);
    }

    /**
     * @param ClassStudioAct $classAct
     * @param $Date
     * @param $Time
     * @return mixed
     */
    public static function sendWaitingListFree(ClassStudioAct $classAct, $Date, $Time)
    {
        /** @var Settings $company */
        $company = Settings::getSettings($classAct->CompanyNum);
        /** @var Client $client */
        $client = Client::find($classAct->ClientId);

        if (!isset($client) || (!$client->ContactMobile && !$client->parentClientId) || !isset($company) || !$company->ContactMobile) {
            return null;
        }

        $body_data = [
            $client->FirstName,
            $classAct->ClassName,
            date('d/m/Y', strtotime($classAct->ClassDate)),
            date('H:i', strtotime($classAct->ClassStartTime)),
            $company->AppName,
        ];

        return self::insertGetId([
            'CompanyNum' => $classAct->CompanyNum,
            'classActId' => $classAct->id,
            'ClientId' => $classAct->ClientId,
            'clientPhone' => '972' . substr($client->parentClientId ? Client::find($client->parentClientId)->ContactMobile : $client->ContactMobile, -9),
            'template_name' => 'waiting_list_btn',
            'template_language' => 'he',
            'template_components' => WhatsAppService::composeTemplateComponents([], $body_data, []),
            'Dates' => $Date . " " . $Time,
            'Date' => $Date,
            'Time' => $Time,
            'content_type' => 4,
            'price_unlimited' => $company->WhatsAppPrice ?? '0.18',
        ]);
    }

    /**
     * @param int $limit_messages
     * @return mixed
     */
    public static function getMessages4Send(int $limit_messages)
    {
        return self::whereNull('workerStatus')
            ->where('Status', '=', '0')
            ->where(function ($q) {
                $q->where('Date', '<', date('Y-m-d'))->where('Date', '>=', date('Y-m-d', strtotime("-2 days")))
                    ->Orwhere('Date', '=', date('Y-m-d'))->where('Time', '<=', date('H:i:s'));
            })
            ->limit($limit_messages)
            ->get();
    }

    /**
     * @param $CompanyNum
     * @param $StartDate
     * @param $EndDate
     * @return mixed
     */
    public static function getMessages4Report($CompanyNum, $StartDate, $EndDate)
    {
        return self::where('CompanyNum', '=', $CompanyNum)
            ->whereBetween('Date', [$StartDate, $EndDate])
            ->orderBy('Date', 'DESC')
            ->orderBy('Time', 'DESC')
            ->get();
    }

    /**
     * @param $CompanyNum
     * @param $ClientId
     * @return mixed
     */
    public static function getMessages4ClientLog($CompanyNum, $ClientId)
    {
        return self::where('CompanyNum', '=', $CompanyNum)
            ->where('ClientId', '=', $ClientId)
            ->orderBy('Date', 'DESC')
            ->orderBy('Time', 'DESC')
            ->limit(50)
            ->get();
    }

    /**
     * @param $CompanyNum
     * @param $StartDate
     * @param $EndDate
     * @return mixed
     */
    public static function getMessages4ReportPriceSum($CompanyNum, $StartDate, $EndDate)
    {
        return self::where('CompanyNum', '=', $CompanyNum)
            ->whereBetween('Date', [$StartDate, $EndDate])
            ->where('Status', '=', '1')
            ->sum('price_unlimited');
    }

    /**
     * @param $CompanyNum
     * @param $StartDate
     * @param $EndDate
     * @return mixed
     */
    public static function getMessages4PaySum($CompanyNum, $StartDate, $EndDate)
    {
        return self::where('CompanyNum', '=', $CompanyNum)
            ->whereBetween('Date', [$StartDate, $EndDate])
            ->where('Status', '=', '1')
            ->sum('price_unlimited');
    }

    /**
     * @param array $idArray
     * @return void
     */
    public static function updateWorkerStatusStart(array $idArray)
    {
        self::whereIn('id', $idArray)
            ->update([
                'workerStart' => time(),
                'workerStatus' => 1,
            ]);
    }

    /**
     * @param array $idArray
     * @return void
     */
    public static function updateWorkerStatusEnd(array $idArray)
    {
        self::whereIn('id', $idArray)
            ->update([
                'workerStatus' => 2,
                'workStatusDone' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * @return void
     */
    public function setError($status = 2)
    {
        $this->status = $status;
        if ($status == 2) {
            $this->workStatusDone = date('Y-m-d H:i:s');
        }
        $this->save();
    }

    /**
     * @param $result
     * @return void
     */
    public function setResponse($result)
    {
        if ($result) {
            $this->response = json_encode($result);
        }
        $this->status = (!$result || isset($result->error)) ? 2 : 1;
        $this->save();
    }

    /**
     * @param array $templateList
     * @return string
     */
    public function reconstructMessage(array $templateList): string
    {
        if (empty($templateList)) return '';

        $data = json_decode($this->template_components) ?? [];

        foreach ($templateList as $template) {
            if ($template['name'] == $this->template_name && $template['language'] == $this->template_language) {
                $body = '';
                foreach ($template['components'] as $component) {
                    if ($component['type'] == 'BODY') {
                        $body = $component['text'];

                        break;
                    }
                }

                // replace placeholders
                foreach ($data as $part) {
                    if ($part->type == 'body') {
                        for ($i = 0; $i < sizeof($part->parameters); $i++) {
                            $body = str_replace('{{' . ($i + 1) . '}}', $part->parameters[$i]->text, $body);
                        }
                    }
                }

                return str_replace("\n", '<br>', $body);
            }
        }

        return '';
    }

}
