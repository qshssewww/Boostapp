<?php

class LogService
{
    public static $table = 'boostapp.club_membership_log';

    const CATEGORY_CREATE = 0;
    const CATEGORY_UPDATE = 1;
    const CATEGORY_DELETE = 2;
    const CATEGORY_ERROR = 3;

    const TYPE_CLUB_MEMBERSHIP = 0;
    const TYPE_ITEM = 1;
    const TYPE_ITEM_ROLE = 2;
    const TYPE_ITEM_LIMIT = 3;

    /**
     * @param $LogID
     * @param $ClubMembershipID
     * @param $data
     * @return void
     */
    public static function addNewClubMembership($LogID, $ClubMembershipID, $data)
    {
        DB::table(self::$table)->insert([
            'LogID' => $LogID,
            'ClubMembershipID' => $ClubMembershipID,
            'Category' => self::CATEGORY_CREATE,
            'ObjectType' => self::TYPE_CLUB_MEMBERSHIP,
            'ObjectID' => $ClubMembershipID,
            'JSONData' => json_encode($data),
        ]);
    }

    /**
     * @param $LogID
     * @param $ClubMembershipID
     * @param $data
     * @return void
     */
    public static function updateClubMembership($LogID, $ClubMembershipID, $data)
    {
        DB::table(self::$table)->insert([
            'LogID' => $LogID,
            'ClubMembershipID' => $ClubMembershipID,
            'Category' => self::CATEGORY_UPDATE,
            'ObjectType' => self::TYPE_CLUB_MEMBERSHIP,
            'ObjectID' => $ClubMembershipID,
            'JSONData' => json_encode($data),
        ]);
    }

    /**
     * @param $LogID
     * @param $ClubMembershipID
     * @param $data
     * @return void
     */
    public static function logError($LogID, $ClubMembershipID, $data)
    {
        DB::table(self::$table)->insert([
            'LogID' => $LogID,
            'ClubMembershipID' => $ClubMembershipID,
            'Category' => self::CATEGORY_ERROR,
            'ObjectType' => self::TYPE_CLUB_MEMBERSHIP,
            'ObjectID' => $ClubMembershipID,
            'JSONData' => json_encode($data),
        ]);
    }

    /**
     * @param $LogID
     * @param $ClubMembershipID
     * @param $type
     * @param $id
     * @param $itemID
     * @param $data
     * @return void
     */
    public static function addNewClubMembershipObject($LogID, $ClubMembershipID, $type, $id, $itemID, $data)
    {
        DB::table(self::$table)->insert([
            'LogID' => $LogID,
            'ClubMembershipID' => $ClubMembershipID,
            'Category' => self::CATEGORY_CREATE,
            'ObjectType' => $type,
            'ObjectID' => $id,
            'ItemID' => $itemID,
            'JSONData' => json_encode($data),
        ]);
    }

    /**
     * @param $LogID
     * @param $ClubMembershipID
     * @param $type
     * @param $id
     * @param $itemID
     * @param $data
     * @return void
     */
    public static function updateClubMembershipObject($LogID, $ClubMembershipID, $type, $id, $itemID, $data)
    {
        DB::table(self::$table)->insert([
            'LogID' => $LogID,
            'ClubMembershipID' => $ClubMembershipID,
            'Category' => self::CATEGORY_UPDATE,
            'ObjectType' => $type,
            'ObjectID' => $id,
            'ItemID' => $itemID,
            'JSONData' => json_encode($data),
        ]);
    }

    /**
     * @param $LogID
     * @param $ClubMembershipID
     * @param $type
     * @param $id
     * @param $itemID
     * @return void
     */
    public static function deleteClubMembershipObject($LogID, $ClubMembershipID, $type, $id, $itemID)
    {
        DB::table(self::$table)->insert([
            'LogID' => $LogID,
            'ClubMembershipID' => $ClubMembershipID,
            'Category' => self::CATEGORY_DELETE,
            'ObjectType' => $type,
            'ObjectID' => $id,
            'ItemID' => $itemID,
        ]);
    }
}
