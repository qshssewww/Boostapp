<?php
use Hazzard\Database\Model;

class Role extends Model {
	
	protected $table = 'roles';

	protected $guarded = array('id');

	public static function getPermissions($id)
	{
		$permissions = self::where('id', $id)->pluck('permissions');

		return explode(',', $permissions);
	}

    public function isOwner(){
        if ($this->getAttribute('permissions') == '*' && $this->getAttribute('name') == 'בעלים')
            return true;
        return false;
    }
}