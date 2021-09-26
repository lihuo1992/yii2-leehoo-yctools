<?php

namespace leehooyctools;

use Yii;


class StatusReturn
{


    public $status = false;
    public $code = 0;
    public $data = [];
    public $msg = '';

    public function __construct()
    {
        Yii::$app->params['__STATUS_RETURN__'] =array();
        Yii::$app->params['__STATUS_RETURN__']['__METHOD'] =   __METHOD__;
        Yii::$app->params['__STATUS_RETURN__']['status'] =   false;
        Yii::$app->params['__STATUS_RETURN__']['msg'] =   null;
        if(!isset(Yii::$app->params['__STATUS_RETURN__']['__LOG'] ))
        {
            Yii::$app->params['__STATUS_RETURN__']['__LOG'] =   array();
        }
    }

    public function setError($message=''){
        Yii::$app->params['__STATUS_RETURN__']['status'] = false;
        Yii::$app->params['__STATUS_RETURN__']['msg'] = $message;
        $this->status=false;
        $this->msg = $message;
        return false;
    }
    public function setData($data){
        Yii::$app->params['__STATUS_RETURN__']['status'] = true;
        Yii::$app->params['__STATUS_RETURN__']['data'] = $data;
        $this->data = $data;
        return true;
    }
    public function addData($key,$value){

        if(!is_array(Yii::$app->params['__STATUS_RETURN__']['data'])){
            $tem_data = Yii::$app->params['__STATUS_RETURN__']['data'];
            Yii::$app->params['__STATUS_RETURN__']['data'] =array();
            Yii::$app->params['__STATUS_RETURN__']['data'][]=$tem_data;
            Yii::$app->params['__STATUS_RETURN__']['data'][$key]=$value;
        }
        else{
            Yii::$app->params['__STATUS_RETURN__']['data'][$key] = $value;
        }
        $this->data = Yii::$app->params['__STATUS_RETURN__']['data'];
        return true;
    }
    public function setSuc($message=''){
        Yii::$app->params['__STATUS_RETURN__']['status'] = true;
        Yii::$app->params['__STATUS_RETURN__']['msg'] = $message;
        $this->status=true;
        return true;
    }


    public static function setLog($key='',$message=''){
        Yii::$app->params['__STATUS_RETURN__']['__LOG'][$key] = $message;
        return true;
    }

    public static function getLog($key=''){
        if(!empty($key)){
            return Yii::$app->params['__STATUS_RETURN__']['__LOG'][$key];
        }
        else{
            return Yii::$app->params['__STATUS_RETURN__']['__LOG'];
        }

    }

    public  function  out(){
          return  $this->status;
    }

    /**
     * Notes:
     * Author: 黎获
     * @return $this | array |
     * Time：Test
     */
    public  function  output(){
     return $this;
    }


    public static function getError(){
        if(isset(Yii::$app->params['__STATUS_RETURN__'])){
            if(Yii::$app->params['__STATUS_RETURN__']['status']==false ){
                   return  Yii::$app->params['__STATUS_RETURN__']['msg'] ;
            }
            else{
                return null;
            }
        }
        else{
            return null;
        }
    }

    public static function getData(){
        if(isset(Yii::$app->params['__STATUS_RETURN__'])){

            return  Yii::$app->params['__STATUS_RETURN__']['data'] ;

        }
        else{
            return null;
        }
    }
}
