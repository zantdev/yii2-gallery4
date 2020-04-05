<?php

namespace zantknight\yii\gallery\models;


use Yii;
use zantknight\yii\gallery\models\GalleryOwner;

/**
 * This is the model class for table "gallery_4".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $file_size
 * @property string|null $title
 * @property string|null $type
 * @property string|null $category
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
            [['description'], 'string'],
            [['title', 'name'], 'string', 'max' => 100],
            [['file_size'], 'integer'],
            [['type'], 'string', 'max' => 50],
            [['ext'], 'string', 'max' => 10],
            [['category'], 'string', 'max' => 20],
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
            'name' => Yii::t('app', 'Name'),
            'file_size' => Yii::t('app', 'File Size'),
            'title' => Yii::t('app', 'Title'),
            'type' => Yii::t('app', 'Type'),
            'category' => Yii::t('app', 'Category'),
            'ext' => Yii::t('app', 'Ext'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    public function getGo() {
        return $this->hasMany(GalleryOwner::className(), ['gallery_id' => 'id']);
    }
}
