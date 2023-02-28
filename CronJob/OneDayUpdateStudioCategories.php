<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/TagsCategoriesStudios.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Models/Tags.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Models/TagsStudio.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Models/TagsSection.php';

set_time_limit(0);
ini_set("memory_limit", "-1");

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

try {
    // get all tag categories
    $tagsStudio = TagsStudio::select(['category_id', 'tags_id', 'company_num'])
        ->join(Tags::getTable(), TagsStudio::getTable() . '.tags_id', '=', Tags::getTable() . '.id')
        ->distinct()
        ->get();

    $tagsArr = [];
    foreach ($tagsStudio as $tag) {
        $tagsArr[$tag->company_num][] = $tag->tags_id;
    }

    $tagsSection = TagsSection::select(['category_id', 'tags_id', 'company_num'])
        ->join(Tags::getTable(), TagsSection::getTable() . '.tags_id', '=', Tags::getTable() . '.id')
        ->distinct()
        ->get();

    foreach ($tagsSection as $tag) {
        $tagsArr[$tag->company_num][] = $tag->tags_id;
    }

    foreach ($tagsArr as $CompanyNum => $newTagsIds) {
        $newTagsIds = array_unique($newTagsIds);
        asort($newTagsIds);

        $existedTags = TagsCategoriesStudios::where('CompanyNum', $CompanyNum)->get();
        $existedTagsIds = [];
        foreach ($existedTags as $existedTag) {
            $existedTagsIds[] = $existedTag->tags_id;
        }
        asort($existedTagsIds);

        foreach ($newTagsIds as $tagId) {
            if (!in_array($tagId, $existedTagsIds)) {
                $tag = Tags::find($tagId);
                if ($tag) {
                    $TagsCategoriesStudios = new TagsCategoriesStudios();
                    $TagsCategoriesStudios->tags_category_id = $tag->category_id;
                    $TagsCategoriesStudios->tags_id = $tagId;
                    $TagsCategoriesStudios->CompanyNum = $CompanyNum;
                    $TagsCategoriesStudios->updated_at = date('Y-m-d H:i:s');
                    $TagsCategoriesStudios->save();
                }

            } else {
                $key = array_search($tagId, $existedTagsIds);
                unset($existedTagsIds[$key]);
            }

        }
        foreach ($existedTagsIds as $existedTagId) {
            TagsCategoriesStudios::where('CompanyNum', $CompanyNum)
                ->where('tags_id', $existedTagId)
                ->delete();
        }
    }

    $Cron->end();
} catch (\Throwable $e) {
    LoggerService::error($e);
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    $Cron->cronLog($arr);
}
