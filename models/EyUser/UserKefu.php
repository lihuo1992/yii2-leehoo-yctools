<?php

namespace leehooyctools\models\EyUser;

use leehooyctools\config\Connection;
use Yii;

/**
 * This is the model class for table "user_kefu".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $qrCode
 * @property int|null $createTime
 * @property int|null $state
 * @property string|null $remarks
 */
class UserKefu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_kefu';
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
            [['createTime', 'state'], 'integer'],
            [['name', 'qrCode', 'remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'qrCode' => 'Qr Code',
            'createTime' => 'Create Time',
            'state' => 'State',
            'remarks' => 'Remarks',
        ];
    }
}
