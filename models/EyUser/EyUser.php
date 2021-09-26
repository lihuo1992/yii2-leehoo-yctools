<?php

namespace leehooyctools\models\EyUser;

use leehooyctools\config\Connection;
use Yii;

/**
 * This is the model class for table "ey_user".
 *
 * @property int $id
 * @property int|null $userId
 * @property int|null $accountId
 * @property string|null $account
 * @property string|null $name
 * @property string|null $avatar
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $sex
 * @property string|null $wechat
 * @property string|null $lastLoginTime
 * @property string|null $createTime
 * @property int|null $loginNum
 * @property int|null $memberType
 * @property int|null $updateTimestamp
 * @property int|null $createTimestamp
 */
class EyUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ey_user';
    }
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
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
            [['userId', 'accountId', 'loginNum', 'memberType', 'updateTimestamp', 'createTimestamp'], 'integer'],
            [['lastLoginTime', 'createTime'], 'safe'],
            [['account'], 'string', 'max' => 55],
            [['name', 'avatar', 'email', 'wechat'], 'string', 'max' => 255],
            [['sex'], 'string', 'max' => 8],
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
            'accountId' => 'Account ID',
            'account' => 'Account',
            'name' => 'Name',
            'avatar' => 'Avatar',
            'email' => 'Email',
            'phone' => 'Phone',
            'sex' => 'Sex',
            'wechat' => 'Wechat',
            'lastLoginTime' => 'Last Login Time',
            'createTime' => 'Create Time',
            'loginNum' => 'Login Num',
            'memberType' => 'Member Type',
            'updateTimestamp' => 'Update Timestamp',
            'createTimestamp' => 'Create Timestamp',
        ];
    }
}
