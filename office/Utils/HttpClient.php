<?php

use GuzzleHttp\Exception\ParseException;

require_once __DIR__ . '/../services/LoggerService.php';
require_once __DIR__ . '/../Classes/AppNotification.php';

class HttpClient
{
    /**
     * @param string $type HTTP request type 'GET', 'POST' and so on
     * @param string $url Request url
     * @param array $data Data to send to url
     * @param bool $asString Return result as string
     * @param array $options Guzzle options
     * @return array|string
     * @throws Throwable
     */
    public static function sendRequest(string $type, string $url, array $data = [], bool $asString = false, array $options = [])
    {
        $asJSON = $options['json'] ?? false;
        unset($options['json']);

        $Authorization = $options['Authorization'] ?? null;
        unset($options['Authorization']);

        $options['defaults']['verify'] = false; //todo: remove this line after fixing
        $client = new GuzzleHttp\Client($options);

        if ($asJSON) {
            $requestOptions = ['json' => $data];
        } elseif (strtoupper($type) === 'POST') {
            $requestOptions = ['body' => $data];
        } else {
            $requestOptions = ['query' => $data];
        }
        $requestOptions['allow_redirects'] = true;

        $request = $client->createRequest($type, $url, $requestOptions);

        if(!$asString) {
            $request->addHeader('Accept', 'application/json');
        }
        if ($Authorization) {
            $request->addHeader('Authorization', $Authorization);
        }

        try {
            $response = $client->send($request);

            if ($response->getStatusCode() == 200) {
                // get json as array or string
                try {
                    // if $asString = true parameter is passed - converting to string
                    if ($asString) {
                        $result = (string)$response->getBody();
                    } else {
                        // try to read as json. if can't - go to catch section and return as string or array
                        $result = $response->json();
                        if(empty($result)) {
                            try {
                                file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/moshe_logs.txt',
                                    '[' . __FILE__ . '] [line: ' . __LINE__ . '] [' . date("Y-m-d H:i:s") . '] HttpClient response : ' . print_r($response->getHeaders(), true)
                                    . PHP_EOL . PHP_EOL . print_r((string)$response->getBody(), true) . PHP_EOL, FILE_APPEND | LOCK_EX);
                            } catch(Exception $e) {
                                // Do nothing
                            }
                        }
                    }
                } catch (ParseException $e) {
                    try {
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/moshe_logs.txt', '[' . __FILE__ . '] [line: ' . __LINE__ . '] [' . date("Y-m-d H:i:s") . '] Url : ' . $url . ' Params :' . print_r($data) . 'HttpClient parse error response : ' . print_r($response->getHeaders(), true) . PHP_EOL . PHP_EOL . print_r((string)$response->getBody(), true) . PHP_EOL, FILE_APPEND | LOCK_EX);
                    } catch(Exception $e) {
                        // Do nothing
                    }
                    $result = $response->getBody()->getContents();
                }

                try {
                    LoggerService::debug($response->getBody()->getContents());
                    LoggerService::debug((string)$response->getBody());
                    LoggerService::debug($response->json());
                    LoggerService::debug($response->getReasonPhrase());
                } catch (\Throwable $e) {
                    LoggerService::debug($e);
                }

                return $result;
            } else {
                try {
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/moshe_logs.txt', '[' . __FILE__ . '] [line: ' . __LINE__ . '] [' . date("Y-m-d H:i:s") . '] Url : ' . $url . ' Params :' . print_r($data) . 'HttpClient wrong status error response : ' . print_r($response->getHeaders(), true) . PHP_EOL . PHP_EOL . print_r((string)$response->getBody(), true) . PHP_EOL, FILE_APPEND | LOCK_EX);

                    $subject = 'wrong status code on response (login) from meshulam.';
                    $text = 'date: '.date('Y-m-d H:i:s').'<br>';
                    $notification = new AppNotification([
                        'CompanyNum' => 100,
                        'ClientId' => 251931,
                        'Type' => AppNotification::TYPE_EMAIL,
                        'Subject' => $subject ?? '',
                        'Text' => $text ?? '',
                        'Dates' => date('Y-m-d H:i:s'),
                        'UserId' => 0,
                        'Date' => date('Y-m-d'),
                        'Time' => date('H:i:s'),
                        'priority' => 1
                    ]);
                    $notification->save();

                    $notificationBackup = new AppNotification([
                        'CompanyNum' => 100,
                        'ClientId' => 429928673,
                        'Type' => AppNotification::TYPE_EMAIL,
                        'Subject' => $subject ?? '',
                        'Text' => $text ?? '',
                        'Dates' => date('Y-m-d H:i:s'),
                        'UserId' => 0,
                        'Date' => date('Y-m-d'),
                        'Time' => date('H:i:s'),
                        'priority' => 1
                    ]);
                    $notificationBackup->save();

                } catch(Exception $e) {
                    // Do nothing
                }

                try {
                    LoggerService::debug($response->getBody()->getContents());
                    LoggerService::debug((string)$response->getBody());
                    LoggerService::debug($response->json());
                    LoggerService::debug($response->getReasonPhrase());
                } catch (\Throwable $e) {
                    LoggerService::debug($e);
                }

                throw new LogicException('Wrong http status. Url: ' . $url . '. HTTP status: ' . $response->getStatusCode());
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }
    }
}
