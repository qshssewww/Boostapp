<?php


class TemplateAvailability
{
    private $table1;
    private $table2;

    public function __construct()
    {
        $this->table1 = "boostapp.templateAvailability";
        $this->table2 = "boostapp.templateTimes";
    }
    public function getAllValidTemplateAvailability($CompanyNum){
        $res=DB::table($this->table1)
        ->leftJoin($this->table2,$this->table1.".id", '=', $this->table2.".templateAvailabilityId")
        ->select($this->table1.'.*', $this->table2.'.day',$this->table2.'.templateDate',$this->table2.'.timeRanges')
        ->where($this->table1.'.CompanyNum',"=",$CompanyNum)
        ->orderBy('id','DESC')
        ->get();
        $result = array();
        foreach ($res as $element) {
            if($element->endDate==null || $this->checkDateValid($element->endDate)){
                $result[$element->id]['id'] = $element->id;
                $result[$element->id]['CompanyNum'] = $element->CompanyNum;
                $result[$element->id]['name'] = $element->name;
                $result[$element->id]['type'] = $element->type;
                $result[$element->id]['startDate'] = $element->startDate;
                $result[$element->id]['endDate'] = $element->endDate;
                $result[$element->id]['timeRanges'][]=array(
                    "day"=>$element->day,
                    "templateDate"=>$element->templateDate,
                    "timeRanges"=>json_decode($element->timeRanges)
                );
            }
        }
        return array_values($result);
    }

    public function getSingleTemplateAvailabilityById($id){
        $res=DB::table($this->table1)
        ->leftJoin($this->table2,$this->table1.".id", '=', $this->table2.".templateAvailabilityId")
        ->select($this->table1.'.*', $this->table2.'.day',$this->table2.'.templateDate',$this->table2.'.timeRanges')
        ->where($this->table1.'.id',"=",$id)
        ->get();
        $result = array();
        foreach ($res as $element) {
                $result[$element->id]['id'] = $element->id;
                $result[$element->id]['CompanyNum'] = $element->CompanyNum;
                $result[$element->id]['name'] = $element->name;
                $result[$element->id]['type'] = $element->type;
                $result[$element->id]['startDate'] = $element->startDate;
                $result[$element->id]['endDate'] = $element->endDate;
                $result[$element->id]['timeRanges'][]=array(
                    "day"=>$element->day,
                    "templateDate"=>$element->templateDate,
                    "timeRanges"=>json_decode($element->timeRanges)
                );
        }
        $result =  array_values($result);
        return count($result)?$result[0]:null;
    }

    public function insertNewTemplateAvailability($data){
        $insertedRemplateAvailability=array(
            "CompanyNum"=>$data["CompanyNum"],
            "name"=>$data["name"],
            "type"=>$data["type"]
        );
        if(isset($data["startDate"]) && isset($data["endDate"])){
            $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $data["startDate"])));
            $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $data["endDate"])));
            $insertedRemplateAvailability['startDate']=$startDate;
            $insertedRemplateAvailability['endDate']=$endDate;
        }
        $templateAvailabilityId=DB::table($this->table1)->insertGetId($insertedRemplateAvailability);
        foreach($data["times"] as $time){
            DB::table($this->table2)->insert(array(
                "templateAvailabilityId"=>$templateAvailabilityId,
                "day"=>isset($time["day"])?$time["day"]:null,
                "templateDate"=>isset($time["date"])?$time["date"]:null,
                "timeRanges"=>json_encode($time["timeRanges"])
            ));

        }
        return $templateAvailabilityId;

    }

    private function checkDateValid($date){
        $format = "Y-m-d H:i:s";
        $date1  = \DateTime::createFromFormat($format, date($format));
        $date2  = \DateTime::createFromFormat($format, $date);
        return $date1<$date2;
    }
}