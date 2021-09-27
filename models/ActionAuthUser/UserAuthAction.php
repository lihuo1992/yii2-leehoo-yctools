<?php

namespace leehooyctools\models\ActionAuthUser;

use leehooyctools\config\Connection;
use Yii;

/**
 * This is the model class for table "user_auth_action".
 *
 * @property int $id 自增ID
 * @property string $actionRoute action路由
 * @property int $createTime 创建时间
 * @property int|null $updateTime 更新时间
 * @property string|null $desc 描述
 * @property string $authCode 表user_auth_item . authCode
 */
class UserAuthAction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_auth_action';
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
            [['actionRoute', 'createTime', 'authCode'], 'required'],
            [['createTime', 'updateTime'], 'integer'],
            [['actionRoute', 'desc'], 'string', 'max' => 255],
            [['authCode'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'actionRoute' => 'Action Route',
            'createTime' => 'Create Time',
            'updateTime' => 'Update Time',
            'desc' => 'Desc',
            'authCode' => 'Auth Code',
        ];
    }
}
