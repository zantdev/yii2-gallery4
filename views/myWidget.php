<?php 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

use kartik\file\FileInput;

?>
<!-- StringHelper::basename($model::className()) -->
<?php if ($ownerModel !== null) { ?>
<label class="control-label">Image</label>
<?= FileInput::widget($config); ?>
<?= Html::hiddenInput('fileModel', 
        StringHelper::basename($ownerModel::className())
    ); ?>
<?= Html::hiddenInput('gallery4Id', null, [
    'id' => 'gallery4Id'
]); ?>
<?php }else { ?>
<label class="control-label">Upload Document</label>
<?= FileInput::widget($config); ?>
<?php }?>

<?php 
$this->registerJs(
    "$('#gallery4-fileinput').on('filebatchselected', 
        function(event) {
            $(this).fileinput('upload');
        }
    );
    $('#gallery4-fileinput').on('filebatchuploadsuccess',
        function(event, data){
            var strGallery = $('#gallery4Id').val();
            var galleryId = data.response.initialPreviewConfig[0].key;
            strGallery = `\${strGallery}:\${galleryId}`;
            $('#gallery4Id').val(strGallery);
        }
    );",
    View::POS_READY,
    'auto-upload-handler'
);
?>