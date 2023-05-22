<?php

namespace zantknight\yii\gallery\models;


use Yii;
use yii\helpers\Url;
use yii\helpers\FileHelper;
use zantknight\yii\gallery\models\GalleryOwner;
use yii\web\UploadedFile;
use yii\web\UploadedFile;

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

    public static function getImagesUrl($objectPk, $modelClass) {
        $list = [];
        $galleryOwners = GalleryOwner::find()->joinwith(['gallery'])->where([
            'owner_id' => $objectPk,
            'model' => $modelClass
        ])->all();
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https' : 'http';
        $absoluteHomeUrl = Url::base(true, $protocol);

        foreach ($galleryOwners as $item) {
            $list[] = $absoluteHomeUrl."/media/".$item->gallery->title;
        }

        return $list;
    }

    public static function deleteAllImageByObjPk($objectPk, $modelClass) {
        $galleryOwner = GalleryOwner::find()->where([
            'owner_id' => $objectPk,
            'model' => $modelClass
        ])->all();
        $isDeleted = false;
        $isGODeleted = true;
        $galleryId = null;
        foreach ($galleryOwner as $item) {
            $galleryId = $item->gallery_id;
            $isGODeleted = $isGODeleted &&$item->delete();
        }
        if ($isGODeleted && $galleryId) {
            $gallery = Gallery4::findOne($galleryId);
            $filePath = Yii::$app->basePath."/web/media/".
                    $gallery->name.".".$gallery->ext;
            if (FileHelper::unlink($filePath)) {
                if ($gallery->delete()) {
                    $isDeleted = true;
                }
            }
        }

        return $isDeleted;
    }
    
    public static function addImageFromBase64($objectPk, $modelClass, $image64, $imgExt, $imgType, $category = null) {
        $gallery = new Gallery4();
        $fileName = $gallery->generateName(32);

        $gallery->name = $fileName;
        $decodedImage = base64_decode($image64);
        $fileUrl = Yii::$app->basePath."/web/media/$fileName.$imgExt";

        $myfile = fopen($fileUrl, "w");
        if ($myfile) {
            fwrite($myfile, $decodedImage);
            $fileSize = filesize($fileUrl);
            $gallery->file_size = $fileSize;
            $gallery->title = $fileName.".".$imgExt;
            $gallery->type = $imgType;
            $gallery->ext = $imgExt;
            if ($category == null) {
                $gallery->category = "GALLERY4";
            }else {
                $gallery->category = $category;
            }
            $gallery->created_at = date('Y-m-d H:i:s');
            if ($gallery->save()) {
                $galleryOwner = new GalleryOwner();
                $galleryOwner->gallery_id = $gallery->primaryKey;
                $galleryOwner->owner_id = strval($objectPk);
                $galleryOwner->model = $modelClass;
                $galleryOwner->created_at = date('Y-m-d H:i:s');
                if ($galleryOwner->save()) {
                    $out['data']['id'] = $objectPk;
                    $out['data']['url'] = 
                        Url::to('@web/media/'.$gallery->title, true);
                }else {
                    $out['success'] = false;
                    $out['data'] = $galleryOwner->errors;
                }
            }
            fclose($myfile);
        }
    }

    public static function changeSingleImageFromBase64($objectPk, $modelClass, $image64, $imgExt, $imgType, $category = null) {
        $go = GalleryOwner::find()->where([
            'model' => $modelClass,
            'owner_id' => $objectPk
        ])->one();
        if ($go) {
            $gallery = Gallery4::findOne($go->gallery_id);
            if ($gallery) {
                if ($gallery->category == $category) {
                    $fileUrl = Yii::$app->basePath."/web/media/".$gallery->name.".".$gallery->ext;
                    unlink($fileUrl);
                    if ($go->delete()) {
                        if ($gallery->delete()) {
                            $gallery = new Gallery4();
                            $fileName = $gallery->generateName(32);

                            $gallery->name = $fileName;
                            $decodedImage = base64_decode($image64);
                            $fileUrl = Yii::$app->basePath."/web/media/$fileName.$imgExt";

                            $myfile = fopen($fileUrl, "w");
                            if ($myfile) {
                                fwrite($myfile, $decodedImage);
                                $fileSize = filesize($fileUrl);
                                $gallery->file_size = $fileSize;
                                $gallery->title = $fileName.".".$imgExt;
                                $gallery->type = $imgType;
                                $gallery->ext = $imgExt;
                                if ($category == null) {
                                    $gallery->category = "GALLERY4";
                                }else {
                                    $gallery->category = $category;
                                }
                                $gallery->created_at = date('Y-m-d H:i:s');
                                if ($gallery->save()) {
                                    $galleryOwner = new GalleryOwner();
                                    $galleryOwner->gallery_id = $gallery->primaryKey;
                                    $galleryOwner->owner_id = strval($objectPk);
                                    $galleryOwner->model = $modelClass;
                                    $galleryOwner->created_at = date('Y-m-d H:i:s');
                                    if ($galleryOwner->save()) {
                                        $out['data']['id'] = $objectPk;
                                        $out['data']['url'] = 
                                            Url::to('@web/media/'.$gallery->title, true);
                                    }else {
                                        $out['success'] = false;
                                        $out['data'] = $galleryOwner->errors;
                                    }
                                }
                                fclose($myfile);
                            }
                        }                        
                    }
                }
            }
        }
    }

    public static function addImageFromAjax($objectPk, $modelClass, $category) {
        $galleryOwner = GalleryOwner::find()->where([
            'owner_id' => $objectPk,
            'model' => $modelClass,
            'category' => $category
        ])->one();
        if ($galleryOwner) {
            $gallery = Gallery4::findOne($galleryOwner->gallery_id);
            $fileName = $gallery->name;
        }else {
            $gallery = new Gallery4();
            $fileName = $gallery->generateName(32);
        }

        $gallery->name = $fileName;
        $gallery->category = $category;
        $gallery->fileInput = UploadedFile::getInstance(
            $gallery,
            'fileInput'
        );
        $gallery->title = $gallery->fileInput->name;
        $gallery->created_at = date('Y-m-d H:i:s');
        $gallery->file_size = $gallery->fileInput->size;
        if ($gallery->upload() && $gallery->validate() && $gallery->save()) {
            if ($galleryOwner == null) {
                $galleryOwner = new GalleryOwner();
            }
            $galleryOwner->gallery_id = $gallery->id;
            $galleryOwner->model = $modelClass;
            $galleryOwner->created_at = date('Y-m-d H:i:s');
            $galleryOwner->category = $category;
            $galleryOwner->save();
            $fileUrl = Url::to("@web/media/$gallery->name.$gallery->ext", true);
            return [
                'key' => $gallery->id,
                'caption' => $gallery->name,
                'title' => $gallery->title,
                'size' => $gallery->fileInput->size,
                'downloadUrl' => $fileUrl,
            ];
        }else {
            echo '<pre>';
            print_r($gallery);
            die;
            return [
                'success' => false,
                'message' => 'Upload data is fail'
            ];
        }

        return [
            'success' => false,
            'message' => 'Non image post is declined!'
        ];
    }

    public static function setImageFromBase64($objectPk, $modelClass, $image64, $imgExt, $imgType, $category = null) {
        $galleryOwner = GalleryOwner::find()->where([
            'owner_id' => $objectPk,
            'model' => $modelClass
            ])->one();
        if ($galleryOwner) {
            $gallery = Gallery4::findOne($galleryOwner->gallery_id);
            $fileName = $gallery->name;
        }else {
            $gallery = new Gallery4();
            $fileName = $gallery->generateName(32);
        }

        $gallery->name = $fileName;
        $decodedImage = base64_decode($image64);
        $fileUrl = Yii::$app->basePath."/web/media/$fileName.$imgExt";

        $myfile = fopen($fileUrl, "w");
        if ($myfile) {
            fwrite($myfile, $decodedImage);
            $fileSize = filesize($fileUrl);
            $gallery->file_size = $fileSize;
            $gallery->title = $fileName.".".$imgExt;
            $gallery->type = $imgType;
            $gallery->ext = $imgExt;
            if ($category == null) {
                $gallery->category = "GALLERY4";
            }else {
                $gallery->category = $category;
            }
            if ($category == null) {
                $gallery->category = "GALLERY4";
            }else {
                $gallery->category = $category;
            }
            $gallery->created_at = date('Y-m-d H:i:s');
            if ($gallery->save()) {
                if (!$galleryOwner) {
                    $galleryOwner = new GalleryOwner();
                }
                $galleryOwner->gallery_id = $gallery->primaryKey;
                $galleryOwner->owner_id = strval($objectPk);
                $galleryOwner->model = $modelClass;
                $galleryOwner->created_at = date('Y-m-d H:i:s');
                if ($galleryOwner->save()) {
                    $out['data']['id'] = $objectPk;
                    $out['data']['url'] = 
                        Url::to('@web/media/'.$gallery->title, true);
                }else {
                    $out['success'] = false;
                    $out['data'] = $galleryOwner->errors;
                }
            }
            fclose($myfile);
        }
    }

    public static function getProfileImagePath($objectPk = null, $modelClass = null) {
        $path = null;
        if ($objectPk) {
            $galleryOwner = GalleryOwner::find()->where([
                'owner_id' => $objectPk,
                'model' => $modelClass
                ])->one();
        }else {
            $galleryOwner = GalleryOwner::find()->where([
                'model' => $modelClass
                ])->one();
        }

        if ($galleryOwner) {
            $gallery = Gallery4::findOne($galleryOwner->gallery_id);
            $fileName = $gallery->name.".".$gallery->ext;
            $path = Url::to('@web/media/'.$fileName, true); 
        }

        return $path;
    }
    
    public static function getImagesPath($objectPk = null, $modelClass = null, $category = null, $isHttps = false) {
        $imageList = [];
        if ($objectPk) {
            $galleriesOwner = GalleryOwner::find()->where([
                'owner_id' => $objectPk,
                'model' => $modelClass
                ])->all();
        }else {
            $galleriesOwner = GalleryOwner::find()->where([
                'model' => $modelClass
                ])->all();
        }
        
        foreach ($galleriesOwner as $item) {
            if ($category) {
                $gallery = Gallery4::find()->where([
                    'category' => $category, 'id' => $item->gallery_id])->one();
                
            }else {
                $gallery = Gallery4::find()->where([
                    'id' => $item->gallery_id])->one();
            }

            if ($gallery) {
                $fileName = $gallery->name.".".$gallery->ext;
                $path = Url::to('@web/media/'.$fileName, false);
                if ($isHttps) {
                    $path = "https://".$_SERVER['HTTP_HOST'].$path;
                }
                // if (isset($_SERVER['HTTPS']) &&
                //     ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
                //     isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                //     $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                //     $protocol = 'https://';
                // }
                // else {
                //     $protocol = 'http://';
                // }
                // $path = $protocol.$path;
                // echo $protocol;
                // die;
                $imageList[] = $path;
            }
        }

        return $imageList;
    }

    public function getGo() {
        return $this->hasMany(GalleryOwner::className(), ['gallery_id' => 'id']);
    }
}
