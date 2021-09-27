<?php
namespace leehooyctools\config;
use Yii;
class Connection
{

    //配置用户Mysql表连接
    const USER_DB_CONNENCT = [
        'dsn' => 'mysql:host=127.0.0.1:9963;dbname=webapp',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
    
    //配置用户Redis连接
    const USER_REDIS_CONNENCT = [
        'hostname'=>'127.0.0.1',
        'port'=>6379,
        'database'=>0
    ];

    public  static  function UserDb(){
        if(isset(Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_DB_CONNECTION']) && !empty(Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_DB_CONNECTION']))
        {
            return Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_DB_CONNECTION'];
        }
        else
        {
            $conntection  =new \yii\db\Connection(self::USER_DB_CONNENCT);
            Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_DB_CONNECTION'] = $conntection;
            return Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_DB_CONNECTION'];
        }
    }

    public  static  function UserRedis(){
        if(isset(Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_REDIS_CONNECTION']) && !empty(Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_REDIS_CONNECTION']))
        {
            return Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_REDIS_CONNECTION'];
        }
        else
        {
            $conntection  =new \yii\redis\Connection(self::USER_REDIS_CONNENCT);
            Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_REDIS_CONNECTION'] = $conntection;
            return Yii::$app->params['__VENDOR_LEEHOOTOOLS']['USER_REDIS_CONNECTION'];
        }
    }
}
