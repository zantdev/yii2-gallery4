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


        if ($this->config === null) {
            $name = $this->path;
            $this->config = [
                'name' => $name.'[]',
                'options'=>[
                    'multiple'=>true
                ],
                'pluginOptions' => [
                    'uploadUrl' => Url::to(["gallery4/api/upload"]),
                    'maxFileCount' => 10
                ]
            ];
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
}
?>