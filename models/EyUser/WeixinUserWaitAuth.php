<?php

namespace leehooyctools\models\EyUser;

use leehooyctools\config\Connection;
use Yii;

/**
 * This is the model class for table "weixin_user_wait_auth".
 *
 * @property int $id 自增ID
 * @property string|null $openid 微信公众号openid
 * @property int|null $createTime 创建时间
 * @property int|null $updateTime 更新（回写时间）
 * @property int|null $userId 用户ID
 * @property int|null $state 用户ID
 * @property string|null $mdId 避免重复ID = md5(openid+userAuthRes)
 * @property string|null $userAuthRes 用户权限集合
 */
class WeixinUserWaitAuth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'weixin_user_wait_auth';
    }
    public static function getDb()
    {
        return Connection::UserDb();
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['createTime', 'updateTime', 'userId'], 'integer'],
            [['openid'], 'string', 'max' => 32],
            [['mdId'], 'string', 'max' => 55],
            [['userAuthRes'], 'string', 'max' => 512],
            [['mdId'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => 'Openid',
            'createTime' => 'Create Time',
            'updateTime' => 'Update Time',
            'userId' => 'User ID',
            'mdId' => 'Md ID',
            'userAuthRes' => 'User Auth Res',
        ];
    }
}
