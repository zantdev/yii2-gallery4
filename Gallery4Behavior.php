<?php 
namespace zantknight\yii\gallery; 

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

use zantknight\yii\gallery\models\Gallery4;

class Gallery4Behavior extends Behavior 
{   
    public $modelName = null;
    public $model = null;
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
        ];
    }

    public function afterInsert($event) 
    {
        $gallery4Ids = Yii::$app->request->post('gallery4Id');
        if ($gallery4Ids) {
            $galleryKeys = explode(":", $gallery4Ids);
            foreach($galleryKeys as $keyValue) {
                if ($keyValue != "") {
                    $gallery = Gallery4::findOne($keyValue);
                    $gallery->owner_id = $this->model->id;
                    $gallery->save();
                }
            }
        }
        
    }
}
?>