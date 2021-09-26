<?php

namespace leehooyctools\models\ActionAuthUser;

use leehooyctools\config\Connection;
use Yii;

/**
 * This is the model class for table "user_auth_index".
 *
 * @property int $id
 * @property int $userId
 * @property string $authCode
 * @property int|null $expireTime 有效截止时间
 * @property int|null $createTime
 * @property int|null $updateTime
 * @property string|null $remarks 备注信息
 */
class UserAuthIndex extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_auth_index';
    }

    public static function getDb()
    {
        return Yii::$app->get(Connection::USER_DB_NAME);
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'authCode'], 'required'],
            [['userId', 'expireTime', 'createTime', 'updateTime'], 'integer'],
            [['authCode'], 'string', 'max' => 32],
            [['remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'authCode' => 'Auth Code',
            'expireTime' => 'Expire Time',
            'createTime' => 'Create Time',
            'updateTime' => 'Update Time',
            'remarks' => 'Remarks',
        ];
    }
}
