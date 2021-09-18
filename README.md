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


Usage
-----

- 响应日志使用ResponseLog举例:

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
        ], ?>```
