<?php

use Hazzard\Database\Model;

require_once __DIR__ . "/../../services/LoggerService.php";

class TagsStudio extends Model
{
    protected $table = 'boostapp.tags_studio';

    /**
     * @param $lessonName
     * @param $companyNum
     * @return null
     */
    public static function getTagByPreviousClassName($lessonName, $companyNum)
    {
        $words = explode(' ', $lessonName);

        foreach ($words as $word){

            if (empty($word))
                continue;

            $tag = DB::table('boostapp.tags_studio as tagsS')
                ->leftjoin('classstudio_date as lesson', 'tagsS.studio_date_id', '=', 'lesson.id')
                ->select('tagsS.tags_id')
                ->where('tagsS.company_num', $companyNum)
                ->where('tagsS.isCron', 0)
                ->where('lesson.ClassName', 'like', '%'.$word.'%')
                ->whereNull('lesson.meetingTemplateId')
//                ->orderBy('tagsS.id', 'DESC')
                ->first();

            if(!empty($tag))
                return $tag->tags_id;
        }

        return null;
    }


    public static function getTagByPreviousClassType($lessonTypeId, $companyNum)
    {
        $tag = DB::table('boostapp.tags_studio as tagsS')
            ->leftjoin('classstudio_date as lesson', 'tagsS.studio_date_id', '=', 'lesson.id')
            ->select('tagsS.tags_id')
            ->where('tagsS.company_num', $companyNum)
            ->where('tagsS.isCron', 0)
            ->where('lesson.ClassNameType', $lessonTypeId)
            ->whereNull('lesson.meetingTemplateId')
//            ->orderBy('tagsS.id', 'DESC')
            ->first();

        return $tag->tags_id ?? null;
    }


    /**
     * @param $companyNum
     * @return mixed
     */
    public static function getPopularCompanyTag($companyNum)
    {
        $tag = DB::table('boostapp.tags_studio as tagsS')
            ->select('tagsS.tags_id', DB::raw('count(*) as total'))
            ->leftjoin('classstudio_date as lesson', 'tagsS.studio_date_id', '=', 'lesson.id')
            ->where('tagsS.company_num', $companyNum)
            ->where('tagsS.isCron', 0)
            ->whereNull('lesson.meetingTemplateId')
            ->groupBy('tagsS.tags_id')
            ->orderBy('total', 'DESC')
            ->first();

        return $tag->tags_id ?? null;
    }


    /**
     * @param $typeIdArray
     * @return mixed
     */
    public static function getTagByClassType($typeIdArray)
    {
        $oneTagIdForCategorySearch =
            DB::table('boostapp.tags_studio as tagsS')
                ->leftjoin('classstudio_date as lesson', 'tagsS.studio_date_id', '=', 'lesson.id')
                ->select('tagsS.tags_id')
                ->where('tagsS.isCron', 0)
                ->whereIn('lesson.ClassNameType', $typeIdArray)
                ->whereNull('lesson.meetingTemplateId')
                ->orderBy('tagsS.id', 'DESC')
                ->first();

        return $oneTagIdForCategorySearch->tags_id ?? null;
    }


    /**
     * @param $categoryId
     * @return mixed
     */
    public static function getLessonCategoryPopularTag($categoryId)
    {
        $tag = self::
        select('tags_id', DB::raw('count(*) as total'))
            ->where('isCron', 0)
            ->where('category_id', $categoryId)
            ->groupBy('tags_id')
            ->orderBy('total', 'DESC')
            ->first();
        return $tag->tags_id ?? null;
    }

    /**
     * @param $companyNum
     * @return array
     */
    public static function getCompanyFavoriteCategories($companyNum, $categoriesKeyArray)
    {
        $categories = DB::table('boostapp.tags_studio as tagsStudio')
            ->select(DB::raw('count(*) as total'), 'tags.category_id',)
            ->leftjoin('boostapp.tags as tags', 'tagsStudio.tags_id', '=', 'tags.id')
            ->where('tagsStudio.isCron', 0)
            ->where('tagsStudio.company_num', $companyNum)
            ->where('tags_studio.tags_id', '!=', 0)
            ->groupBy('tags.category_id')
            ->orderBy('total', 'DESC')
            ->limit(4)
            ->get();


        $categoriesArray = [];
        if (isset($categories)) {
            foreach ($categories as $category) {
                $categoriesArray[$categoriesKeyArray[$category->category_id]] = '';
            }
        }

        return $categoriesArray;
    }

    /**
     * @param $oldLessonId
     * @param $newLessonId
     * @param $companyNum
     */
    public static function cronCreating($oldLessonId, $newLessonId, $companyNum)
    {
        $tag = self::getTagByLessonId($oldLessonId);
        if($tag) {
            self::insert(
                ['studio_date_id' => $newLessonId, 'company_num' => $companyNum, 'isCron' => 1, 'tags_id' => $tag->tags_id]
            );
        }
    }

    /**
     * @param $lessonId
     * @return mixed
     */
    public static function getTagByLessonId($lessonId)
    {
        return self::where('studio_date_id', $lessonId)->first();
    }

    /**
     * @param $lessonIdArray
     * @param $tagId
     * @param $companyNum
     */
    public static function groupLessonUpdate($lessonIdArray, $tagId, $companyNum)
    {
        try {

            foreach ($lessonIdArray as $item) {

                $getLessonTagRow = self::where('studio_date_id', $item)
                    ->where('company_num', $companyNum)
                    ->first();

                if (!empty($getLessonTagRow)){
                    $getLessonTagRow->tags_id = $tagId;
                    $getLessonTagRow->save();
                } else if($item > 0) {

                    $tagsStudio = new self([
                        'studio_date_id' => $item,
                        'tags_id' => $tagId,
                        'company_num' => $companyNum,
                        'isCron' => 0
                    ]);

                    $tagsStudio->save();
                }
            }

        } catch (Exception $e) {
            LoggerService::info('tag group update error, tag id: ' . $tagId, LoggerService::TYPE_ERROR);
        }
    }


}

