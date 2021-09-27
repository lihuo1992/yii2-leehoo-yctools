易芽开发项目必要工具
==========
响应日志+Debug+接口返回json+操作文件+其他常用

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist leehootools/yii2-leehoo-yctools "*"
```

or add

```
"leehootools/yii2-leehoo-yctools": "*"
```

to the require section of your `composer.json` file.

应用
-----

##### 响应日志使用ResponseLog举例:

```php
<?= 
'data' => [
            'class' => 'app\modules\data\Module',
            'on beforeAction' => function (yii\base\ActionEvent $event) {
                \leehooyctools\ResponseLog::initClass();
            },
            'on afterAction' => function ($event) {
                \leehooyctools\ResponseLog::getClass()->saveIn();
            }
        ]?> 
```
#####  用户授权功能
- 首先配置Mysql用户表和Redis链接名：
```php
namespace leehooyctools\config;

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
}
```
- 调用函数
```php
\leehooyctools\user\UserAuth::createUserAuthByOpenid('o-QfV6Byn1KWCsxA-q0uXqN6VSKs','walPluginAdv',1670000000,'微信识别朋友圈转发，自动授权');
```
