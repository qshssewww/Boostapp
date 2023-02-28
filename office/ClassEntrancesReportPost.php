<?php require_once '../app/initcron.php';

if (Auth::guest()) exit;
if (Auth::userCan('153')):
    header('Content-Type: text/html; charset=utf-8');

    $companyNum = Auth::user()->CompanyNum;

    $resArr = array("data" => array());
    $startLastWeek = strtotime('last sunday midnight');
    $startDatesArray = [date('Y-m-d',$startLastWeek )];
    $endDatesArray = [date('Y-m-d', strtotime("next saturday",$startLastWeek))];
    $sunday = 'sunday midnight last week';
    $saturday = 'saturday midnight last week';

    for ($i = 0; $i <7; $i++) {
        array_push($startDatesArray, date('Y-m-d', strtotime($startDatesArray[$i] . $sunday)));
        array_push($endDatesArray, date('Y-m-d', strtotime($endDatesArray[$i] . $saturday)));
    }

    $filterOn = (isset($_POST['SortItemText']) && $_POST['SortItemText'] !== lang('all'));

    //if get filter key
    if( $filterOn) {
        $openTables = DB::table('client')
            ->leftJoin('boostapp.classstudio_act', 'boostapp.classstudio_act.FixClientId', '=', 'client.id')
            ->leftJoin('boostapp.client_activities', 'classstudio_act.ClientActivitiesId', '=', 'boostapp.client_activities.id')
            ->select('client.id', 'client.CompanyName', 'client.ContactMobile' ,'classstudio_act.id as caId', 'classstudio_act.ClassDate', 'classstudio_act.Status')
            ->where('client.CompanyNum', '=', $companyNum)
            ->where('client.Status', '=', '0')
            ->whereBetween('classstudio_act.ClassDate', array($startDatesArray[7], $endDatesArray[0]))
            ->whereIn('classstudio_act.Status', array(1, 2, 6, 10, 11, 12, 15, 16, 17, 21, 23))
            ->where('client_activities.ItemId', $_POST['SortItemText'])
            ->orderBy('client.id', 'ASC')
            ->get();
    }
    else{
        $openTables = DB::table('client')
            ->select('client.id', 'client.CompanyName', 'client.ContactMobile' ,'classstudio_act.id as caId', 'classstudio_act.ClassDate', 'classstudio_act.Status')
            ->leftJoin('boostapp.classstudio_act', 'boostapp.classstudio_act.FixClientId', '=', 'client.id')
            ->where('client.CompanyNum', '=', $companyNum)
            ->where('client.Status', '=', '0')
            ->orderBy('client.id', 'ASC')
            ->get();
    }

    //Setting Variables
    $statusArray = array(1, 2, 6, 10, 11, 12, 15, 16, 17, 21, 23);
    $start = true;
    $clientIdInterval = -100; //not sexiest
    $lastTask =null;

    $filterOn = (isset($_POST['SortItemText']) && $_POST['SortItemText'] !== lang('all'));

    if(!empty($openTables)) {
        //add dummay raw
        $x = clone $openTables[0];
        $x->id = "-1";
        array_push($openTables,$x);
    }


    foreach ($openTables as $task) {
        if ($task->id !== $clientIdInterval) {
            if ($start) {
                $clientIdInterval = $task->id;
                //Preparing arrays for inserting values
                $totalArray = array_fill(0, 8, 0);
                $iconArray = array_fill(0, 8, '');
                $showArray = array_fill(0, 8, '0');
                $start = false;

            } else {
                //Completion of a particular customer's, create relevant data in arrays
                for ($i = 0; $i < count($totalArray) - 1; $i++) {
                    if ($totalArray[$i + 1] > $totalArray[$i]) {
                        $iconArray[$i] = '<i class="fas fa-caret-down text-danger"></i>';
                        $showArray[$i] = 1;
                    } else if ($totalArray[$i + 1] < $totalArray[$i]) {
                        $iconArray[$i] = '<i class="fas fa-caret-up text-primary"></i>';
                    }
                }

                $showProblem = '';
                if ($showArray[0] == '1' && $showArray[1] == '1') {
                    $showProblem = '999';
                }
                if ($totalArray[0] == '0' && $totalArray[1] == '0' && $companyNum != '569121') {
                    $showProblem = '999';
                }

                //Prepare object where all the data Stored
                $reportArray = array();
                $reportArray[0] = $lastTask->id;
                $reportArray[1] = '<input type="hidden" class="name"  name="names" value= ' . $showProblem . '> <a href="ClientProfile.php?u= ' . $lastTask->id . '"><span class="text-primary">' . $lastTask->CompanyName . '</span></a>';
                $reportArray[2] = $lastTask->ContactMobile;
                $reportArray[3] = '<input type="hidden" class="8"  name="8" value=' . $totalArray[7] . '>' . $totalArray[7];
                $temp = 6;
                for ($i = 4; $i <= 10; $i++) {
                    $reportArray[$i] = '<input type="hidden" class=' . $temp . ' name=' . $temp . ' value=' . $totalArray[$temp] . '> ' . $totalArray[$temp] . $iconArray[$temp] . '';
                    $temp--;
                }

                $reportArray[11] = $showProblem;
                array_push($resArr["data"], $reportArray);

                $clientIdInterval = $task->id;
                $totalArray = array_fill(0, 8, 0);
                $iconArray = array_fill(0, 8, '');
                $showArray = array_fill(0, 8, '0');
            }

        }
        //checking status valid
        if (in_array($task->Status, $statusArray)) {
            //Count what week the entrance was
            for ($i = 0; $i < count($startDatesArray); $i++) {
                if ($task->ClassDate >= $startDatesArray[$i] && $task->ClassDate <= $endDatesArray[$i]) {
                    $totalArray[$i] = $totalArray[$i] + 1;
                }
            }
        }
        //save last task
        $lastTask = $task;
    }
    echo json_encode($resArr, JSON_UNESCAPED_UNICODE);

endif;
?>





