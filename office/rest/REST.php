<?php
class REST
{
    public $answer;

    public function __construct()
    {
        $this->answer = new stdClass();

        if($_SERVER['SERVER_NAME'] === 'apptest.boostapp.co.il'){
            require_once realpath(__DIR__.'../../../../apptest.boostapp.co.il/medicaldata').DIRECTORY_SEPARATOR.'mockData.php';
            global $CompanyNum, $ClientId;
            $this->CompanyNum = $CompanyNum;
            $this->UserId = $ClientId;

            // $this->answer->company = $CompanyNum;
        }else{
            if (!Auth::check()) {
                $this->answer->errr = true;
                $this->answer->message = 'אנא תתחבר למערכת';
                $this->answer->code = 500;
                exit;
            }
            $this->CompanyNum = Auth::user()->CompanyNum;
            $this->UserId = Auth::user()->id;


           $remoteHost=  array_key_exists( 'REMOTE_HOST', $_SERVER) ? $_SERVER['REMOTE_HOST'] : gethostbyaddr($_SERVER["REMOTE_ADDR"]);
            if($remoteHost == '31.168.116.239' && !empty($_SERVER['HTTP_DEV']) && $_SERVER['HTTP_DEV']=="shlomo"){
                if(!empty($_SERVER['HTTP_COMPANYNUM']) && (int) $_SERVER['HTTP_COMPANYNUM']) $this->CompanyNum = $_SERVER['HTTP_COMPANYNUM'];
            }

        }



    }

    public function __destruct()
    {
        $this->answer->code = empty($this->answer->code) ? 200 : $this->answer->code;
        http_response_code($this->answer->code);
        unset($this->answer->code);
        echo json_encode($this->answer);
    }

    public function getExrecise($id = '0', $tags = array()){
        global $rest;
        $q = DB::table('exrecise as e')->where('e.CompanyNum', '=', $rest->CompanyNum);
    
        if($id !== '0'){
            $q->where('e.id', '=', $id);
        }else{
            if(empty($_GET['list'])) $q->where('e.Status', '=', 1);
        }
    
    
        $q->leftJoin('exrecisetags as t', function($join){
            $join->on('t.CompanyNum', '=', 'e.CompanyNum')
                 ->on('e.id', '=', 't.ExreciserId');
        });
    
        // allow to search by tags
        if(count($tags) !== 0){
            // qoute safe strings
            $first = true;
            foreach($tags as $tag){
                if($first){
                    $q->where('t.Tag', 'LIKE', sprintf("'%%%s%%'", $tag) );
                    $first = false;
                    continue;
                }
                $q->orWhere('t.Tag', 'LIKE', sprintf("'%%%s%%'", $tag) );
            }
            // IN dosn't have like
            // $q->whereIn('t.Tag', array_map(function($str){ return sprintf("'%s'", $str); }, $tags));
        }
    
        $q->leftJoin('exrecisesteps as s', function($join){
            $join->on('s.CompanyNum', '=', 'e.CompanyNum')
                 ->on('e.id', '=', 's.ExreciserId');
        });
    
        $q->select(
            'e.id as exreciseId',
            'e.Title as exreciseTitle',
            'e.Description as exreciseDescription',
            'e.Status as exreciseStatus',
            't.Tag as tagName',
            't.Status as tagStatus',
            's.id as stepId',
            's.Position as stepPosition',
            's.Status as stepStatus',
            's.Name as stepName',
            's.ExreciseRepeat as stepExreciseRepeat',
            's.ExreciseSets as stepExreciseSets',
            's.Time as stepTime',
            's.TimeUnit as stepTimeUnit',
            's.Weight as stepWeight',
            's.WeightUnit as stepWeightUnit',
            's.Distance as stepDistance',
            's.DistanceUnit as stepDistanceUnit',
            's.Break as stepBreak',
            's.BreakUnit as stepBreakUnit'
        );
    
        $q->orderBy('s.Position');
        // return $q->toString();
        $items = DB::select($q->toString());
        
        $results = new StdClass();
    
    
        foreach($items as $item){
    
            if(!isset($results->{$item->exreciseId})){
                $results->{$item->exreciseId} = array(
                    "id"=>$item->exreciseId,
                    "name" => $item->exreciseTitle,
                    "description" => $item->exreciseDescription,
                    "status" => $item->exreciseStatus,
                    "tags" => array(),
                    "steps" => array()
                );    
            }
    
    
            
            if(array_search($item->tagName, array_column($results->{$item->exreciseId}['tags'], 'text')) === FALSE){
                $tag = new StdClass();
                $tag->text = $item->tagName;
                $tag->status = $item->tagStatus;
                $results->{$item->exreciseId}['tags'][] = $tag;
                unset($tag);
            }
               
            if(array_search($item->stepId, array_column( $results->{$item->exreciseId}['steps'], 'id')) === FALSE){
                $step = new StdClass();
                $step->id = $item->stepId;
                $step->position = (int) $item->stepPosition;
                $step->status = (int) $item->stepStatus;
                $step->name = $item->stepName;
                $step->repeat = (int) $item->stepExreciseRepeat;
                $step->sets = (int) $item->stepExreciseSets;
    
                $step->time = new StdClass();
                $step->time->amount = (int) $item->stepTime;
                $step->time->unit = $item->stepTimeUnit;
                
                $step->weight = new StdClass();
                $step->weight->amount = (int) $item->stepWeight;
                $step->weight->unit = $item->stepWeightUnit;
                
                $step->distance = new StdClass();
                $step->distance->amount = (int) $item->stepDistance;
                $step->distance->unit = $item->stepDistanceUnit;
                
                $step->break = new StdClass();
                $step->break->amount = (int) $item->stepBreak;
                $step->break->unit = $item->stepBreakUnit;
                $results->{$item->exreciseId}['steps'][] = $step;
                unset($step);
            }
        }
    
        return array_values((array) $results);
    
    }
}