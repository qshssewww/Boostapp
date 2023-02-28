<?php

/**
 * @property $id
 * @property $ClientId
 * @property $RankId
 *
 * Class Rank
 */
class Rank extends \Hazzard\Database\Model
{
    protected $table = "boostapp.client_rank";

    public function setData($id)
    {
        $data = DB::table($this->table)->where("id", "=", $id)->first();
        if ($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function getRankNamesArrayByClientId($clientId)
    {
        $ranks = DB::table('client_rank')->where("ClientId", "=", $clientId)
        ->leftJoin('clientlevel', 'client_rank.RankId', '=', 'clientlevel.id')
        ->get();
        if (!isset($ranks)) {
            return [];
        }
        $result = array();
        foreach ($ranks as $rank) {
           $result[] = $rank->Level;
        }
        return $result;
    }

    public function updateClientRank($clientId,$rankArray,$isNew=false)
    {
        if (!$isNew) {
            DB::table('client_rank')->where('Clientid', $clientId)->delete();
        }
        $array = array();
        foreach ($rankArray as $rank) {
            $array[] = array('ClientId' => $clientId, 'RankId' => $rank);
        }
        DB::table('client_rank')->insert($array);
    }

    public function deleteClientRank($clientId)
    {
        return DB::table('client_rank')->where('Clientid', $clientId)->delete();
    }

    /**
     * @param $clientId
     * @return Rank[]
     */
    public static function getRanks($clientId)
    {
        return self::where('ClientId', $clientId)
                ->orderBy('id', 'desc')
                ->get();
    }

    public function getCountRanksId($RankId){
        return self::where('RankId', $RankId)->count();
    }

}
