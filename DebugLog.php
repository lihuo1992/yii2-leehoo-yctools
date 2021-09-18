<?php
namespace leehooyctools;



use Yii;
use leehooyctools\config\GlobalParamsCode;
class DebugLog
{

    /**
     * Notes:
     * Author: 黎获
     * @return mixed|null|array
     */
    static public function getAllLogs(){
        if(isset(Yii::$app->params[GlobalParamsCode::DEBUG]))
        {
            return Yii::$app->params[GlobalParamsCode::DEBUG];
        }
        else
        {
            return null ;
        }
    }
    /**
     * Notes:
     * Author: 黎获
     * @return mixed|null|array
     */
    static public function getAllLogsForSave(){
        if(isset(Yii::$app->params[GlobalParamsCode::RESPONSE_DEBUG_LOG_SAVE]))
        {
            return Yii::$app->params[GlobalParamsCode::RESPONSE_DEBUG_LOG_SAVE];
        }
        else
        {
            return null ;
        }
    }
    /**
     * Notes:
     * Author: 黎获
     * @return mixed|null|array
     */
    static public function getAllTempLogsForSave(){
        if(isset(Yii::$app->params[GlobalParamsCode::RESPONSE_DEBUG_TEMP_LOG_SAVE]))
        {
            return Yii::$app->params[GlobalParamsCode::RESPONSE_DEBUG_TEMP_LOG_SAVE];
        }
        else
        {
            return null ;
        }
    }

    static public function isSaveTempLog(){
        $debugConfig = DebugLog::getDebugConfig();
        return $debugConfig['saveTempDebugLog'];
    }
    static public function isSaveDebugTimeLog(){
        $debugConfig = DebugLog::getDebugConfig();
        return $debugConfig['saveDebugTime'];
    }

    static public function getDebugConfig(){
        if(!isset(Yii::$app->params['__DEBUG_CONFIG_IN_REDIS_']))
        {
            Yii::$app->params['__DEBUG_CONFIG_IN_REDIS_'] = ['saveTempDebugLog'=>0,'saveDebugTime'=>0];
            $redis = Yii::$app->redis;
            $redis->select(3);
            $redisValue = $redis->get('DEBUG_CONFIG');

            if(!empty($redisValue))
            {
                $redisValue = json_decode($redisValue,true);
                if(!empty($redisValue)&& is_array($redisValue))
                {
                    if(isset($redisValue['saveTempDebugLog']))
                    {
                        Yii::$app->params['__DEBUG_CONFIG_IN_REDIS_']['saveTempDebugLog'] = intval($redisValue['saveTempDebugLog']);
                    }
                    if(isset($redisValue['saveDebugTime']))
                    {
                        Yii::$app->params['__DEBUG_CONFIG_IN_REDIS_']['saveDebugTime'] = intval($redisValue['saveDebugTime']);
                    }
                }
            }
        }

        return Yii::$app->params['__DEBUG_CONFIG_IN_REDIS_'];
    }

    /**
     * Notes: 加入新的Debug信息
     * @param $key
     * @param $value
     */
    static public  function saveInfo($key,$value){
        Yii::$app->params[GlobalParamsCode::DEBUG][$key]=$value;
        return;
    }


    /**
     * Notes: 加入新的Debug信息
     * Author: 黎获
     * @param $key
     * @param $value
     */
    static public  function saveInfoArr($key,$value){
        Yii::$app->params[GlobalParamsCode::DEBUG][$key][]=$value;
        return;
    }

    static public function debugLogTime($key=''){
        $current_debug_time = microtime(true);
        if(empty($key)){
            global $INIT_TIME_INDEX;
            Yii::$app->params[GlobalParamsCode::DEBUG]['debug_time']['__TOTAL'] = $current_debug_time - $INIT_TIME_INDEX ;
            Yii::$app->params[GlobalParamsCode::DEBUG]['debug_time']['_INIT_LOAD_'] = $current_debug_time - $INIT_TIME_INDEX ;
            Yii::$app->params['current_debug_time'] =$current_debug_time;
            Yii::$app->params['current_debug_time_init'] =$INIT_TIME_INDEX;
            return ;
        }
        else{
            if(empty(Yii::$app->params['current_debug_time_init'])){
                Yii::$app->params['current_debug_time'] =$current_debug_time;
                Yii::$app->params['current_debug_time_init'] =$current_debug_time;
            }
            Yii::$app->params[GlobalParamsCode::DEBUG]['debug_time']['__TOTAL'] = $current_debug_time-  Yii::$app->params['current_debug_time_init'] ;
            Yii::$app->params[GlobalParamsCode::DEBUG]['debug_time'][$key] = $current_debug_time- Yii::$app->params['current_debug_time'] ;

            Yii::$app->params['current_debug_time'] =$current_debug_time;
            return ;
        }
    }

}
