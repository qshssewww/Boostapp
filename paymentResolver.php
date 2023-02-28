<?php

/* This file redirects response from YaadSarig payment system to app.boostapp.co.il, login.boostapp.co.il or letts.co.il */

require_once __DIR__ . '/app/init.php';
require_once __DIR__ . '/office/Classes/YaadPaymentResolverModel.php';
require_once __DIR__ . '/office/services/LoggerService.php';

if (!empty($_REQUEST)) {
    $redirectUrl = PaymentResolver::getRedirectUrl($_REQUEST);
    redirect_to($redirectUrl);
    exit;
} else {
    LoggerService::error('empty response from yaad at '.date('Y-m-d H:i:s'), LoggerService::CATEGORY_PAYMENT_RESOLVER_YAAD);
    die('You have no access.');
}

class PaymentResolver
{
    /**
     * @param $response
     * @return string
     * @throws Throwable
     */
    public static function getRedirectUrl($response)
    {
        self::logYaadResponse($response);

        try {
            if (!isset($response['Order'], $response['CCode'], $response['paymentStatus'])) {
                throw new InvalidArgumentException('Wrong data format');
            }

            // deleting of `paymentStatus` field from request (this parameter is set in Yaad Settings panel for success and fail statuses)
            $paymentStatus = $response['paymentStatus'];
            unset($response['paymentStatus']);

            $domain = null;

            preg_match('/(\w+)-([-\d]+)/', $response['Order'], $m);
            if (!empty($m) && isset($m[1], $m[2])) {
                $domain = $m[1];
                $orderId = $m[2];

                // replacing Order ID - delete prefix
                $response['Order'] = $orderId;
            }

            // getting active routes
            $resolverRoutes = YaadPaymentResolverModel::getActiveRoutes();
            if (!isset($resolverRoutes[$domain])) {
                throw new LogicException('Wrong route ' . $domain);
            }

            // check if transaction status is success or if it's J2 credit operation (CCode = 600)
            if ($paymentStatus === 'success' || (int)$response['CCode'] === 600) {
                $url = $resolverRoutes[$domain]->success_url;
            } else {
                $url = $resolverRoutes[$domain]->fail_url;
            }

            // deleting last & symbol
            $url = rtrim($url, '&');

            if (strpos($url, '?') !== false) {
                // if URL includes ? symbol ===> adds & symbol to the end of url (maybe we have GET parameters in url)
                $url .= '&';
            } else {
                // if URL doesn't include ? symbol ===> adds ? symbol to the end of url (GET parameters in url not exist)
                $url .= '?';
            }

            // adds HTTP query params
            $url .= http_build_query($response);
            LoggerService::debug($url, LoggerService::CATEGORY_YAADSARIG);
            return $url;
        } catch (\Throwable $e) {
            LoggerService::error($e, LoggerService::CATEGORY_PAYMENT_RESOLVER_YAAD);
            throw $e;
        }
    }

    /**
     * @param $request
     * @return void
     */
    private static function logYaadResponse($request)
    {
        $Logdata = json_encode([
            'file' => __FILE__,
            'line' => __LINE__,
            '$_REQUEST' => $request,
            'isGet' => $_SERVER['REQUEST_METHOD'] === 'GET',
            'isPost' => $_SERVER['REQUEST_METHOD'] === 'POST',
        ]);

        DB::table('boostapp.fixlog')->insertGetId(['type' => 'info', 'category' => 'yaad_payment_resolver','Logdata' => $Logdata]);
    }
}
