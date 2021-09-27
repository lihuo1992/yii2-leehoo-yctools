<?php

namespace leehooyctools\user;

use leehooyctools\config\Connection;
use leehooyctools\DebugLog;
use leehooyctools\GlobalParams;
use leehooyctools\models\ActionAuthUser\UserAuthAction;
use leehooyctools\models\EyUser\EyUser;
use leehooyctools\models\EyUser\UserMain;
use leehooyctools\models\EyUser\UserWeixinExtra;
use leehooyctools\models\EyUser\WeixinUserWaitAuth;
use leehooyctools\ResponseLog;
use leehooyctools\models\ActionAuthUser\UserAuthIndex;
use leehooyctools\StatusReturn;
use yii\base\BaseObject;
use Yii;

class UserAuth
{
    /**
     * Notes:给用户授权功能
     * Author: LeeHoo
     * @param $userId
     * @param $authCode
     * @param $expireTime
     * @param $remarks
     * @return array|StatusReturn
     * Create Time: 2021/9/26 6:03 下午
     */
    public static function createUserAuth($userId,$authCode,$expireTime,$remarks){
        $statusReturn = new StatusReturn();
        $userAuthIndex = UserAuthIndex::findOne(['userId'=>$userId,'authCode'=>$authCode]);

        if(!empty($userAuthIndex))
        {
            $statusReturn->code=2;
            $statusReturn->msg='already exist';
            $statusReturn->setSuc();
        }
        else
        {
            $userAuthIndex =new UserAuthIndex();
            $userAuthIndex->createTime = time();
            $userAuthIndex->authCode = $authCode;
            $userAuthIndex->userId = $userId;
            $userAuthIndex->expireTime = $expireTime;
            $userAuthIndex->remarks = $remarks;
            $info = $userAuthIndex->save();
            if(!$info)
            {
                ResponseLog::saveDebug('saveUserAuthIndexError',$userAuthIndex->getErrors());
            }
            else
            {
                $user = UserMain::findOne($userId);
                if(!empty($user))
                {
                    $username = $user->username;
                    UserAuth::updateUserInfoInRedis($userId,$username);
                    $statusReturn->msg ='ok';
                    $statusReturn->setSuc();
                }
                else
                {
                    $statusReturn->msg='用户不存在：'.$userId;
                }

            }
        }
        $statusReturn->data=$userAuthIndex;
        return $statusReturn->output();
    }


    /**
     * Notes: openid给用户授权
     * Author: LeeHoo
     * @param $openid
     * @param $authCode
     * @param $expireTime
     * @param $remarks
     * @return array|StatusReturn
     * Create Time: 2021/9/27 11:18 上午
     */
    public static function createUserAuthByOpenid($openid,$authCode,$expireTime,$remarks){
        $statusReturn = new StatusReturn();
        $where = array();
        $where[]='and';
        $where[]=['wechat'=>$openid];
        $eyUser = EyUser::find()->select('userId')->where($where)->orderBy('id desc')->asArray()->all();
        if(!empty($eyUser))
        {
            $userId = $eyUser[0]['userId'];
            return self::createUserAuth($userId,$authCode,$expireTime,$remarks);
        }
        else
        {
            $userAuthRes = [$authCode,$expireTime,$remarks];

            $userAuthResMd5 = [$authCode];
            $md5Id = md5(json_encode([$userAuthResMd5,$openid]));
            $weixinUserWaitAuth = WeixinUserWaitAuth::findOne(['mdId'=>$md5Id]);
            if(!empty($weixinUserWaitAuth))
            {
                $statusReturn->msg='权限已写入，等待用户注册并使用选品';
            }
            else
            {
                $weixinUserWaitAuth = new WeixinUserWaitAuth();
                $weixinUserWaitAuth->openid=$openid;
                $weixinUserWaitAuth->createTime=time();
                $weixinUserWaitAuth->mdId=$md5Id;
                $weixinUserWaitAuth->state = 0;
                $weixinUserWaitAuth->userAuthRes=json_encode($userAuthRes);
                $weixinUserWaitAuth->save();
            }
            $statusReturn->setSuc();
            $statusReturn->msg='权限已写入，等待用户注册并使用选品';

        }
        return $statusReturn->output();
    }

    public static function updateUserInfoInRedis($userId,$username)
    {
        $authRouteRes = array();
        $authCodeRes = array();
        //查询权限：
        $where = array();
        $where[]='and';
        $where[]=['userId'=>$userId];
        $userAuthIndex = UserAuthIndex::find()->where($where)->asArray()->all();
        DebugLog::saveInfo('userAuthIndex',$userAuthIndex);
        if(!empty($userAuthIndex))
        {
            $authCodeRes= array_column($userAuthIndex,'authCode');
//        ActionAuthUser

            $where =array();
            $where[]='and';
            $where[]=['in','authCode',$authCodeRes];
            $userAuthActionRes = UserAuthAction::find()->select('*')->where($where)->asArray()->all();

            if(!empty($userAuthActionRes))
            {
                $authRouteRes = array_column($userAuthActionRes,'actionRoute');
            }

        }

//        $redisConnectName = Connection::REDIS_NAME;
        $redis = Connection::UserRedis();
        $redis->select(1);
        $redisValue = $redis->get('UVT:'.$username);
        if(!empty($redisValue))
        {
            $redisJson = json_decode($redisValue,true);
            if(isset($redisJson['expireTimestamp']))
            {
                $expireTimestamp = $redisJson['expireTimestamp'];
                $expireTime = $expireTimestamp-time();
            }
            else
            {
                $expireTime = 2*60*60;
                $redisJson['expireTimestamp']=time()+$expireTime;
            }

            $wxOpenId =  null;
            if(empty($wxOpenId))
            {
                $eyUser = EyUser::findOne(['userId'=>$userId]);
                $wxOpenId = $eyUser->wechat;
            }
            $kefuInfo = null;
            if(!empty($wxOpenId))
            {
                $userWeixinExtra = UserWeixinExtra::findOne(['openid'=>$wxOpenId]);
                if(!empty($userWeixinExtra))
                {
                    $kefuId = $userWeixinExtra->kefuId;
                    $kefuInfo = GlobalParams::getKefuInfoById($kefuId);
                }
                else
                {
                    $kefuInfo = GlobalParams::getRandKefuInfo();
                    $kefuId = $kefuInfo['id'];
                    $kefuQrCodeImgUrl = $kefuInfo['qrCode'];
                    $userWeixinExtra = new UserWeixinExtra();
                    $userWeixinExtra->createTime=time();
                    $userWeixinExtra->openid=$wxOpenId;
                    $userWeixinExtra->kefuId=$kefuId;
                    $userWeixinExtra->save();
                }
            }
            else
            {
                $kefuInfo = GlobalParams::getRandKefuInfo();
            }
            $redisJson['kefuInfo']=$kefuInfo;
            $redisJson['authRouteRes'] = $authRouteRes;
            $redisJson['authCodeRes'] = $authCodeRes;
            DebugLog::saveInfo('redisInUVT',$redisJson);
            $redis = Connection::UserRedis();
            $redis->select(1);
            $redis->setex('UVT:'.$username,$expireTime,json_encode($redisJson));

        }
    }
}
