<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../office/Classes/AppSettings.php';
require_once __DIR__ . '/../../office/Classes/Settings.php';
require_once __DIR__ . '/../../office/Classes/Users.php';
require_once __DIR__ . '/../../office/services/LoggerService.php';

class MultiUserHelper
{
    /**
     * Returns list of available studios for $user - gets all brands and user records with the same multiUserId
     * @param User $user
     * @return array
     */
    public static function getList(User $user)
    {
        $studiosList = [];

        try {
            $CompanyNum = $user->CompanyNum;

            $CompanySettings = Settings::getSettings($CompanyNum);
            $appSettings = AppSettings::getByCompanyNum($CompanyNum);

            if ($CompanySettings->BrandsMain != 0 && $user->role_id != 1) {
                $customBrands = DB::table('brands')
                    ->select('id', 'CompanyNum', 'BrandName', 'FinalCompanynum')
                    ->where('Status', '=', '0')
                    ->where('CompanyNum', '=', $CompanySettings->BrandsMain)
                    ->get();

                if (!empty($customBrands)) {
                    foreach ($customBrands as $brand) {
                        $studiosList[] = [
                            'id' => $brand->id,
                            'isCurrentStudio' => $brand->FinalCompanynum == $user->CompanyNum,
                            'name' => $brand->BrandName,
                            'logo' => $appSettings->logoImg,
                            'type' => 'brand',
                            'CompanyNum' => $brand->FinalCompanynum,
                        ];
                    }
                }
            }

            if ($user->multiUserId) {
                /** @var Users[] $anotherUsersWithSameMultiUser */
                $anotherUsersWithSameMultiUser = Users::where('multiUserId', $user->multiUserId)->where('status', '=', '1')->get();

                if (!empty($anotherUsersWithSameMultiUser)) {
                    foreach ($anotherUsersWithSameMultiUser as $anotherUser) {
                        $skip = false;

                        foreach ($studiosList as $k => $studioItem) {
                            if ($studioItem['CompanyNum'] == $anotherUser->CompanyNum) {
                                $studiosList[$k]['type'] = 'multiuser';
                                $studiosList[$k]['id'] = $anotherUser->id;

                                $skip = true;
                            }
                        }

                        if ($skip) {
                            continue;
                        }

                        $appSettings = AppSettings::getByCompanyNum($anotherUser->CompanyNum);
                        if (empty($appSettings)) {
                            continue;
                        }

                        $companySettings = $appSettings->studioSettings();
                        if (empty($companySettings) || $companySettings->Status != 0) {
                            continue;
                        }

                        $studiosList[] = [
                            'id' => $anotherUser->id,
                            'isCurrentStudio' => $anotherUser->CompanyNum == $user->CompanyNum,
                            'name' => $companySettings->AppName,
                            'logo' => $appSettings->logoImg,
                            'type' => 'multiuser',
                            'CompanyNum' => $appSettings->CompanyNum,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);
        }

        return $studiosList;
    }
}
