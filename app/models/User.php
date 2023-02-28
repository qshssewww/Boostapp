<?php

use Hazzard\Database\Model;

require_once __DIR__ . '/../../office/Classes/MultiUsers.php';

/**
 * @property $id
 * @property $username
 * @property $CompanyLogin
 * @property $email
 * @property $password
 * @property $display_name
 * @property $CompanyNum
 * @property $BrandsMain
 * @property $ItemId
 * @property $Brands
 * @property $joined
 * @property $status
 * @property $role_id
 * @property $last_session
 * @property $reminder
 * @property $remember
 * @property $FirstName
 * @property $LastName
 * @property $ContactMobile
 * @property $LastActivity
 * @property $ActiveStatus
 * @property $AgentNumber
 * @property $AgentEXT
 * @property $EmailSend
 * @property $MobileSend
 * @property $Coach
 * @property $CompanyId
 * @property $Salary
 * @property $Dob
 * @property $FixPrice
 * @property $About
 * @property $UploadImage
 * @property $Gender
 * @property $JumpBrands
 * @property $JumpBrandsId
 * @property $multiUserId
 */
class User extends Model
{

    protected $table = 'users';

    protected $guarded = array('id');

    protected $metaAttributes;

    protected $_permissions;

    protected $_role;

    /**
     * @return array
     */
    protected function getUsermetaAttribute()
    {
        if (!isset($this->metaAttributes)) {
            $this->metaAttributes = (array)Usermeta::get($this->id, '', true);
        }

        return $this->metaAttributes;
    }

    /**
     * @param $displayName
     * @return mixed
     */
    protected function getDisplayNameAttribute($displayName)
    {
        if (!empty($displayName)) {
            return $displayName;
        }

        if (!empty($this->username)) {
            return $this->username;
        }

        return $this->email;
    }

    /**
     * @param $value
     * @return int
     */
    protected function getIdAttribute($value)
    {
        return (int)$value;
    }

    /**
     * @return mixed|string
     */
    protected function getFirstNameAttribute()
    {
        return isset($this->usermeta['first_name']) ? $this->usermeta['first_name'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getLastNameAttribute()
    {
        return isset($this->usermeta['last_name']) ? $this->usermeta['last_name'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getAboutAttribute()
    {
        return isset($this->usermeta['about']) ? $this->usermeta['about'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getGenderAttribute()
    {
        return isset($this->usermeta['gender']) ? $this->usermeta['gender'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getBirthdayAttribute()
    {
        return isset($this->usermeta['birthday']) ? $this->usermeta['birthday'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getUrlAttribute()
    {
        return isset($this->usermeta['url']) ? $this->usermeta['url'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getPhoneAttribute()
    {
        return isset($this->usermeta['phone']) ? $this->usermeta['phone'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getLocationAttribute()
    {
        return isset($this->usermeta['location']) ? $this->usermeta['location'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getVerifiedAttribute()
    {
        return isset($this->usermeta['verified']) ? $this->usermeta['verified'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getLocaleAttribute()
    {
        return isset($this->usermeta['locale']) ? $this->usermeta['locale'] : '';
    }

    /**
     * @return array|string|string[]
     */
    protected function getAvatarAttribute()
    {
        return $this->generateAvatar($this->usermeta, $this->email);
    }

    /**
     * @return mixed|string
     */
    protected function getLastLoginAttribute()
    {
        return isset($this->usermeta['last_login']) ? $this->usermeta['last_login'] : '';
    }

    /**
     * @return mixed|string
     */
    protected function getLastLoginIpAttribute()
    {
        return isset($this->usermeta['last_login_ip']) ? $this->usermeta['last_login_ip'] : '';
    }

    /**
     * @return \Hazzard\Database\Model
     */
    protected function getRoleAttribute()
    {
        if (!isset($this->_role)) {
            $this->_role = Role::find($this->role_id);
        }

        return $this->_role;
    }

    /**
     * @return false|string[]
     */
    protected function getPermissionsAttribute()
    {
        if (!isset($this->_permissions) && $this->role) {
            $this->_permissions = explode(',', $this->role->permissions);
        }

        return $this->_permissions;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function can($permission)
    {
        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    /**
     * @param $value
     * @return int
     */
    public function getStatusAttribute($value)
    {
        return (int)$value;
    }

    /**
     * @return mixed
     */
    public function isOwner()
    {
        $role = Role::find($this->role_id);
        return $role->isOwner();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * @return bool
     */
    public function isSuspended()
    {
        return $this->status == 2;
    }

    /**
     * @param $meta
     * @param $email
     * @param $type
     * @return array|string|string[]
     */
    public static function generateAvatar($meta, $email = '', $type = null)
    {
        if (is_null($type) && isset($meta['avatar_type'])) {
            $type = $meta['avatar_type'];
        }

        switch ($type) {
            case 'image':
                if (!empty($meta['avatar_image'])) {
                    return app()->url("uploads/{$meta['avatar_image']}?") . time();
                }

            case 'gravatar':
                return get_gravatar($email, 300, 'mm');

            case 'facebook':
                if (!empty($meta['facebook_id'])) {
                    return $meta['facebook_avatar'];
                }

            case 'google':
                if (!empty($meta['google_avatar'])) {
                    return str_replace('?sz=50', '?sz=300', $meta['google_avatar']);
                }

            case 'soundcloud':
                if (!empty($meta['soundcloud_avatar'])) {
                    return str_replace('-large', '-t300x300', $meta['soundcloud_avatar']);
                }

            default:
                if (!empty($meta["{$type}_avatar"])) {
                    return $meta["{$type}_avatar"];
                }
        }

        return asset_url('img/avatar.png');
    }

    /**
     * Create Multi User or updating if already exists
     * @param $phone string Phone number including country code
     * @return MultiUsers Related multi_users result
     * @throws Exception If country code isn't provided
     */
    public function setMultiUser(string $phone): MultiUsers
    {
        if (empty($this->multiUserId)) {
            $multiUser = MultiUsers::firstOrCreate($phone);
        } else {
            $multiUser = MultiUsers::updatePhone($this->multiUserId, $phone);
        }
        $this->multiUserId = $multiUser->getAttribute('id');
        $this->ContactMobile = '0' . $multiUser->getAttribute('VerifiedMobile');
        $this->save();

        return $multiUser;
    }
}
