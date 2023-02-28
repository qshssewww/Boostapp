<?php

require_once __DIR__ . '/../../app/init.php';
require_once __DIR__ . '/../services/SmsService.php';
require_once __DIR__ . '/../services/EmailService.php';

//SmsService::send('+9725524810210', 'Hello world!');
//EmailService::send('gutnikalexandr@gmail.com', 'Hello world!', 'Test message');
EmailService::sendTemplate('alexg@boostapp.co.il', 'Hello world!', 'user/remind-password');
