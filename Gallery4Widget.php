<?php
namespace zantknight\yii\gallery; 

use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use zantknight\yii\gallery\models\Gallery4;
use zantknight\yii\gallery\models\GalleryOwner;

class Gallery4Widget extends Widget {
    public $config;
    public $model;
    public $ownerModel;
    public $path;
    public $multiple;

    public function init() {
        parent::init();

        if ($this->ownerModel === null) {
            $this->path = 'media';
        }else {
            $this->path = strtolower(StringHelper::basename(
                $this->ownerModel::className()
            ));
        }
        

        $this->config = [
            'options' => [
                'accept' => 'image/*',
            ],
            'pluginOptions' => [
                'uploadUrl' => Url::to(["gallery4/api/upload"]),
                'uploadAsync' => false,
                'maxFileCount' => 1,
                'showCancel' => false,
                'showRemove' => false,
                'uploadExtraData' => [
                    'model' => StringHelper::basename(
                        $this->ownerModel::className()
                    )
                ]
            ],
            'model' => new Gallery4(),
            'attribute' => 'fileInput'
        ];

        if (!$this->ownerModel->isNewRecord) {
            $this->config['pluginOptions']['preview'] = 
                $this->getPreview();
        }

        if (!$this->multiple) {
            $this->multiple = 0;
        }else {
            $this->multiple = 1;
        }
    }

    /**
     * Bypassing kartik file input's config and
     * other active record
     */
    public function run() {
        return $this->render('myWidget', [
            'ownerModel' => $this->ownerModel,
            'config' => $this->config,
            'multiple' => $this->multiple
        ]);
    }

    private function getPreview() {
        $ret = [];
        $strGalId = "";
        $galleryOwner = GalleryOwner::find()->where([
            'model' => StringHelper::basename($this->ownerModel::className()),
            'owner_id' => $this->ownerModel->id
        ])->all();

        if ($galleryOwner) {
            foreach ($galleryOwner as $go) {
                $gallery = Gallery4::findOne($go->gallery_id);
                if ($gallery) {
                    $fileUrl = Url::to(
                        "@web/media/$gallery->name.$gallery->ext", 
                        true
                    );
                    $ret['data'][] = [
                        'id' => $gallery->id,
                        'model' => $go->model,
                        'title' => $gallery->title,
                        'file_size' => $gallery->file_size,
                        'url' => $fileUrl
                    ];
                    $strGalId = $strGalId.":$gallery->id";
                }
            }
        }

        $ret['str_gal_id'] = $strGalId;
        
        return $ret;
    }
}
?>