<?php
/**
 *   CREATER: 黎获
 *   TIME:2019/1/30 16:05
 *   NOTES: 记录log方法
 */
namespace leehooyctools;

use leehooyctools\ResponseLog;
//use app\models\ErrorCode;
use leehooyctools\config\RedisKey;
use leehooyctools\config\ResponseCode;

use Yii;
use yii\web\Response;

class JsonReturn
{

    public $debug =null;

    public $msg ='';
    public $code = 0;
    public $errcode = 0;
    public $err_code = ''; //登陆过期适配前端刷新页面
    public $extend_data=[];

    public $data = [];

    private $redis_data_key = null;
    private $debug_log_time_lock=false;
    private $cache_data_key = null;
//    private $actionUniqueId;
//    private $requstDataKey;


    private $cacheDataKey = null;
    private $cacheData = null;
    private $cacheDataExpire = 12*60*60; //12小时

    public function __construct()
    {
//        $this->actionUniqueId = Yii::$app->controller->action->uniqueId;

        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:*');
        //请求头
        header('Access-Control-Allow-Headers:*');
        // 响应头设置
        header('Access-Control-Allow-Credentials:false');


        $this->debug_log_time_lock=false;
        $this->debugLogTime();
        Yii::$app->params['_XXXAJAXRETURNXXX_'] = $this;
    }

    public static function extend(){

        if(empty(Yii::$app->params['_XXXAJAXRETURNXXX_'])){
            return null;
        }
        else{
            return Yii::$app->params['_XXXAJAXRETURNXXX_'];
        }
    }
    public function setDataToRedisKey($request_Data){
        $request_Data_str = json_encode($request_Data);
        $request_Data_arr = json_decode($request_Data_str,true);
        $action_id = Yii::$app->controller->action->uniqueId;
        $this->redis_data_key = md5($action_id.$request_Data_str);
        $redis_data['route']=$action_id;
        $redis_data['__LAST_TIME__']=time();
        $redis_data['request_data']=$request_Data_arr;
        $this->redis_data=$redis_data;
        return $this->redis_data_key;
    }

    public function setCacheKey($dataKey){

        $redis = Yii::$app->redis;
        $redis->select(2);
        $actionCacheSwitch = $redis->get(RedisKey::ACTION_CACHE_SWITCH);
        if(!empty($actionCacheSwitch) && $actionCacheSwitch==1)
        {
            if(is_array($dataKey) || is_object($dataKey))
            {
                $dataKey = json_encode($dataKey);
            }
            $action_id = Yii::$app->controller->action->uniqueId;
            $dataKey = md5($action_id.$dataKey);
            $this->cacheDataKey='_ACT:'.$dataKey;
            return $dataKey;
        }
    }

    public function setDataToCacheKey($request_Data){
        $request_Data_str = json_encode($request_Data);
        $request_Data_arr = json_decode($request_Data_str,true);
        $action_id = Yii::$app->controller->action->uniqueId;
        $this->cache_data_key = '_ACT_RT_:'.md5($action_id.$request_Data_str);
        $redis_data['route']=$action_id;
        $redis_data['__LAST_TIME__']=time();
        $redis_data['request_data']=$request_Data_arr;
        $this->cache_data=$redis_data;
        return $this->cache_data_key;
    }
    public function setPreDataFormat($data_format){
        $this->data=$data_format;
        Yii::$app->params['__AJAX_RETURN_DATA_FORMAT__']=$data_format;
    }
    public  function  setExtendData($key,$value){
        $this->extend_data[$key]=$value;
    }

    public function getCacheDataToInit(){
        if(!empty($this->cacheDataKey))
        {
            $redis = Yii::$app->redis;
            $redis->select(2);
            $cacheData = $redis->get($this->cacheDataKey);
            if(!empty($cacheData))
            {
                $cacheData = json_decode($cacheData,true);
                if(!empty($cacheData) && is_array($cacheData))
                {
                    if(isset($cacheData['modTime']) && isset($cacheData['ac']) && isset($cacheData['return']))
                    {
                        $this->cacheData = $cacheData['return'];
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function returnData(){
        if(!empty($this->cacheDataKey) && !empty($this->cacheData))
        {
            $this->cacheData['_chk_d'] =$this->cacheDataKey;
            return  $this->cacheData;
        }
        $returns = ['code'=>$this->code,'msg'=>$this->msg,'data'=>$this->data];

        Yii::$app->params[GlobalParamsCode::JSON_RETURNS] =['code'=>$this->code,'msg'=>$this->msg];


        if($this->debug_log_time_lock){
            $this->debugLogTime('__REMAIN_TIME');
        }
        if($this->debug_log_time_lock && !empty(Yii::$app->params[GlobalParamsCode::DEBUG]['debug_time'])){
            ResponseLog::saveDebug('__DEBUG_TIME_LOG__',Yii::$app->params[GlobalParamsCode::DEBUG]['debug_time']);
        }
        if($this->code!=200){
            ResponseLog::saveDebug('__RESPONSE_MSG__',$returns['msg']);
        }

        if(!empty($this->extend_data)){
            foreach($this->extend_data as $ek=>$ev){
                $returns[$ek]=$ev;
            }
        }

        if(YII_DEBUG){
            if(empty( $this->debug)){
                $this->debug = array();
            }
            if(isset( Yii::$app->params[GlobalParamsCode::DEBUG]) && !empty(Yii::$app->params[GlobalParamsCode::DEBUG])) {
                $this->debug = array_merge(  $this->debug,Yii::$app->params[GlobalParamsCode::DEBUG]);
            }
            $returns['__DEBUG'] =   $this->debug ;
        }
        if(!empty($this->err_code)){
            $returns['err_code'] = $this->err_code;
        }

        if($returns['code']!=200){
            $word_index = ['A'=>'10','B'=>'11','C'=>'12','D'=>'13','E'=>'14','F'=>'15','G'=>'16','H'=>'17','I'=>'18','J'=>'19','K'=>'20','L'=>'21','M'=>'22','N'=>'23','O'=>'24','P'=>'25','Q'=>'26','R'=>'27','S'=>'28','T'=>'29','U'=>'30','V'=>'31','W'=>'32','X'=>'33','Y'=>'34','Z'=>'35'];
            if(!empty(Yii::$app->params['__ERR_CODE_SAVE_INFO__'])){
                /**
                $err_code_res = Yii::$app->params['__ERR_CODE_SAVE_INFO__'];
                $err_line = $err_code_res['err_line'];
                $err_code_obj = ErrorCode::findOne(['err_line'=>$err_line]);
                if(!empty($err_code_obj)){
                    $err_code = $err_code_obj->err_code;
                }
                else{
                    $module = $err_code_res['module'];
                    $controller = $err_code_res['controller'];
                    $action = $err_code_res['action'];
                    $err_code = $word_index[substr(strtoupper($module),0,1)].$word_index[substr(strtoupper($controller),0,1)].$word_index[substr(strtoupper($action),0,1)];
                    $err_code.=rand(10,99);
                    $err_code_obj= new ErrorCode();
                    $err_code_obj->create_time =time();
                    $err_code_obj->err_line=$err_line;
                    $err_code_obj->module=$module;
                    $err_code_obj->action=$action;
                    $err_code_obj->controller=$controller;
                    $err_code_obj->err_code=$err_code;
                    $err_code_obj->save();
                }
                $returns['msg'].='['.$err_code.']';
                 * **/
            }
        }
        else
        {
            if(!empty($this->cacheDataKey))
            {
                $redis = Yii::$app->redis;
                $redis->select(2);
                $expireTime = $this->cacheDataExpire;
                $cacheData = array();
                $cacheData['modTime']=time();
                $cacheData['ac']=Yii::$app->controller->action->id;
                $cacheData['return']=$returns;
                $redis->setex($this->cacheDataKey,$expireTime,json_encode($cacheData));
                $returns['_chk_s']=$this->cacheDataKey;
            }

        }
        if(
            $returns['code']==ResponseCode::NEED_UPGRADE
            || $returns['code']==ResponseCode::AUTH_FORBID
        )
        {
            if(isset(Yii::$app->params['__USER_KEFU_INFO']))
            {
                $kefuInfo =Yii::$app->params['__USER_KEFU_INFO'];
            }
            else
            {
//                $kefuInfo = GlobalParams::getRandKefuInfo();
            }
            $returns['__extra']=['kefuQrCodeImgUrl'=>$kefuInfo['qrCode']];
        }
        return   $returns;
    }
    private function output(){
        $returns =$this->returnData();
        if (isset($_GET['callback'])) {
            $returns_jsonp = array();
            $returns_jsonp['data'] = $returns;
            Yii::$app->response->format=Response::FORMAT_JSONP;
            $returns_jsonp['callback']=$_GET['callback'];
            $returns = $returns_jsonp;
        }
        else{
            Yii::$app->response->format=Response::FORMAT_JSON;
        }
        return   $returns;
    }

    public function MissParams($err_code=0){
        $this->msg = Yii::t('app','parameter_missing');
        return $this->Err($err_code);
    }

    public function DataError($err_code=0){
        $this->msg = Yii::t('app','data_error');
        return $this->Err($err_code);
    }
    public function Done(){

        if($this->code!=200){
            return  $this->Err();
        }
        else{
            return  $this->Suc();
        }
    }
    public function Suc(){
        $this->code=ResponseCode::SUCCESS;
        return $this->output();
    }
    public function setSuc(){
        $this->code=ResponseCode::SUCCESS;
        return $this;
    }
    public function setMiss(){
        $this->code=ResponseCode::DATA_DEFAULT;
        return $this;
    }
    public function setNotFound(){
        $this->code=ResponseCode::NOT_FOUND;
        return $this;
    }
    public function setNeedUpgrade(){
        $this->code=ResponseCode::NEED_UPGRADE;
        return $this;
    }
    public function setReload(){
        $this->code=ResponseCode::PAGE_RELOAD;
        return $this;
    }
    public function setDataExpired(){
        $this->code=ResponseCode::DATA_EXPIRED;
        return $this;
    }
    public function setPermissionDend(){
        $this->code=ResponseCode::AUTH_FORBID;
        return $this;
    }
    public function setErr($msg=''){
        if(!empty($msg)){
            $this->msg = $msg;
        }
        $this->code = 0;
        $debug_prid = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);

        if(!empty($debug_prid)){
            $debug_log = $debug_prid[0];
            if(isset($debug_log['line']) && isset($debug_log['file'])){
                $line_file = $debug_log['file'].','.$debug_log['line'];
                 Yii::$app->params['__ERR_CODE_SAVE_INFO__']=[
                     'module'=>Yii::$app->controller->module->id,
                     'controller'=>Yii::$app->controller->id,
                     'action'=>Yii::$app->controller->action->id,
                     'err_line'=>$line_file
                 ];
                 DebugLog::saveInfo('__ERR_CODE_SAVE_INFO__',Yii::$app->params['__ERR_CODE_SAVE_INFO__']);
            }
        }
        return $this;
    }

    public function Err($errcode=0,$msg=''){
        if($this->code==200)
        {
            $this->code=0;
        }

        if($errcode>0){
            $this->errcode = $errcode;
        }
        if(!empty($msg)){
            $this->msg = Yii::t('app',$msg);
        }

        if($this->errcode){
            $this->msg.='['. $this->errcode.']';
        }
        return $this->output();
    }

    public function addDebug($key,$value){
        DebugLog::saveInfo($key,$value);
    }

    public function debugLogTime($key=''){
        DebugLog::debugLogTime('actionStart');
    }

    public function OutForce(){
        $this->output(true);
    }
}
