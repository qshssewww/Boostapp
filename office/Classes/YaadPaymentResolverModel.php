<?php

/**
 * @property $id
 * @property $prefix
 * @property $success_url
 * @property $fail_url
 * @property $created_at
 * @property $status 0 - disabled, 1 - active
 */
class YaadPaymentResolverModel extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.yaad_url_resolver';

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLED = 0;

    /**
     * @return YaadPaymentResolverModel[]
     */
    public static function getActiveRoutes(): array
    {
        $routesList = self::where('status', self::STATUS_ACTIVE)->get();

        $result = [];
        foreach ($routesList as $route) {
            $result[$route->prefix] = $route;
        }

        return $result;
    }
}
