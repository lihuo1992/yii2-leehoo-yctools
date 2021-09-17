<?php
namespace leehooyctools\config;

class RedisKey
{
    const LAST_CACHE_MOD_TIME = '__LAST_CACHE_MOD_TIME';
    const ACTION_CACHE_SWITCH = '__ACTION_CACHE_SWITCH';
    const AMZ_PARENTASINS_INFO = '_AMZ_PA';
    public static function rateLimitHash($action)
    {
        $route = $action->controller->module->id.'/'.$action->controller->id.'/'.$action->id;
        $redisKey = 'RateLimitUser:'.$route;
        return $redisKey;
    }
}
