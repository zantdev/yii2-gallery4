<?php
namespace zantknight\yii\gallery; 

use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use zantknight\yii\gallery\models\Gallery4;

class Gallery4Widget extends Widget {
    public $config;
    public $model;
    public $ownerModel;
    public $fieldName;
    public $path;

    public function init() {
        parent::init();

        if ($this->ownerModel === null) {
            $this->path = 'media';
        }else {
            $this->path = strtolower(StringHelper::basename(
                $this->ownerModel::className()
            ));
        }
        if (!$this->ownerModel->isNewRecord) {
            $this->config['pluginOptions']['initialPreview'] = 
                $this->getInitialPreview();
        }

        $this->config['pluginOptions']['uploadUrl'] = Url::to([
            "gallery4/api/upload"
        ]);
        $this->config['pluginOptions']['uploadAsync'] = false;
        $this->config['pluginOptions']['uploadExtraData']['model'] = 
            StringHelper::basename($this->ownerModel::className());

        $this->config = array_merge([
            'model' => new Gallery4(),
            'attribute' => 'fileInput',
        ], $this->config);
    }

    /**
     * Bypassing kartik file input's config and
     * other active record
     */
    public function run() {
        return $this->render('myWidget', [
            'ownerModel' => $this->ownerModel,
            'config' => $this->config,
            'fieldName' => $this->fieldName
        ]);
    }

    private function getInitialPreview() {
        $gallery = Gallery4::find()->where([
            'model' => StringHelper::basename($this->ownerModel::className()),
            'owner_id' => $this->ownerModel->id
        ])->one();

        if ($gallery) {
            return [
                "<img class='w-100' src='".
                    Url::to("@web/media/$gallery->name.$gallery->ext", true)
                ."' />"
            ];
        }else {
            return [];
        }
    }
}
?>