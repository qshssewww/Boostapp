<?php
/**
 * @param $query
 * @param $ClientId
 * @param $MemberShipInfo
 * @param $TrueClientId
 * @param $LimitMultiActivity
 * @return mixed
 */
function addClientCheckAndCount($query, $ClientId, $MemberShipInfo, $LimitMultiActivity)
{
    return ($LimitMultiActivity == 0 ? $query->where('FixClientId', $ClientId) : $query->where('ClientId', $MemberShipInfo->ClientId))->count();
}


