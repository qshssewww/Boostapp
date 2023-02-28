<?php
use Hazzard\Database\Model;

/**
 * @property $id
 * @property $user_id
 * @property $created_at
 * @property $updated_at
 */
class UserAccessGet extends Model
{
 protected $table = 'user_access_get';

    /**
     * @param $userId
     * @return UserAccessGet
     */
    public static function findByUserId($userId)
 {
    return self::where('user_id', $userId)->first();
 }

    /**
     * @param $userId
     * @return mixed
     */
    public static function deleteByUserId($userId){
        return self::where('user_id', $userId)->delete();
    }

}
