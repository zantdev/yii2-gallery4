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
        'ownerModel' => $model,
        'multiple' => true
    ]); ?>
   ```
   **Description**
   - ownerModel
     Model class where is used by view 
   - multiple
     Multiple upload status
5. Put this chunk in params.php
   ```php
    return [
        ...
        'bsVersion' => '4.x',
    ];
   ```
6. You will get something like this
   ![alt text](https://i.postimg.cc/rmLKSSH9/1.png)
   ![alt text](https://i.postimg.cc/G3DnWWH9/2.png)
   ![alt text](https://i.postimg.cc/pXYbTKQH/3.png)
   ![alt text](https://i.postimg.cc/9QJ6PVNg/4.png)
7. For image administration, go to Module Gallery4 Url (/gallery4/admin) and you will get something like this
   ![alt text](https://i.postimg.cc/Yqdy4RPQ/5.png)