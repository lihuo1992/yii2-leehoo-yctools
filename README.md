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
    const USER_DB_NAME = 'db';
    const REDIS_NAME = 'redis';
}
```
- 调用函数
```php
\leehooyctools\user\UserAuth::createUserAuth(1111,'walPluginAdv',1670000000,'微信识别朋友圈转发，自动授权');
```
