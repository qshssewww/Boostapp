<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $ClientId
 * @property $Token
 * @property $Tokef
 * @property $YaadCode
 * @property $Dates
 * @property $UserId
 * @property $Status
 * @property $sme
 * @property $L4digit
 * @property $Type
 * @property $TransactionId
 * @property $Private
 * @property $YaadNumber
 *
 * Class Token
 */
class Token extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.token';

    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;

    /**
     * @param $id
     * @return Token|null
     */
    public static function getById($id)
    {
        return self::where("id", $id)->first();
    }

    /**
     * @param $companyNum
     * @param $clientId
     * @param bool $onlyPrivate
     * @return Token[]|[]
     */
    public static function getTokens($companyNum, $clientId, bool $onlyPrivate = true)
    {
        $query = self::where("CompanyNum", "=", $companyNum)
            ->where("ClientId", "=", $clientId);
         if($onlyPrivate) {
             $query->where("Private", "=", 0);         }
         return $query->where('Status', '=', self::STATUS_ACTIVE)
             ->orderBy('Dates', 'DESC')
             ->get();
    }

    /**
     * @param $token
     * @return Token|null
     */
    public static function getByToken($token)
    {
        $data = self::where("Token", "=", $token)
            ->where("Private", "=", 0)
            ->first();
        return $data;
    }

    /**
     * Returns existed token or create new one and returns it
     *
     * @param $data
     * @param $type
     * @return false|Token
     */
    public static function getOrSetToken($data, $type)
    {
        try {
            $tokenModel = self::where('CompanyNum', '=', $data['CompanyNum'])
                ->where('ClientId', '=', $data['ClientId'])
                ->where('Token', '=', $data['Token'])
                ->where('Type', '=', $type)
                ->where("Private", "=", 0)
                ->first();

            if (isset($tokenModel)) {
                // already added -> update Dates field
                $tokenModel->Dates = date('Y-m-d H:i:s');
            } else {
                // new token -> create record

                $tokenModel = new self([
                    'CompanyNum' => $data['CompanyNum'],
                    'ClientId' => $data['ClientId'],
                    'Token' => $data['Token'],
                    'Tokef' => $data['Tokef'],
                    'YaadCode' => $data['YaadCode'] ?? '',
                    'Dates' => date('Y-m-d H:i:s'),
                    'UserId' => '0',
                    'sme' => $data['Cvv'] ?? '',
                    'L4digit' => $data['L4digit'],
                    'Private' => 0,
                    'YaadNumber' => $data['YaadNumber'] ?? '',
                    'Type' => $type
                ]);
            }
            $tokenModel->save();

            LoggerService::debug($tokenModel, LoggerService::CATEGORY_ADD_NEW_TOKEN);

            return $tokenModel;
        } catch (\Throwable $e) {
            // TODO: log
            return false;
        }
    }

    /**
     * @param $clientId
     * @return void
     */
    public function updateClient($clientId)
    {
        $this->ClientId = $clientId;
        $this->save();
    }

    /**
     * @param $id
     * @return string
     */
    public static function getL4digitById($id): string {
        return self::where('id',$id)->pluck('L4digit') ?? '';
    }

    /**
     * @param DocsPayment $DocsPayment
     * @param int $type
     * @return Token|null
     */
    public static function getTokenByDocsPayment(DocsPayment $DocsPayment, int $type = 0): ?Token
    {
         return self::where('L4digit', '=', $DocsPayment->L4digit)
            ->where('ClientId', '=', $DocsPayment->ClientId)
            ->where('Status', '=', '0')
            ->where('Type', '=', $type)
            ->orderBy('id', 'DESC')
            ->first();
    }


}
