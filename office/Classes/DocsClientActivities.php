<?php

require_once __DIR__ . '/ClientActivities.php';
require_once __DIR__ . '/Docs.php';
require_once __DIR__ . '/../services/LoggerService.php';

/**
 * @property $id
 * @property $docs_id
 * @property $client_activity_id
 *
 * Class DocsClientActivities
 * used for keeping relation between Docs and ClientActivities
 */
class DocsClientActivities extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.docs_client_activities';

    /**
     * @param $docsId int Docs id
     * @param $clientActivityId int ClientActivities id
     * @see Docs
     * @see ClientActivities
     * @return bool
     */
    public static function saveRelation(int $docsId, int $clientActivityId): bool
    {
        try {
            $relation = new self;
            $relation->client_activity_id = $clientActivityId;
            $relation->docs_id = $docsId;
            return $relation->save();
        } catch (\Throwable $e) {
            LoggerService::error($e);
            return false;
        }
    }

    /**
     * @param $clientActivitiesId
     * @return Docs[]
     */
    public static function getDocs($clientActivitiesId)
    {
        /** @var self[] $docsClientActivities */
        $docsClientActivities = self::where('client_activity_id', $clientActivitiesId)->get();

        $result = [];
        foreach ($docsClientActivities as $docsClientActivity) {
            $docsModel = $docsClientActivity->docs();
            if ($docsModel) {
                $result[] = $docsModel;
            }
        }

        return $result;
    }

    /**
     * @param $docsId
     * @return ClientActivities[]|stdClass[]
     */
    public static function getClientActivities($docsId)
    {
        /** @var self[] $docsClientActivities */
        $docsClientActivities = self::where('docs_id', $docsId)->get();

        $result = [];
        foreach ($docsClientActivities as $docsClientActivity) {
            $clientActivity = $docsClientActivity->clientActivity();
            if ($clientActivity) {
                $result[] = $clientActivity;
            }
        }

        return $result;
    }

    /**
     * @param $docsId
     * @return array
     */
    public static function getClientActivitiesIds($docsId): array
    {
        /** @var self[] $docsClientActivities */
        $docsClientActivities = self::where('docs_id', $docsId)->get();
        $result = [];
        foreach ($docsClientActivities as $docsClientActivity) {
            $result[] = $docsClientActivity->client_activity_id;
        }
        return $result;
    }


    /**
     * @return \Hazzard\Database\Model|Docs
     */
    public function docs()
    {
        return Docs::find($this->docs_id);
    }

    /**
     * @return ClientActivities|stdClass
     */
    public function clientActivity()
    {
        return new ClientActivities($this->client_activity_id);
    }
}
