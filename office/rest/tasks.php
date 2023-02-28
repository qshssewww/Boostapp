<?php
$q = DB::table('calendar')
    ->where('calendar.StartDate', '<=', date('Y-m-d'))
    ->where('calendar.CompanyNum', '=', $rest->CompanyNum)
    ->where('calendar.Status', '=', '0')
    ->orderBy('calendar.start_date', 'ASC')
    ->limit(10)
    ->leftJoin('client', 'client.id', '=', 'calendar.ClientId')
    ->select(DB::raw('
    calendar.id as calendarId,
    calendar.Title as title,
    CONCAT(calendar.StartDate," ",calendar.StartTime) as deadLine,
    IF(calendar.start_date<=NOW(), "danger", "success") as calStatus,
    CASE calendar.Level
        when "0" then "נמוכה"
        when "1" then "בינונית"
        when "2" then "גבוהה"
    END as calLevel,
    client.id as clientId,
    client.CompanyName as fullName
'));

// $rest->answer->sql = $q->toSql();
$rest->answer->items = $q->get();
