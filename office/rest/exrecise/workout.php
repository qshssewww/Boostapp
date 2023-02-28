<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if(isset($data['client']['incdies'])){

    }
    
    try{
        if(gettype($data['instructor']) === "string") $data['instructor'] = json_decode($data['instructor']);
    } catch (Exception $e){
        unset($data['instructor']);
    }

    
    try{
        if(gettype($data['assistant']) === "string") $data['assistant'] = json_decode($data['assistant']);
    } catch (Exception $e){
        unset($data['assistant']);
    }



    $startDate = new DateTime($data['startDate']);
    if(!empty($data['endDate']) && $data['endDate'] != ''){
        try {
            $endDate = new DateTime($data['endDate']);
            $endDate = $endDate->format('Y-m-d');
        }catch (Exception $e) {
            $endDate = NULL;
        }
       
    }else{
        $endDate = NULL;
    }
 

    $arr = array(
        'CompanyNum'    => $rest->CompanyNum,
        'ClientId'      => $data['client']['clientId'],
        'StartDate'     => date('Y-m-d', strtotime($data['startDate'])),
        'EndDate'       => (!empty($data['endDate']) && $data['endDate'] != '')? date('Y-m-d', strtotime($data['endDate'])): NULL,
        'InstructorId'  => is_object($data['instructor'])?$data['instructor']->coachId:$data['instructor']['coachId'],
        'AssistantId'     => (!empty($data['assistant']))?is_object($data['assistant'])?$data['assistant']->coachId:$data['assistant']['coachId']:null,
        'Comment'       => !empty($data['comments'])?$data['comments']:null
    );

    if(empty($data['id'])):
        DB::table('workout')->insert($arr);
        $rest->answer->workoutId = DB::getPdo()->lastInsertId();
    else:
        DB::table('workout')->where('id', $data['id'])->update($arr);
        $rest->answer->workoutId = $data['id'];
        DB::table('workoutPlan')->where('WorkoutId', '=', $rest->answer->workoutId)->delete();
        DB::table('workoutPlanStep')->where('WorkoutId', '=', $rest->answer->workoutId)->delete();
    endif;

    $sql = array();

    foreach ($data['days'] as $day) {
        if(!isset($day['exrecises']) || !is_array($day['exrecises'])|| !count($day['exrecises'])) continue;

        $exrecisePos = 0;
        foreach($day['exrecises'] as $exrecise){

            DB::table('workoutPlan')->insert(array(
                'CompanyNum'    => $rest->CompanyNum,
                'WorkoutId'     => $rest->answer->workoutId,
                'ClientId'      => $data['client']['clientId'],
                'Title'         => !(empty($exrecise['name']))?$exrecise['name']:null,
                'Description'   => !(empty($exrecise['description']))?$exrecise['description']:null ,
                'Position'      => $exrecisePos,
                'Day'           => date('Y-m-d', strtotime($day['date']))
            ));
            $exrecisePos++;
            $workoutsPlanId = DB::getPdo()->lastInsertId();

            $exreciseStepPos = 0;
            foreach($exrecise['steps'] as $step){

                $arr = array(
                    "CompanyNum"        => $rest->CompanyNum,
                    "WorkoutId"         => $rest->answer->workoutId,
                    'ClientId'      => $data['client']['clientId'],
                    "workoutPlanId"     => $workoutsPlanId,
                    "Position"          => $exreciseStepPos,
                    "Status"            => 1,
                    "Name"    => !(empty($step['name']))?$step['name']:null,
                    "ExreciseRepeat"    => !(empty($step['repeat']))?$step['repeat']:null,
                    "ExreciseSets"      => !(empty($step['sets']))?$step['sets']:null
                );
    
                if(isset($step['time']) && isset($step['time']['amount'])) $arr['Time'] = $step['time']['amount'];
                if(isset($step['time']) && isset($step['time']['unit']) && isset($step['time']['unit']['name'])) $arr['TimeUnit'] = $step['time']['unit']['name'];
                
                if(isset($step['weight']) && isset($step['weight']['amount'])) $arr['Weight'] = $step['time']['amount'];
                if(isset($step['weight']) && isset($step['weight']['unit']) && isset($step['weight']['unit']['name'])) $arr['WeightUnit'] = $step['weight']['unit']['name'];
                
                if(isset($step['distance']) && isset($step['distance']['amount'])) $arr['Distance'] = $step['time']['amount'];
                if(isset($step['distance']) && isset($step['distance']['unit']) && isset($step['distance']['distance']['name'])) $arr['WeightUnit'] = $step['distance']['unit']['name'];
              
                if(isset($step['break']) && isset($step['break']['amount'])) $arr['Break'] = $step['time']['amount'];
                if(isset($step['break']) && isset($step['break']['unit']) && isset($step['break']['distance']['name'])) $arr['BreakUnit'] = $step['break']['unit']['name'];
                $sql[] = DB::table('workoutPlanStep')->insert($arr);
                $exreciseStepPos++;
            } // step
        } // exrecise
    } // day

    $rest->answer->exrecises = $sql;

    // let return workout, allow angular replace existing data so allow edit again
    $_GET['id'] = $rest->answer->workoutId;
}
    
        $q = DB::table('workout as w')
                ->where('w.CompanyNum', '=', $rest->CompanyNum)
                ->leftJoin('users as instructure', function($join){
                    $join->on('instructure.id', '=', 'w.InstructorId')
                         ->on('instructure.CompanyNum', '=', 'w.CompanyNum');
                })
                ->leftJoin('users as assistant', function($join){
                    $join->on('assistant.id', '=', 'w.AssistantId')
                         ->on('assistant.CompanyNum', '=', 'w.CompanyNum');
                })
                ->leftJoin('client as client', function($join){
                    $join->on('client.id', '=', 'w.ClientId')
                         ->on('client.CompanyNum', '=', 'w.CompanyNum');
                })
                ->leftJoin('workoutPlan as wp', function($join){
                    $join->on('w.CompanyNum', '=', 'wp.CompanyNum')
                        ->on('w.id', '=', 'wp.WorkoutId');

                    return $join;
                })
                ->leftJoin('workoutPlanStep as wps', function($join){
                    $join->on('w.CompanyNum', '=', 'wps.CompanyNum')
                        ->on('wp.id', '=', 'wps.workoutPlanId');
                })
                ->select(
                    'w.id as workoutId',
                    'w.StartDate as workoutStartDate',
                    'w.EndDate as workoutEndDate',
                    'w.InstructorId as workoutInstructorId',
                    'instructure.display_name as workoutInstructorName',
                    'w.AssistantId as workoutAssistantId',
                    'assistant.display_name as workoutAssistantName',
                    'w.Comment as workoutComment',
                    'w.Status as workoutStatus',

                    'client.id as clientId',
                    'client.CompanyName as clientFullName',
                    'client.ContactMobile as clientPhone',
                    'client.email as clientEmail',

                    'wp.id as workoutPlanId',
                    'wp.Title as workoutPlanTitle',
                    'wp.Description as workoutPlanDescription',
                    'wp.Day as workoutPlanDay',
                    'wp.Position as workoutPlanPosition',
                    'wp.Status as workoutPlanStatus',

                    'wps.id as workoutPlanStepId',
                    'wps.Position as workoutPlanStepPosition',
                    'wps.Status as workoutPlanStepStatus',
                    'wps.Name as workoutPlanStepName',
                    'wps.ExreciseRepeat as workoutPlanStepExreciseRepeat',
                    'wps.ExreciseSets as workoutPlanStepExreciseSets',
                    'wps.Time as workoutPlanStepTime',
                    'wps.TimeUnit as workoutPlanStepTimeUnit',
                    'wps.Weight as workoutPlanStepWeight',
                    'wps.WeightUnit as workoutPlanStepWeightUnit',
                    'wps.Distance as workoutPlanStepDistance',
                    'wps.DistanceUnit as workoutPlanStepDistanceUnit',
                    'wps.Break as workoutPlanStepBreak',
                    'wps.BreakUnit as workoutPlanBreakStepUnit'
                )
                ;


        if(isset($_GET['id'])){
            $q->where('w.id', '=', (int) $_GET['id']);
        }
        if(isset($_GET['day'])){
           $q->where('wp.Day', '=', DB::RAW("'".(string) $_GET['day']."'"));
        }
        if(isset($_GET['client'])){
           $q->where('client.id', '=', (int) $_GET['client']);
        }
        
        $rows = DB::select($q->toString());

        $workouts = new StdClass();

        // build a workouts array, start as object after build convert to arrays (angular way...)
        foreach($rows as $row){
            if(!isset($workouts->{$row->workoutId})){
                $workouts->{$row->workoutId} = new StdClass();
                $workouts->{$row->workoutId}->id = $row->workoutId;
                $workouts->{$row->workoutId}->startDate = $row->workoutStartDate;
                $workouts->{$row->workoutId}->endDate = $row->workoutEndDate;
                $workouts->{$row->workoutId}->instructor = new StdClass();
                $workouts->{$row->workoutId}->instructor->id = $row->workoutInstructorId;
                $workouts->{$row->workoutId}->instructor->name = $row->workoutInstructorName;      
                $workouts->{$row->workoutId}->assistant = new StdClass();
                $workouts->{$row->workoutId}->assistant->id = $row->workoutAssistantId;
                $workouts->{$row->workoutId}->assistant->name = $row->workoutAssistantName;
                $workouts->{$row->workoutId}->comment = $row->workoutComment;
                $workouts->{$row->workoutId}->status = $row->workoutStatus;
                $workouts->{$row->workoutId}->trainer = new StdClass();
                $workouts->{$row->workoutId}->trainer->id = $row->clientId;
                $workouts->{$row->workoutId}->trainer->fullName = $row->clientFullName;
                $workouts->{$row->workoutId}->trainer->email = $row->clientEmail;
                $workouts->{$row->workoutId}->trainer->phone = $row->clientPhone;

                
                $workouts->{$row->workoutId}->days = new StdClass();

            } 

            if(!isset($workouts->{$row->workoutId}->days->{$row->workoutPlanDay})){
                $workouts->{$row->workoutId}->days->{$row->workoutPlanDay} = new StdClass();
                $workouts->{$row->workoutId}->days->{$row->workoutPlanDay}->day = $row->workoutPlanDay;
                $workouts->{$row->workoutId}->days->{$row->workoutPlanDay}->exrecises = new StdClass();
            }


            if($row->workoutPlanPosition != null){
                if(!isset($workouts->{$row->workoutId}->days->{$row->workoutPlanDay}->exrecises->{$row->workoutPlanPosition})){

                    $workouts->{$row->workoutId}->days->{$row->workoutPlanDay}->exrecises->{$row->workoutPlanPosition} = new stdClass();
                    $exrecise = $workouts->{$row->workoutId}->days->{$row->workoutPlanDay}->exrecises->{$row->workoutPlanPosition};
                    $exrecise->id = $row->workoutPlanId;
                    $exrecise->name = $row->workoutPlanTitle;
                    $exrecise->description = $row->workoutPlanDescription;
                    // $exrecise->day = $row->workoutPlanDay;
                    // $exrecise->position = $row->workoutPlanPosition;
                    $exrecise->status = $row->workoutPlanStatus;
                    $exrecise->steps = new StdClass();
                }
            }

            if($row->workoutPlanStepPosition != null){
                if(!isset($workouts->{$row->workoutId}->days->{$row->workoutPlanDay}->exrecises->{$row->workoutPlanPosition}->steps->{$row->workoutPlanStepPosition})){
                    $step = $workouts->{$row->workoutId}->days->{$row->workoutPlanDay}->exrecises->{$row->workoutPlanPosition}->steps->{$row->workoutPlanStepPosition} = new StdClass();
                    $step->id = $row->workoutPlanStepId;
                    $step->position = $row->workoutPlanStepPosition;
                    $step->status = $row->workoutPlanStepStatus;
                    $step->name = $row->workoutPlanStepName;
                    $step->repeat = $row->workoutPlanStepExreciseRepeat;
                    $step->sets = $row->workoutPlanStepExreciseSets;
                    $step->time = $row->workoutPlanStepTime;
                    $step->timeUnit = $row->workoutPlanStepTimeUnit;
                    $step->weight = $row->workoutPlanStepWeight;
                    $step->weightUnit = $row->workoutPlanStepWeightUnit;
                    $step->distance = $row->workoutPlanStepDistance;
                    $step->distanceUnit = $row->workoutPlanStepDistanceUnit;
                    $step->break = $row->workoutPlanStepBreak;
                    $step->breakUnit = $row->workoutPlanBreakStepUnit;
                }
            }
        }

        $workouts = array_values((array) $workouts);
        foreach($workouts as &$workout){
            $workout->days = array_values((array) $workout->days);
            foreach($workout->days as &$day){
                $day->exrecises = array_values((array) $day->exrecises);
                foreach($day->exrecises as &$exrecise){
                    $exrecise->steps = array_values((array) $exrecise->steps);
                }
            }
        }


        $rest->answer->items = $workouts;
        // $rest->answer->raw = $raw;
        // $rest->answer->sql = $q->toString();
        
        exit;



/*
CREATE TABLE `workout` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `CompanyNum` int(10) unsigned NOT NULL,
    `ClientId` int(10) unsigned NOT NULL,
    `StartDate` DATE NOT NULL,
    `EndDate` DATE NULL,
    `InstructorId` int(10) unsigned NOT NULL,
    `AssistantId` int(10) unsigned NULL,
    `Comment` TEXT,
    `Status` tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `workoutPlan` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `CompanyNum` int(10) NOT NULL,
    `ClientId` int(10) unsigned NOT NULL,
    `WorkoutId` int(10) NOT NULL,
    `Title` varchar(200) NULL,
    `Description` TEXT NULL,
    `Day` DATE NOT NULL,
    `Position` int(3) NOT NULL DEFAULT '0',
    `Status` tinyint(1) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `workoutPlanStep` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `CompanyNum` int(10) NOT NULL,
 `ClientId` int(10) unsigned NOT NULL,
 `WorkoutId` int(10) NOT NULL,
 `workoutPlanId` int(10) NOT NULL,
 `Position` int(3) NOT NULL DEFAULT '0',
 `Status` tinyint(1) NOT NULL DEFAULT '1',
 `Name` varchar(255) NOT NULL,
 `ExreciseRepeat` int(20) DEFAULT NULL COMMENT 'חזרות לתרגיל',
 `ExreciseSets` int(20) DEFAULT NULL COMMENT 'סטים לתרגיל',
 `Time` decimal(16,2) DEFAULT NULL,
 `TimeUnit` varchar(20) DEFAULT NULL COMMENT 'sec, min, hour, day',
 `Weight` decimal(16,2) DEFAULT NULL,
 `WeightUnit` varchar(20) DEFAULT NULL COMMENT 'mg, g, kg',
 `Distance` double(16,2) DEFAULT NULL,
 `DistanceUnit` varchar(1) DEFAULT NULL COMMENT 'mm, m, km',
 `Break` double(16,2) DEFAULT NULL,
 `BreakUnit` varchar(20) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
*/