<?php

use Hazzard\Database\Model;

require_once 'Utils.php';

/**
 * @property $id
 * @property $VerifiedMobile
 * @property $AreaCode
 * @property $Status
 * @property $updated_at
 * @property $created_at
 */
class MultiUsers extends Model
{
    /**
     * Mobile phone without prefix and leading zeros
     * @var string
     */
    private $VerifiedMobile;

    /**
     * Country code without '+'
     * @var string
     */
    private $AreaCode;

    /**
     * 0-Inactive 1-Active
     * @var int
     */
    private $Status;

    protected $table = 'multi_users';

    protected $guarded = array('id');

    /**
     * Verifying phone and country code and separating them
     * @param string $phone Can be provided with area code, or seperated
     * @param null $areaCode If already seperated from phone, overridden if provided on $phone
     * @return array ['phone' => Phone without area code and leading zeros, 'areaCode' => Country Code without '+']
     * @throws \Exception if area code is not provided on $phone or seperated
     */
    protected static function formatPhone(string $phone, $areaCode = null): array
    {
        $findPrefix = null;
        $regex = '/^\+(' . implode('|', Utils::$systemPrefix) . ')/';

        if (preg_match($regex, $phone, $findPrefix))
            $areaCode = $findPrefix[0];
        else if (empty($areaCode))
            throw new \Exception('Area code not provided or not registered on system');

        $phone = preg_replace($regex, '', $phone);

        return [
            'phone' => ltrim($phone, '0'), 
            'areaCode' => str_replace('+', '', $areaCode)
        ];
    }

    /**
     * Searching for MultiUser by phone
     * @param string $phone Can be provided with area code, or seperated
     * @param null $areaCode If already seperated from phone, overridden if provided on $phone
     * @return mixed Return object if exists
     * @throws \Exception if area code is not provided on $phone or seperated
     */
    public static function findByPhone(string $phone, $areaCode = null) {
        $phoneFormated = self::formatPhone($phone, $areaCode);

        return self::where('VerifiedMobile', $phoneFormated['phone'])
            ->where('AreaCode', $phoneFormated['areaCode'])
            ->first();
    }

    /**
     * Retrieving multi_user by phone number, if not exist creating new one
     * @param $phone string Mobile number **including country code**
     * @return MultiUsers Multi user object
     * @throws \Exception If country code isn't provided
     */
    public static function firstOrCreate(string $phone): MultiUsers
    {
        $phoneFormated = self::formatPhone($phone);
        $multiUser = self::findByPhone($phoneFormated['phone'], $phoneFormated['areaCode']);
        if (!$multiUser){
            $multiUser = new self;
            $multiUser->setAttribute('VerifiedMobile', $phoneFormated['phone']);
            $multiUser->setAttribute('AreaCode', $phoneFormated['areaCode']);
            $multiUser->save();
        }
        return $multiUser;
    }

    /**
     * Updating phone on multi-user
     * @param $id int|string Multi user id
     * @param $phone string Phone number including country code
     * @return MultiUsers Related `multi_users` result
     * @throws \Exception Country code is not provided
     */
    public static function updatePhone($id, string $phone): MultiUsers
    {
        $existingUser = self::findByPhone($phone);
        if ($existingUser) //Phone exists on `multi_users`
        {
            return $existingUser;
        }

        $multiUser = self::find($id);
        if (empty($multiUser)) //Multi-user isn't found on DB
        {
            $multiUser = self::firstOrCreate($phone);
        }

        $phoneFormated = self::formatPhone($phone);
        if ($multiUser->getAttribute('VerifiedMobile') != $phoneFormated['phone'] ||
            $multiUser->getAttribute('AreaCode') != $phoneFormated['areaCode']) {

            $multiUser->setAttribute('VerifiedMobile', $phoneFormated['phone']);
            $multiUser->setAttribute('AreaCode', $phoneFormated['areaCode']);
            $multiUser->save();
        }
        return $multiUser;
    }
}
