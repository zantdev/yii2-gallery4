<?php 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

use zantknight\yii\gallery\Gallery4Asset;

$bundle = Gallery4Asset::register($this);

?>
<div class="file-list-container">
  <div class="header">
    <h3>File</h3>
  </div>
  <div class="content" id="content-gallery4">
    <?php 
      if (isset($config['pluginOptions']['preview']['data'])) {
        if (sizeof($config['pluginOptions']['preview']['data']) > 0) {
          foreach ($config['pluginOptions']['preview']['data'] as $preview) {
            ?>
            <div class="item item-<?= $preview['id']?>" data-id="<?= $preview['id']?>">
              <a data-lightbox="image-1" data-title="<?= $preview['title'] ?>" href="<?= $preview['url'] ?>">
                <img class="preview" src="<?= $preview['url'] ?>" />
              </a>
              <div class="body-container">
                <div class="file-name"><?= $preview['title'] ?></div>
                <div class="info-container">
                  <div class="file-size"><?= $preview['file_size'] ?></div>
                  <div class="file-action">
                    <a class="action-wrapper action-download" href="<?= $preview['url'] ?>">
                      <i class="c-icon cil-cloud-download"></i>
                    </a>
                    <a class="action-wrapper action-delete" data-model="<?= $preview['model'] ?>" 
                      data-galId="<?= $preview['id']?>" href="#">
                      <i class="c-icon c-icon cil-trash"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
        }else {
          ?>
          <div class="no-file-wrapper">
            <div class="no-file">
              <img class="image-icon" src="<?= $bundle->baseUrl ?>/images/secret-file.png" />
              <div class="caption">No File Selected</div>
            </div>
          </div> 
          <?php
        }
      }else {
        ?>
        <div class="no-file-wrapper">
          <div class="no-file">
            <img class="image-icon" src="<?= $bundle->baseUrl ?>/images/secret-file.png" />
            <div class="caption">No File Selected</div>
          </div>
        </div>
        <?php
      }
    ?>
  </div>
  <?php 
  if (isset($config['pluginOptions']['preview']['str_gal_id'])) {
    $strGalId = $config['pluginOptions']['preview']['str_gal_id'];
    echo Html::hiddenInput('gallery4Id', $strGalId, [
      'id' => 'gallery4Id'
    ]);
  }else {
    echo Html::hiddenInput('gallery4Id', null, [
      'id' => 'gallery4Id'
    ]);
  }

  ?>
  <?= Html::hiddenInput('gallery4Multiple', $multiple, [
      'id' => 'gallery4Multiple'
    ]); 
  ?>
  <div class="footer">
    <button id="btn-add-change" data-toggle="modal" 
      data-target="#modalChooserGallery4" type="button" class="btn btn-primary">
      <?php 
      if (isset($config['pluginOptions']['preview']['str_gal_id'])) {
        if ($multiple) {
          echo '<i class="cil-cloud-upload"></i> Add Image';
        }else {
          echo '<i class="cil-swap-vertical"></i> Change Image';
        }
      }else {
        echo '<i class="cil-cloud-upload"></i> Add Image';
      }
      ?>
    </button>
  </div>
</div>

<?= $this->render('imageChooser', [
  'config'=>$config, 
  'ownerModel' => $ownerModel, 
  'multiple' => $multiple,
  'bundle' => $bundle
]); ?>

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
    ",
    View::POS_READY,
    'auto-upload-handler'
);
?>
