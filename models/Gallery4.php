<?php

namespace zantknight\yii\gallery\models;

use Yii;

/**
 * This is the model class for table "gallery_4".
 *
 * @property int $id
 * @property string|null $model
 * @property string|null $owner_id
 * @property string|null $name
 * @property string|null $file
 * @property string|null $title
 * @property string|null $type
 * @property string|null $ext
 * @property string|null $description
 */
class Gallery4 extends \yii\db\ActiveRecord
{
    public $fileInput;
    public $destinationPath = "media";
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gallery_4';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'owner_id'], 'string'],
            [['model', 'title', 'name'], 'string', 'max' => 100],
            [['file'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
            [['ext'], 'string', 'max' => 10],
            [['fileInput'], 'file', 'skipOnEmpty' => true],
        ];
    }

    public function generateName($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomString = ''; 
    
        for ($i = 0; $i < $n; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        } 
    
        return $randomString; 
    }

    public function upload()
    {
        if ($this->validate() && $this->save()) {
            $this->fileInput->saveAs("$this->destinationPath/". 
                $this->name . '.' . $this->fileInput->extension);
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model' => Yii::t('app', 'Model'),
            'owner_id' => Yii::t('app', 'Owner ID'),
            'name' => Yii::t('app', 'Name'),
            'file' => Yii::t('app', 'File'),
            'title' => Yii::t('app', 'Title'),
            'type' => Yii::t('app', 'Type'),
            'ext' => Yii::t('app', 'Ext'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
}
