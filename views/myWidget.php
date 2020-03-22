<?php 
use yii\web\View;

use zantknight\yii\gallery\Gallery4Asset;

$bundle = Gallery4Asset::register($this);

?>
<div class="file-list-container">
    <div class="header">
        <h3>File</h3>
    </div>
    <div class="content">
        <div class="item">
            <a data-lightbox="image-1" data-title="Italy" href="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png">
                <img class="preview" src="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png" />
            </a>
            <div class="file-name">Italy</div>
            <div class="info-container">
                <div class="file-size">220 Kb</div>
                <div class="file-action">
                    <a class="action-wrapper action-download" href="#">
                        <i class="c-icon cil-cloud-download"></i>
                    </a>
                    <a class="action-wrapper action-change" href="#">
                        <i class="c-icon cil-swap-vertical"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="item">
            <a data-lightbox="image-1" data-title="Italy" href="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png">
                <img class="preview" src="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png" />
            </a>
            <div class="file-name">Italy</div>
            <div class="info-container">
                <div class="file-size">220 Kb</div>
                <div class="file-action">
                    <a class="action-wrapper action-download" href="#">
                        <i class="c-icon cil-cloud-download"></i>
                    </a>
                    <a class="action-wrapper action-change" href="#">
                        <i class="c-icon cil-swap-vertical"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <button type="button" class="btn btn-primary"><i class="cil-cloud-upload"></i> Image</button>
    </div>
</div>
<div class="file-list-container">
    <div class="header">
        <h3>File</h3>
    </div>
    <div class="content">
        <div class="no-file-wrapper">
            <div class="no-file">
                <img class="image-icon" src="<?= $bundle->baseUrl ?>/images/secret-file.png" />
                <div class="caption">No File Selected</div>
            </div>
        </div>
    </div>
    <div class="footer">
        <button data-toggle="modal" data-target="#modalChooserGallery4" type="button" class="btn btn-primary">
            <i class="cil-cloud-upload"></i> Add Image
        </button>
    </div>
</div>

<?= $this->render('imageChooser', ['config'=>$config, 'ownerModel' => $ownerModel]); ?>

<?php 
$this->registerJs(
    "
    lightbox.option({
        'resizeDuration': 200,
        'fadeDuration': 300,
        'imageFadeDuration': 300,
        'wrapAround': true
    })
    $('#gallery4-fileinput').on('filebatchselected', 
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
