<?php

require_once __DIR__ . '/LinkHelper.php';


class ImageHelper
{

    /**
     * @param string $image
     * @return string
     */
    public static function getImageWithPrefix(string $image): string
    {
        $prefixStartUrl = LinkHelper::getPrefixUrlByHttpHost();
        return $image === '' ? '' : $prefixStartUrl . '/camera/uploads/large/' . $image;
    }

    /**
     * @param string $image
     * @return string
     */
    public static function getImageWithAppPrefix(string $image): string
    {
        $prefixStartUrl = LinkHelper::getAppPrefixUrlByHttpHost();
        return $image === '' ? '' : $prefixStartUrl . '/camera/uploads/large/' . $image;
    }

    /**
     * @param string $imageWithPrefix
     * @return string
     */
    public static function getShortNameImage(string $imageWithPrefix): string
    {
        if($imageWithPrefix === '') {
            return '';
        }
        $arrUrlAvatar = explode("/", $imageWithPrefix);
        return $arrUrlAvatar[count($arrUrlAvatar)-1];
    }

    /**
     * The function return string of logo from login
     * @param string $image
     * @return string
     */
    public static function getLogoImageWithPrefix(string $image): string
    {
        return $image === '' ? '' : App::url() . '/office/files/logo/' . $image;
    }

    /**
     * @return string
     */
    public static function getDefaultAvatar(): string
    {
        return '/assets/img/21122016224223511960489675402.png';
    }
}
