<?php 
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

use kartik\file\FileInput;
?>
<!-- Modal -->
<div class="modal fade" id="modalChooserGallery4" tabindex="-1" role="dialog" 
  aria-labelledby="File Chooser" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Choose / Upload File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <ul class="nav nav-pills" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" 
            aria-selected="true">Choose File</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" 
            aria-selected="false">Upload New</a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="image-selection-container">
            <div class="search-container">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Search image ..." aria-label="Search image ...">
                <span class="input-group-btn">
                <button class="btn btn-secondary" type="button">Go!</button>
                </span>
              </div>
            </div>
            <div class="item-container">
              <div class="item-wrapper">
                <img class="image" src="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png" />
                <label>Image 1</label>
              </div>
              <div class="item-wrapper">
                <img class="image" src="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png" />
                <label>Image 2</label>
              </div>
              <div class="item-wrapper">
                <img class="image" src="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png" />
                <label>Image 3</label>
              </div>
              <div class="item-wrapper">
                <img class="image" src="http://localhost:8100/media/ARYm6gOZfd8soisZkVZ0XoeWW7BRJ7yE.png" />
                <label>Image 4</label>
              </div>
            </div>
          </div>
          <div class="image-pagination">
            <div class="prev">
              <a href="#" class="btn-prev">< Prev</a>
            </div>
            <div class="page-info">
              Page 1 of 2
            </div>
            <div class="next">
              <a href="#" class="btn-next">Next ></a>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <?php if ($ownerModel !== null) { ?>
        <label class="control-label">File</label>
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
        </div>
      </div>
      </div>
    </div>
  </div>
</div>