<?php

namespace leehooyctools\models;


/**
 * This is the model class for table "exception_error_log".
 *
 * @property integer $id
 * @property integer $create_time
 * @property string $content
 * @property string $remark
 * @property string $code
 * @property string $module
 * @property string $controller
 * @property string $action
 * @property string $username
 * @property string $host
 * @property string $ip
 * @property integer $status_code
 * @property integer $user_id
 * @property integer $encode_type
 */
class ExceptionErrorLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exception_error_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'status_code'], 'integer'],
            [['content'], 'string'],
            [['remark'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 55],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_time' => 'Create Time',
            'content' => 'Content',
            'remark' => 'Remark',
            'code' => 'Code',
            'status_code' => 'Status Code',
        ];
    }
}
