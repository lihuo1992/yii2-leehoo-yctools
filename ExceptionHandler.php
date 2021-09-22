<?php
/**
 *   NOTES: 异常日志
 */
namespace leehooyctools;

use leehooyctools\Common;
use leehooyctools\config\GlobalParamsCode;
use leehooyctools\DebugLog;
use leehooyctools\JsonReturn;
use leehooyctools\LittleBigHelper;
use leehooyctools\models\ExceptionErrorLog;
use yii\web\ErrorHandler;
use Yii;

class ExceptionHandler extends ErrorHandler
{
    /**
     * Notes:
     * Author: LeeHoo
     * @param \Error|\Exception $exception
     * Create Time: 2021/8/18 2:03 下午
     */
    public function  renderException($exception){
        Yii::error($exception,'ExceptionError');
        $exception_arr = Common::object_array($exception);

        if(is_array($exception_arr)){
            $statusCode = $exception->getCode();
            if(empty($statusCode) && $exception->statusCode!==null)
            {
                $statusCode = $exception->statusCode;
            }
        }else{
            $statusCode = 500;
        }

        if($statusCode!=404){
            $ex_error_log = new ExceptionErrorLog();
            $encode_type=0;
            $log_arr =array();
            $log_arr['ErrorMessage'] = $exception->getMessage();
            $log_arr['Code'] = $exception->getCode();
            $log_arr['File'] = $exception->getFile();
            $log_arr['ErrorLine'] = $exception->getLine();
            $log_arr['TraceString'] = $exception->getTraceAsString();
            $log_arr['Previous'] = $exception->getPrevious();
            $ex_str = json_encode($log_arr);
            if(empty($ex_str)){
                $new_exception_arr = array();
                foreach($exception_arr as $key =>$vol){
                    $str = preg_replace('#[^\x{4e00}-\x{9fa5}A-Za-z0-9]#u','',$key);
                    $new_exception_arr[$str] = $vol;
                }
                $ex_str = $this->arrayeval($new_exception_arr,true);
                $encode_type = 1;
            }
            $ex_error_log->content = $ex_str;
            $ex_error_log->encode_type = $encode_type;
            $ex_error_log->create_time = time();
            $ex_error_log->status_code = $statusCode;
            $ex_error_log->code = GlobalParamsCode::getErrorCode(true);

            if(\Yii::$app instanceof  \yii\console\Application){

            }
            else{
                if(!empty(Yii::$app->user->id)){
                    $ex_error_log->user_id = Yii::$app->user->id;
                }
                if(!empty(Yii::$app->user->identity->username)){
                    $ex_error_log->username = Yii::$app->user->identity->username;
                }

                if(!empty(Yii::$app->request->getHostInfo())){
                    $ex_error_log->host = Yii::$app->request->getHostInfo();
                }
                $action =  Yii::$app->controller->action;
                $ex_error_log->module=$action->controller->module->id;
                $ex_error_log->controller=$action->controller->id;
                $ex_error_log->action=$action->id;
            }
            $ex_error_log->ip = LittleBigHelper::getRealIp();
            $ex_error_log->save(false);
            $ex_error_log_id = $ex_error_log->id;
            $errorMsgStr =  $ex_error_log_id.'-'.$ex_error_log->code;

            /*记录log*/
            global $__RESPONSE_LOG_CLASS__;

            if(!empty($__RESPONSE_LOG_CLASS__))
            {
                $__RESPONSE_LOG_CLASS__->level = RESPONSE_LOG_LEVEL_ERROR;
                $__RESPONSE_LOG_CLASS__->httpCode = $statusCode;
                $__RESPONSE_LOG_CLASS__->saveDebug('ExceptionErrorLog-MySQL',$errorMsgStr);
                $__RESPONSE_LOG_CLASS__->saveDebug('ExceptionErrorLog-Msg',$exception->getMessage());
                $__RESPONSE_LOG_CLASS__->saveIn();
            }

        }

        $headers=Yii::$app->request->headers;
        $accept = $headers->get('Accept');

        if(Yii::$app->request->isAjax || strpos($accept,'application/json')!==false){
            $http = array (
                100 => "HTTP/1.1 100 Continue",
                101 => "HTTP/1.1 101 Switching Protocols",
                200 => "HTTP/1.1 200 OK",
                201 => "HTTP/1.1 201 Created",
                202 => "HTTP/1.1 202 Accepted",
                203 => "HTTP/1.1 203 Non-Authoritative Information",
                204 => "HTTP/1.1 204 No Content",
                205 => "HTTP/1.1 205 Reset Content",
                206 => "HTTP/1.1 206 Partial Content",
                300 => "HTTP/1.1 300 Multiple Choices",
                301 => "HTTP/1.1 301 Moved Permanently",
                302 => "HTTP/1.1 302 Found",
                303 => "HTTP/1.1 303 See Other",
                304 => "HTTP/1.1 304 Not Modified",
                305 => "HTTP/1.1 305 Use Proxy",
                307 => "HTTP/1.1 307 Temporary Redirect",
                400 => "HTTP/1.1 400 Bad Request",
                401 => "HTTP/1.1 401 Unauthorized",
                402 => "HTTP/1.1 402 Payment Required",
                403 => "HTTP/1.1 403 Forbidden",
                404 => "HTTP/1.1 404 Not Found",
                405 => "HTTP/1.1 405 Method Not Allowed",
                406 => "HTTP/1.1 406 Not Acceptable",
                407 => "HTTP/1.1 407 Proxy Authentication Required",
                408 => "HTTP/1.1 408 Request Time-out",
                409 => "HTTP/1.1 409 Conflict",
                410 => "HTTP/1.1 410 Gone",
                411 => "HTTP/1.1 411 Length Required",
                412 => "HTTP/1.1 412 Precondition Failed",
                413 => "HTTP/1.1 413 Request Entity Too Large",
                414 => "HTTP/1.1 414 Request-URI Too Large",
                415 => "HTTP/1.1 415 Unsupported Media Type",
                416 => "HTTP/1.1 416 Requested range not satisfiable",
                417 => "HTTP/1.1 417 Expectation Failed",
                500 => "HTTP/1.1 500 Internal Server Error",
                501 => "HTTP/1.1 501 Not Implemented",
                502 => "HTTP/1.1 502 Bad Gateway",
                503 => "HTTP/1.1 503 Service Unavailable",
                504 => "HTTP/1.1 504 Gateway Time-out"
            );

            header($http[200]);

            DebugLog::saveInfo('exception_code',$exception->getCode());
            DebugLog::saveInfo('status_code',$statusCode);

            DebugLog::saveInfo('exception_detail',$exception);
            DebugLog::saveInfo('exception_msg',$exception->getMessage());
            DebugLog::saveInfo('exception_line',$exception->getLine());
            DebugLog::saveInfo('exception_file',$exception->getFile());

//            $this->errorAction = 'site/errorJson';
            if($statusCode==='')
            {
                $statusCode=500;
            }
            $jsonReturn = new JsonReturn();
            $jsonReturn->code = $statusCode;
            if($exception->getMessage()=='Login Required' || $exception->getMessage()=='API Login Required'){
                $jsonReturn->msg='请先登录';
            }else{

                if($jsonReturn->code!=401 && $jsonReturn->code!=403 && $jsonReturn->code!=429)
                {
                    if(YII_DEBUG)
                    {
                        $jsonReturn->msg=$exception->getMessage().'['.GlobalParamsCode::getErrorCode(false).']';
                    }
                    else
                    {
                        $jsonReturn->msg=Yii::t('app','server_busy').'[950001]['.GlobalParamsCode::getErrorCode(false).']';
                    }
                }
                else
                {
                    $returnMsg = $exception->getMessage();
                    if($jsonReturn->code==401 && $jsonReturn->msg=='Your request was made with invalid or expired JSON Web Token.')
                    {
                        $jsonReturn->msg='登录过期，请重新登录';
                    }
                    elseif($jsonReturn->code==429 && $jsonReturn->msg=='Rate limit exceeded.')
                    {
                        $jsonReturn->msg='操作频繁，请稍后再试！';
                    }
                    else
                    {
                        if(is_string($returnMsg))
                        {
                            $returnMsgJson=json_decode($returnMsg,true);
                            if(!empty($returnMsgJson) && is_array($returnMsgJson))
                            {
                                DebugLog::saveInfo('_ErrorMSG',$returnMsgJson);
                                $returnMsg = '服务器繁忙，请稍后再试[950429]';
                            }
                        }
                        $jsonReturn->msg=$returnMsg;
                    }
                }
            }
            if(!empty(Yii::$app->params['__AJAX_RETURN_DATA_FORMAT__']) && empty($ajax->data)){
                $jsonReturn->data =Yii::$app->params['__AJAX_RETURN_DATA_FORMAT__'];
            }
            $this->errorAction = 'site/errorJson';
             Yii::$app->params['__JSON_RETURN_DATA'] = $jsonReturn->returnData();
             echo json_encode($jsonReturn->returnData());exit;
        }
        else{
            parent::renderException($exception);
        }

    }

    function arrayeval($array,$format=false,$level=0){
        $space=$line='';
        if(!$format){
            for($i=0;$i<=$level;$i++){
                $space.="\t";
            }
            $line="\n";
        }
        $evaluate='Array'.$line.$space.'('.$line;
        $comma=$space;
        foreach($array as $key=> $val){
            $key=is_string($key)?'\''.addcslashes($key,'\'\\').'\'':$key;

            if(is_bool($val)){
                if($val){
                    $val ='true';
                }
                else{
                    $val ='false';
                }
            }
            else{
                $val=!is_array($val)&&(!preg_match('/^\-?\d+$/',$val)||strlen($val) > 12)?'\''.addcslashes($val,'\'\\').'\'':$val;
            }
            if(is_array($val)){
                $evaluate.=$comma.$key.'=>'.$this->arrayeval($val,$format,$level+1);
            }else{
                $evaluate.=$comma.$key.'=>'.$val;
            }
            $comma=','.$line.$space;
        }
        $evaluate.=$line.$space.')';
        return $evaluate;
    }

}
