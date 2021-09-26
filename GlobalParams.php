<?php
namespace leehooyctools;

use leehooyctools\models\EyUser\UserKefu;
use Yii;


class GlobalParams{
    
    public static function getKefuGroupRes(){
        if(!empty(Yii::$app->params['__KEFU_GROUP_RES_CACHE']))
        {
            return Yii::$app->params['__KEFU_GROUP_RES_CACHE'] ;
        }
        else
        {
            $where = array();
            $where[]='and';
            $where[]=['state'=>1];
            $kefuRes = UserKefu::find()->where($where)->asArray()->all();
            Yii::$app->params['__KEFU_GROUP_RES_CACHE'] =$kefuRes;
            return Yii::$app->params['__KEFU_GROUP_RES_CACHE'];
        }
    }

    public static function getRandKefuInfo(){
        $kefuRes = GlobalParams::getKefuGroupRes();
        $randKefuKey = array_rand($kefuRes,1);
        $kefuInfo = $kefuRes[$randKefuKey];
        return $kefuInfo;
    }

    public static function getKefuInfoById($kefuId){
        if(!empty(Yii::$app->params['__KEFU_GROUP_RES_CACHE']))
        {
            $kefuRes = Yii::$app->params['__KEFU_GROUP_RES_CACHE'];
            foreach($kefuRes as $key =>$vol)
            {
                if($kefuId == $vol['id']);
                {
                    return $vol;
                }
            }
        }
        $userKefuInfo = UserKefu::findOne($kefuId);
        return $userKefuInfo->toArray();
    }
}
