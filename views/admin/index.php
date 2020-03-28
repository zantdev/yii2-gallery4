<?php 
use zantknight\yii\gallery\Gallery4Asset;
use yii\web\View;
use yii\helpers\Url;

$bundle = Gallery4Asset::register($this);
?>
<div class="file-list-container">
  <div class="header">
    <h3>File</h3>
  </div>
  <div class="row mb-3">
    <div class="col-lg-4">
      <input id="txt-image-search" type="text" class="form-control" 
        placeholder="Search file ..." aria-label="Search image ...">
    </div>
  </div>
  <div class="content" id="item-container">
  </div>
  <div class="image-pagination">
    <div class="prev">
      <a href="#" class="btn-prev">&lt; Prev</a>
    </div>
    <div class="page-info">Page 1 of 1</div>
    <div class="next">
      <a href="#" class="btn-next">Next &gt;</a>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalGallery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-4">
            <div class="img-container">
              <img id="img-url" class="preview" src="http://localhost:8100/media/Bp4jzaViS2GSwLcWKX63A0LDdyC58tx8.jpg">
            </div>
          </div>
          <div class="col-lg-8">
            <form>
              <input type="hidden" id="txt-id" />
              <div class="form-group">
                <label for="txt-title">Title</label>
                <input type="text" class="form-control" id="txt-title" placeholder="Title">
              </div>
              <div class="form-group">
                <label for="txt-description">Description</label>
                <textarea class="form-control" id="txt-description" placeholder="Description"></textarea>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="btn-save" type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?php 
$this->registerJs("
  var page = 1,
      limit = 9;

  function loadImageSelection(q, page, limit) {
    $.ajax({
      url: '".Url::to(["api/filter-image"])."',
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
            `\${tpl}
            <div class='item item-\${item.id}'>
              <a data-lightbox='\${item.id}' data-title='\${item.title}' href='\${item.url}'>
                <img class='preview' src='\${item.url}'>
              </a>
              <div class='body-container'>
                <div id='item-title-\${item.id}' class='file-name'>\${item.title}</div>
                <div class='info-container'>
                  <div class='file-size'>\${item.file_size}</div>
                  <div class='file-action'>
                    <a class='action-wrapper action-edit'
                      id='item-\${item.id}'
                      data-id='\${item.id}'
                      data-url='\${item.url}' 
                      data-title='\${item.title}'
                      data-description='\${item.description}'  
                      href='#' data-toggle='modal' data-target='#modalGallery'>
                      <i class='c-icon cil-pencil'></i>
                    </a>
                    <a class='action-wrapper action-download' href='\${item.url}'>
                      <i class='c-icon cil-cloud-download'></i>
                    </a>
                    <a class='action-wrapper action-delete' data-galId='\${item.id}' href='#'>
                      <i class='c-icon cil-trash'></i>
                    </a>
                  </div>
                </div>
              </div>
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
        
        if (data.meta.count > 0){
          $('.page-info').show();
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
        invokeActionButton();
      }
    })
  }

  function invokeActionButton() {
    $('.action-edit').on('click', function(){
      var id = $(this).attr('data-id');
      var img_url = $(this).attr('data-url');
      var title = $(this).attr('data-title');
      var description = $(this).attr('data-description');
      $('#img-url').attr('src', img_url);
      $('#txt-id').val(id);
      $('#txt-title').val(title);
      $('#txt-description').val(description);
    });

    $('.action-delete').on('click', function(){
      c = confirm('Do really want to delete this item?');
      if (c) {
        var id = $(this).attr('data-galId');
        deletePersistent(id);
      }
    });
  }

  function saveData(id, title, description) {
    $.ajax({
      type: 'POST',
      data: {
        id: id,
        title: title,
        description: description
      },
      url: '".Url::to(['api/save-data'])."',
      success: function(data){
        if (data.success) {
          $(`#item-title-\${id}`).text(title);
          $(`#item-\${id}`).attr('data-title', title);
          $(`#item-\${id}`).attr('data-description', description);
          $('#modalGallery').modal('hide');
        }
      }
    })
  }

  function deletePersistent(id) {
    $.ajax({
      type: 'POST',
      data: {
        id: id
      },
      url: '".Url::to(['api/delete-persistent'])."',
      success: function(data){
        if (data.success) {
          $(`.item-\${id}`).fadeOut('normal', function(){
            $(this).remove();
          });
        }
      }
    })
  }

  loadImageSelection('', page, limit);

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

  $('#btn-save').on('click', function(e){
    var id = $('#txt-id').val();
    var title = $('#txt-title').val();
    var description = $('#txt-description').val();

    saveData(id, title, description);
  });

  $('#txt-image-search').on('input', function(e){
    var q = $('#txt-image-search').val();
    loadImageSelection(q, 1, limit);
  });
", 
View::POS_READY,
'chooser-image')
?>