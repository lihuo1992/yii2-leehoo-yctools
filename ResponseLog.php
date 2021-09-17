<?php

namespace leehooyctools;
use Yii;
use leehooyctools\config\GlobalParamsCode;

class ResponseLog
{
    public $level;        // warn info error (也考虑可以取消)
    public $category;     // 日志分类 （可以取消）

    public $code;        // json return code 200（成功）/其他（错误码）
    public $msg;        //  返回的提示信息

    public $timestamp;    // 请求时间
    public $responseTime;       // 响应时间

    public $userId;       // 用户ID
    public $username;       // 用户名 （可以用作昵称）

    public $requestType;       // 1:ajax , 2:api ,3:view (可取消)
    public $method;       // POST GET

    public $moduleId;       //
    public $controllerId;       //
    public $actionId;       //

    public $httpCode;       //
    public $clientIP;       // 客户端IP
    public $serverIP;       // 服务器IP
    public $port;       // 服务器端口（可以取消）
    public $host;       // 当前域名
    public $userAgent;       // HTTP_USER_AGENT

    public $params;       // 请求参数
    public $debugData;       // debug 数据内容

    public $ver;       // 版本号

    private $saveLock = false; // true: 不再写入


    public function __construct()
    {
        \app\tool\DebugLog::debugLogTime();
        $this->level = RESPONSE_LOG_LEVEL_INFO;
//                file_put_contents(ROOT_PATH.'/runtime/logs/ResponseLog.log','save:xx'."\n",FILE_APPEND);
//        self::setLevel(RESPONSE_LOG_LEVEL_INFO);
    }

    static function initClass(){
        global $__RESPONSE_LOG_CLASS__;
        $__RESPONSE_LOG_CLASS__ = new ResponseLog();
    }
    static function getClass(){
        global $__RESPONSE_LOG_CLASS__;
        if(!empty($__RESPONSE_LOG_CLASS__))
        {
            $__RESPONSE_LOG_CLASS__ = new ResponseLog();
        }
        return $__RESPONSE_LOG_CLASS__;
    }


    public static function setHttpCode($statusCode){
        Yii::$app->params[GlobalParamsCode::RESPONSE_HTTP_CODE] = $statusCode;
    }

    public function getHttpCode(){
        if(!empty($this->httpCode))
        {
            return $this->httpCode;
        }
        if(empty(Yii::$app->params[GlobalParamsCode::RESPONSE_HTTP_CODE]))
        {
            $httpCode =   Yii::$app->response->statusCode??200;
        }
        else
        {
            $httpCode = Yii::$app->params[GlobalParamsCode::RESPONSE_HTTP_CODE];
        }

        return $httpCode;
    }

    public static function saveDebug($key,$value,$isTemp=false){
        if($isTemp)
        {
            //临时开启后才会写入日志
            $paramsKey = GlobalParamsCode::RESPONSE_DEBUG_TEMP_LOG_SAVE;
        }
        else
        {
            $paramsKey = GlobalParamsCode::RESPONSE_DEBUG_LOG_SAVE;
        }

        if(!empty($key)){
            if(!empty(Yii::$app->params[$paramsKey][$key])){
                $key_value = Yii::$app->params[$paramsKey][$key];
                Yii::$app->params[$paramsKey][$key] =array();
                Yii::$app->params[$paramsKey][$key][] =$key_value;
                Yii::$app->params[$paramsKey][$key][] =$value;
            }
            else{
                Yii::$app->params[$paramsKey][$key] =$value;
            }
            if(YII_DEBUG){
                Yii::$app->params[GlobalParamsCode::DEBUG]['__fs'][$key]=$value;
            }
        }
        return ;
    }

    public static function saveDebugTemp($key,$value){
        ResponseLog::saveDebug($key,$value,true);
    }



    public function saveIn() {
        if($this->saveLock)
        {
            return ;
        }

        $this->code = '';
        $this->msg = '';

        if(isset(Yii::$app->params[GlobalParamsCode::JSON_RETURNS]))
        {
            $this->code = Yii::$app->params[GlobalParamsCode::JSON_RETURNS]['code'];
            $this->msg = Yii::$app->params[GlobalParamsCode::JSON_RETURNS]['msg'];
        }
        $this->category = '';

        global $INIT_TIME_INDEX;
        $this->timestamp = $INIT_TIME_INDEX;
        $endTime = microtime(true);
        $this->responseTime = intval(($endTime - $INIT_TIME_INDEX)*1000);

        if(empty(Yii::$app->user->id)){
            $userId = '0';
            $username = '';
        }else{
            $userId =Yii::$app->user->id;
            if(!empty(Yii::$app->user->identity->name))
            {
                $username = Yii::$app->user->identity->name;
            }
            else
            {
                $username = Yii::$app->user->identity->username;
            }

        }

        $this->userId = $userId;
        $this->username = $username;

        $params = array();
        $method = 'GET';
        $params['_GET'] = Yii::$app->request->get();
        if(Yii::$app->request->isPost)
        {
            $method = 'POST';
            $params['_POST'] = Yii::$app->request->post();
        }
        $payLoad = Yii::$app->request->payload();
        if(!empty($payLoad))
        {
            if(isset($payLoad['params']))
            {
                unset($payLoad['params']);
            }
            $params['_RAW']= $payLoad;
        }
        $this->method = $method;



        $moduleId = Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        if(empty($moduleId) || empty($controllerId) || empty($actionId)){
            $url = $params['_GET']['r'];
            $urlArr = explode('/',$url);
            $moduleId = $urlArr[0]??'';
            $controllerId = $urlArr[1]??'';
            $actionId = $urlArr[2]??'';
        }
        $this->moduleId = $moduleId;
        $this->controllerId = $controllerId;
        $this->actionId = $actionId;

        unset($params['_GET']['r']);
        if(empty($params['_GET']))
        {
            unset($params['_GET']);
        }
        if(empty($params['_POST']))
        {
            unset($params['_POST']);
        }
        if(!empty($params))
        {
            $this->params = json_encode($params);
        }
        else
        {
            $this->params = '';
        }

        $this->httpCode = $this->getHttpCode();
        $this->clientIP = LittleBigHelper::getRealIp();
        $this->serverIP = LittleBigHelper::getLocalInetIp();
        $this->port = $_SERVER["SERVER_PORT"]??'';
        $this->host = $_SERVER["HTTP_HOST"]??'';
        $this->userAgent = $_SERVER["HTTP_USER_AGENT"]??'';
        $debugData  = DebugLog::getAllLogsForSave();

        $tempDebugData = DebugLog::getAllTempLogsForSave();
        if(!empty($tempDebugData))
        {
            if(DebugLog::isSaveTempLog()){
                if(empty($debugData))
                {
                    $debugData = $tempDebugData;
                }
                else
                {
                    $debugData = array_merge($debugData,$tempDebugData);
                }
            }
        }

        if(DebugLog::isSaveDebugTimeLog())
        {
            DebugLog::debugLogTime('AFTER-ACTION');
            $debugData['__DEBUG_TIME'] = Yii::$app->params[GlobalParamsCode::DEBUG]['debug_time'];
        }


        if(!empty($debugData))
        {
            $this->debugData = json_encode($debugData);
        }
        else
        {
            $this->debugData = '';
        }

        if($this->code ==='')
        {
            $this->code = $this->httpCode;
        }

        $header = Yii::$app->request->headers;
        if($header->has('ey-plugin-version'))
        {
            $this->ver = $header->get('ey-plugin-version');
        }
        else
        {
            $this->ver = '';
        }

//        var_dump($ver);exit;
//
//        echo json_encode($this->ver);exit;
        $propertiesList = get_class_vars(__CLASS__);

        $logData = [];
        foreach($propertiesList as $key =>$vol)
        {
            if($key!='saveLock')
            {
                $logData[$key] = $this->$key;
            }

        }

//        var_dump(json_encode($logData));exit;
//        file_put_contents(ROOT_PATH.'/runtime/logs/ResponseLog.log',json_encode($logData)."\n",FILE_APPEND);
        Yii::info(json_encode($logData),'ResponseLog');
//        file_put_contents(ROOT_PATH.'/runtime/response.log',time()."\n",FILE_APPEND);

        $this->saveLock = true;
    }

//    public function __destruct() {
//
//        if(!$this->saveLock)
//        {
//            $this->saveIn();
//        }
//    }


}
