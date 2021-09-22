<?php
namespace leehooyctools\config;
use leehooyctools\Common;
use Yii;
class GlobalParamsCode
{
    const RESPONSE_DEBUG_LOG_SAVE = '__RESPONSE_DEBUG_LOG_SAVE';
    const RESPONSE_DEBUG_TEMP_LOG_SAVE = '__RESPONSE_DEBUG_TEMP_LOG_SAVE';
    const RESPONSE_LEVEL = '__RESPONSE_LEVEL';
    const RESPONSE_HTTP_CODE = '__RESPONSE_HTTP_CODE';
    const DEBUG = '__DEBUG_INFO_IN_JSON_RETURN';
    const JSON_RETURNS = '__JSON_RETURNS_DETAIL';

    /**
     * Notes: 生成随机错误码
     * Author: LeeHoo
     * @return string
     * Create Time: 2021/8/18 2:16 下午
     */
    static public function setErrorCode(){
        $err_code = Common::GetRandStr(16);
        Yii::$app->params['__ExceptionErrorLog_Code__'] = $err_code;
        return $err_code;
    }

    /**
     * Notes: 获取错误码
     * Author: LeeHoo
     * @param false $must
     * @return mixed|string|null
     * Create Time: 2021/8/18 2:17 下午
     */
    static public function getErrorCode($must=false){
        if(!empty(Yii::$app->params['__ExceptionErrorLog_Code__'])){
            return Yii::$app->params['__ExceptionErrorLog_Code__'];
        }
        else{
            if($must){
                return self::setErrorCode();
            }
            else{
                return null;
            }
        }
    }
}
