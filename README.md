A bootstrap4 gallery manager
============================
Gallery manager for bootstrap4

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist zantknight/yii2-gallery4 "*"
```

or add

```
"zantknight/yii2-gallery4": "*"
```

to the require section of your `composer.json` file.


Usage
-----
1. Migrate to create gallery table  by calling this command
   ```
   php yii migrate --migrationPath=@vendor/zantknight/yii2-gallery4/migrations
   ```
2. Update config/web.php
   ```php
   return [
       ...
       'modules' => [
           'gallery4' => [
                'class' => 'zantknight\yii\gallery\Module',
            ],
       ]
   ]
   ```
3. Add this behavior to your model
   ```php
    ...
    use zantknight\yii\gallery\Gallery4Behavior;

    class YourModel extends \yii\db\ActiveRecord
    {
        ...

        public function behaviors()
        {
            return [
                ...
                [
                    'class' => Gallery4Behavior::className(),
                    'model' => $this
                ]
            ];
        }
    }
   ```
4. Put this onto your view
   ```php
   <?= \zantknight\yii\gallery\Gallery4Widget::widget([
        'config' => [
            'options' => [
                'accept' => 'image/*',
                'uploadAsync'=> true,
                'showUpload'=> false,
                'showCancel'=> false,
                'showRemove'=> false,
                'showClose'=> false,
            ],
        ],
        'ownerModel' => $model,
        'fieldName' => 'flag'
    ]); ?>
   ```
   **Description**
   - config
    Configuration of Kartik yii2-widget-fileinput. You can find [this](https://github.com/kartik-v/yii2-widget-fileinput) for more option
   - ownerModel
     Model class where is used by view 
   - fieldName
     field to store gallery image id
5. Viola