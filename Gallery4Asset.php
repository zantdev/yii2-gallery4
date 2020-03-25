<?php 
namespace zantknight\yii\gallery;

use yii\web\AssetBundle;

class Gallery4Asset extends AssetBundle 
{
    public $sourcePath = __DIR__."/assets/dist";
    public $js = [
        'js/lightbox.min.js'
    ];
    
    public $css = [
        'https://unpkg.com/@coreui/icons@1.0.0/css/all.min.css',
        'css/lightbox.min.css',
        'css/main.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}

?>
