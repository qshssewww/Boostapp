<?php

require_once __DIR__ . '/../Classes/Models/Tags.php';
require_once __DIR__ . '/../Classes/Models/TagsCategories.php';
require_once __DIR__ . '/../Classes/Models/TranslationKeys.php';
require_once __DIR__ . '/../Classes/Models/TranslationValues.php';
require_once __DIR__ . '/../Classes/Models/TagsStudio.php';
require_once __DIR__ . '/../Classes/ClassesType.php';
require_once __DIR__ . '/../Classes/MeetingCategories.php';
require_once __DIR__ . '/../Classes/MeetingTemplates.php';


class TagsService
{
    /**
     * @param $lessonName
     * @param $lessonTypeId
     * @param $companyNum
     * @return array|string
     */
    public static function getLessonPredictionTagKey($lessonName, $lessonTypeName , $companyNum, $lessonTypeId = 0)
    {
        $tagId = TagsStudio::getTagByPreviousClassName($lessonName, $companyNum);
        $tagId = $tagId ?? Tags::getTagIdByAppointmentNameAndTranslations($lessonName);
        if ($lessonTypeId != 0) {
            $tagId = $tagId ?? TagsStudio::getTagByPreviousClassType($lessonTypeId, $companyNum);
        }
        $tagId = $tagId ?? Tags::getTagIdByAppointmentNameAndTranslations($lessonTypeName);
        $tagId = $tagId ?? TagsStudio::getPopularCompanyTag($companyNum);

        if (!isset($tagId)) {
            $typeIdArray = ClassesType::getByType($lessonTypeId); // 5) it's not all the list, it could be only He or En
            //correct search is search by tags
            $tagIdexample = TagsStudio::getTagByClassType($typeIdArray);
            $tagId = isset($tagIdexample) ? TagsStudio::getLessonCategoryPopularTag(
                Tags::find($tagIdexample)->getCategory()
            ) : null;
        }

        return isset($tagId) ? self::getTagArrayByTagId($tagId) : 'no key';

    }

    public static function getMeetingPredictionTagKey ($meetingName, $meetingCategoryName, $companyNum, $meetingCategoryId = 0)
    {
        $tagId = MeetingTemplates::getTagByPreviousTemplateName($meetingName, $companyNum);
        $tagId = $tagId ?? Tags::getTagIdByAppointmentNameAndTranslations($meetingName);
        if ($meetingCategoryId == 0) {
            $tagId = $tagId ?? MeetingTemplates::getTagByPreviousTemplateType($meetingCategoryId, $companyNum);
        }
        $tagId = $tagId ?? Tags::getTagIdByAppointmentNameAndTranslations($meetingCategoryName);
        $tagId = $tagId ?? Tags::getTagIdByAppointmentNameAndTranslations('אימון אישי');
        if (isset($tagId)) {
            return self::getTagArrayByTagId($tagId);
        }
        return 'no key';
    }

    /**
     * @param int $tagId
     * @return array
     */
    public static function getTagArrayByTagId(int $tagId): array
    {
        $tagObj = Tags::find($tagId);
        return ['id' => $tagId, 'key' => TranslationKeys::find($tagObj->translation_id)->key];
    }


    /**
     * @param $companyNum
     * @return array
     */
    public static function getFavoriteAndOtherCategoriesTags($companyNum)
    {
        $categoriesArray = TagsCategories::getCategoriesWithKey();
        $allTagsGroupedByCategories = Tags::getSortedTagsWithKey($categoriesArray);

        $favoriteCategoriesArray = TagsStudio::getCompanyFavoriteCategories(
            $companyNum,
            $categoriesArray
        ); //if empty add all to favorite, else according to array

        if (empty($favoriteCategoriesArray)) {
            $favoriteCategories = $allTagsGroupedByCategories;
            $otherCategories = [];
        } else {
            $favoriteCategories = array_intersect_key($allTagsGroupedByCategories, $favoriteCategoriesArray);
            $otherCategories = array_diff_key($allTagsGroupedByCategories, $favoriteCategoriesArray);
        }

        return ['favorite' => $favoriteCategories, 'other' => $otherCategories];
    }

    /**
     * @return array|mixed
     */
    public static function getAllTagsTranslationsArray()
    {
        $objectsArray = Tags::all();
        $keysArray = [];
        foreach ($objectsArray as $objectTag) {
            $keysArray[$objectTag->translation_id] = $objectTag->id;
        }
        return TranslationValues::getTranslationsByKeys($keysArray);
    }



}
