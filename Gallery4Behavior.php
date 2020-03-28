<?php 
namespace zantknight\yii\gallery; 

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;


use zantknight\yii\gallery\models\Gallery4;
use zantknight\yii\gallery\models\GalleryOwner;

class Gallery4Behavior extends Behavior 
{   
    public $model = null;
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'updateOwnerModel',
            ActiveRecord::EVENT_AFTER_UPDATE => 'updateOwnerModel',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDeleteOwnerModel',
        ];
    }

    public function updateOwnerModel($event) 
    {
        $gallery4Ids = Yii::$app->request->post('gallery4Id');
        $gallery4Multiple = Yii::$app->request->post('gallery4Multiple');
        
        if ($gallery4Ids) {
            $galleryKeys = explode(":", $gallery4Ids);
            foreach ($galleryKeys as $keyValue) {
                if ($keyValue != "") {
                    if ($gallery4Multiple) {
                        $galleryOwner = GalleryOwner::find()->where([
                            'gallery_id' => $keyValue
                        ])->one();
                        if ($galleryOwner) {
                            $galleryOwner->owner_id = 
                                strval($this->model->primaryKey);
                            $galleryOwner->save();
                        }else {
                            $galleryOwner = new GalleryOwner();
                            $galleryOwner->gallery_id = intval($keyValue);
                            $galleryOwner->model = StringHelper::basename(
                                $this->model::className()
                            );
                            $galleryOwner->owner_id = 
                                strval($this->model->primaryKey);
                            $galleryOwner->created_at = date('Y-m-d H:i:s');
                            $galleryOwner->save();
                        }
                    }else {
                        $delGalOwner = GalleryOwner::find()->where([
                            'model' => StringHelper::basename(
                                $this->model::className()
                            ),
                            'owner_id' => strval($this->model->primaryKey)
                        ])->one();
                        if ($delGalOwner) {
                            $galleryOwner = new GalleryOwner();
                            $galleryOwner->gallery_id = $keyValue;
                            $galleryOwner->model = StringHelper::basename(
                                $this->model::className()
                            );
                            $galleryOwner->owner_id = 
                                strval($this->model->primaryKey);
                            $galleryOwner->created_at = date('Y-m-d H:i:s');
                            if ($galleryOwner->save()) {
                                $delGalOwner->delete();
                            }
                        }else {
                            $galleryOwner = new GalleryOwner();
                            $galleryOwner->gallery_id = $keyValue;
                            $galleryOwner->model = StringHelper::basename(
                                $this->model::className()
                            );
                            $galleryOwner->owner_id = strval($this->model->primaryKey);
                            $galleryOwner->created_at = date('Y-m-d H:i:s');
                            $galleryOwner->save();
                        }
                    }                    
                }
            }
        }
    }

    public function beforeDeleteOwnerModel($event) 
    {
        $galleries = Gallery4::find()->where([
            'owner_id' => $this->model->primaryKey
        ])->all();
        
        foreach ($galleries as $gallery) {
            $gallery->owner_id = '';
            $gallery->save();
        } 
    }
}
?>