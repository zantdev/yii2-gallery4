<?php 
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\View;

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
                <input id="txt-image-search" type="text" class="form-control" placeholder="Search file ..." 
                  aria-label="Search image ...">
              </div>
            </div>
            <div id="item-container" class="item-container">
              
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
          <div class="upload-container">
            <?php if ($ownerModel !== null) { ?>
            <label class="control-label">Image / Document</label>
            <?= FileInput::widget($config); ?>
            <?= Html::hiddenInput('fileModel', 
                  StringHelper::basename($ownerModel::className())
                ); ?>
            <a href="javascript:void(0)" id="btn-insert" style="display: none" class="btn btn-success insert-document">
              <i class="cil-check"></i> Insert
            </a>
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
</div>

<?php 
$this->registerJs("
  var multiple = $multiple;
  var page = 1,
      limit = 9,
      id = -1,
      size = -1,
      title = '',
      url = '';
  
  function removeNoFile() {
    $('.no-file-wrapper').remove();
  }
  function invokeClickableItem() {
    $('.item-wrapper').on('click', function(e){
      removeNoFile();
      addFile({
        id: $(this).attr('data-id'),
        title: $(this).attr('data-title'),
        size: $(this).attr('data-size'),
        url: $(this).attr('data-url'),
        url_download: $(this).attr('data-url_download'),
      }, multiple);
      var strGallery = '',
          galleryId = '';

      if (multiple===0) {
        $('#btn-add-change').html('<i class=\"cil-swap-vertical\"> Change Image</i>');
        galleryId = $(this).attr('data-id');
        strGallery = `\${strGallery}:\${galleryId}`;
      }else {
        strGallery = $('#gallery4Id').val();
        galleryId = $(this).attr('data-id');
        strGallery = `\${strGallery}:\${galleryId}`;
      }
      $('#gallery4Id').val(strGallery);
    });
  }
  function addFile(data, multiple) {
    var tpl = `
      <div class='item'>
        <a data-lightbox='\${data.id}' data-title='\${data.title}' href='\${data.url}'>
          <img class='preview' src='\${data.url}'>
        </a>
        <div class='file-name'>\${data.title}</div>
        <div class='info-container'>
          <div class='file-size'>\${data.size}</div>
          <div class='file-action'>
            <a class='action-wrapper action-download' href='\${data.url_download}'>
              <i class='c-icon cil-cloud-download'></i>
            </a>
            <a class='action-wrapper action-delete' href='#'>
              <i class='c-icon cil-trash'></i>
            </a>
          </div>
        </div>
      </div>
    `;
    if (multiple === 1) {
      $('#content-gallery4').append(tpl);
    }else {
      $('#content-gallery4 *').remove();
      $('#content-gallery4').append(tpl);
    }
    $('#modalChooserGallery4').modal('hide');
  }

  function loadImageSelection(q, page, limit) {
    $.ajax({
      url: '".Url::to(["gallery4/api/filter-image"])."',
      data: {
        q: q,
        page: page,
        limit: limit,
      },
      success: function(data){
        var tpl = '';
        if (data.data.length > 0) {
          data.data.forEach(item => {
            tpl = 
            `\${tpl}<div class='item-wrapper' data-id='\${item.id}' 
              data-title='\${item.title}' data-size='\${item.file_size}'
              data-url='\${item.url}' data-url_download='\${item.url_download}'>
              <img class='image' src='\${item.url}' />
              <label>\${item.title}</label>
            </div>`;
          });
        }else {
          tpl = '<p style=\"text-align: center; width: 100%; margin-top: 20px;\">No Data Found!</p>';
        }
  
        if (data.meta.next) {
          $('.btn-next').show();
        }else {
          $('.btn-next').hide();
        }
  
        if (data.meta.prev) {
          $('.btn-prev').show();
        }else {
          $('.btn-prev').hide();
        }
        
        if (data.meta.count > limit){
          var totalPage = parseInt(data.meta.count / limit);
          if (data.meta.count % limit !== 0) {
            totalPage++;
          }
          $('.page-info').text(`Page \${page} of \${totalPage}`);
        }else {
          $('.page-info').hide();
        }

        $('#item-container *').remove();
        $('#item-container').append(tpl);
        invokeClickableItem();
      }
    })
  }
  
  $('.btn-prev').on('click', function(e){
    page--;
    var q = $('#txt-image-search').val();
    loadImageSelection(q, page, limit);
  });

  $('.btn-next').on('click', function(e){
    page++;
    var q = $('#txt-image-search').val();
    loadImageSelection(q, page, limit);
  });

  $('#txt-image-search').on('input', function(e){
    var q = $('#txt-image-search').val();
    loadImageSelection(q, 1, limit);
  });

  $('#gallery4-fileinput').on('filebatchuploadsuccess',
    function(event, data){
      id = data.response.initialPreviewConfig[0].key;
      title = data.response.initialPreviewConfig[0].title;
      size = data.response.initialPreviewConfig[0].size;
      url = data.response.initialPreviewConfig[0].downloadUrl;

      var strGallery = $('#gallery4Id').val();
      var galleryId = data.response.initialPreviewConfig[0].key;
      strGallery = `\${strGallery}:\${galleryId}`;
      $('#gallery4Id').val(strGallery);
      $('#btn-insert').show();
    }
  );

  $('#btn-insert').on('click', function(e){
    addFile({
      id: id,
      title: title,
      size: size,
      url: url,
      url_download: url,
    }, multiple);
  });

  $('#btn-add-change').on('click', function(e){
    page = 1;
    $('#txt-image-search').val('');
    loadImageSelection('', page, limit);
  });
",
View::POS_READY,
'chooser-image');
?>