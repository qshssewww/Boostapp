<?php

use Hazzard\Database\Model;
/**
 * @property $id
 * @property $sections_id
 * @property $tags_id
 * @property $company_num
 * @property $created_at
 * @property $updated_at

 *
 * Class TagsSection
 */




class TagsSection extends Model
{
    protected $table = 'boostapp.tags_section';

    public const DEFAULT_SPACE_TAG_ID = 40;

    public static function updateOrCreateTagsBySectionId ($id, array $data): void
    {
        $isForUpdate = self::where('sections_id', $id)->exists();
        if ($isForUpdate) {
            self::where('sections_id', $id)->update($data);
        } else {
            $data += ['sections_id' => $id];
            self::insert($data);
        }
    }

}