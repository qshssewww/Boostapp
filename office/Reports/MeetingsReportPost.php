<?php
require_once __DIR__ . '/../../app/init.php';
require_once __DIR__ . '/../services/meetings/MeetingService.php';

$dateFrom = $_POST['start'] ?? date("Y-m-d", strtotime("-7 days"));
$dateTo = $_POST['end'] ?? date("Y-m-d");
$companyNum = $_POST['companyNum'] ?? '';
$filter = $_POST['filter'] ?? 0;

$dateTo = $filter == 1 ? date('Y-m-d 23:59:59', strtotime($dateTo)) : $dateTo;

echo json_encode(['data' => MeetingService::getMeetings4Report($companyNum, $dateFrom, $dateTo, $filter)], JSON_UNESCAPED_UNICODE);
