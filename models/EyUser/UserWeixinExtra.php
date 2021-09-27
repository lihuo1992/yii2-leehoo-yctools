<?php

namespace leehooyctools\models\EyUser;

use leehooyctools\config\Connection;
use Yii;

/**
 * This is the model class for table "user_weixin_extra".
 *
 * @property int $id
 * @property string|null $openid
 * @property int|null $kefuId
 * @property int|null $createTime
 */
class UserWeixinExtra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_weixin_extra';
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
            [['kefuId', 'createTime'], 'integer'],
            [['openid'], 'string', 'max' => 55],
            [['openid'], 'unique'],
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
            'kefuId' => 'Kefu ID',
            'createTime' => 'Create Time',
        ];
    }
}
