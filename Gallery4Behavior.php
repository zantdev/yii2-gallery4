<?php 
namespace zantknight\yii\gallery; 

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

use zantknight\yii\gallery\models\Gallery4;

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
        if ($gallery4Ids) {
            $galleryKeys = explode(":", $gallery4Ids);
            foreach($galleryKeys as $keyValue) {
                if ($keyValue != "") {
                    $gallery = Gallery4::findOne($keyValue);
                    $gallery->owner_id = strval($this->model->primaryKey);
                    $gallery->save();                    
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