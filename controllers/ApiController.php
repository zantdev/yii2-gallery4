<?php
namespace zantknight\yii\gallery\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\web\UploadedFile;

use zantknight\yii\gallery\models\Gallery4;
use zantknight\yii\gallery\models\GalleryOwner;

class ApiController extends Controller 
{
    public function actionIndex() {
        return $this->render('indexWidget', [
            'label' => 'Gallery',
        ]);
    }

    public function actionUpload() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $gallery = new Gallery4();
            $gallery->fileInput = UploadedFile::getInstance(
                $gallery, 'fileInput'
            );
            $gallery->type = $gallery->fileInput->type;
            $arrGallName = explode(".", $gallery->fileInput->name);
            $ext = $arrGallName[sizeof($arrGallName)-1];
            $gallery->ext = $ext;
            if (isset($_POST['model'])) {
                $galleryOwner = new GalleryOwner();
                $galleryOwner->model = $_POST['model'];
                $galleryOwner->created_at = date('Y-m-d H:i:s');
                $galleryOwner->save();
            }
            $gallery->title = $gallery->fileInput->name;
            $gallery->name = $gallery->generateName(32);
            $gallery->created_at = date('Y-m-d H:i:s');
            $gallery->file_size = $gallery->fileInput->size;
            if ($gallery->upload()) {
                $galleryOwner->gallery_id = $gallery->id;
                $galleryOwner->save();
                $fileUrl = Url::to("@web/media/$gallery->name.$gallery->ext", true);
                $preview[] = $fileUrl;
                $config[] =  [
                    'key' => $gallery->id,
                    'caption' => $gallery->name,
                    'title' => $gallery->title,
                    'size' => $gallery->fileInput->size,
                    'downloadUrl' => $fileUrl,
                ];
                return [
                    'initialPreview' => [], 
                    'initialPreviewConfig' => $config, 
                    'initialPreviewAsData' => false
                ];;
            }else {
                return [
                    'success' => false,
                    'message' => 'Upload data is fail'
                ];
            }
            
        }

        return [
            'success' => false,
            'message' => 'Non image post is declined!'
        ];
    }

    public function actionFilterImage($q, $page, $limit) {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $arrQ = [];
        $ret = [];
        $galCount = 0;
        $next = false;
        $prev = false;
        $offset = ($page-1)*$limit;
        
        if ($q) {
            $galleries = Gallery4::find()->where(
                ['ilike', 'title', $q])->orderBy(
                ['created_at' => SORT_DESC])
                ->limit($limit)->offset($offset)->all();
            $galCount = Gallery4::find()->where($arrQ)->count();
        }else {
            $galleries = Gallery4::find()->orderBy([
                'created_at' => SORT_DESC
            ])->limit($limit)->offset($offset)->all();
            $galCount = Gallery4::find()->count();
        }

        $ret['data'] = [];
        foreach ($galleries as $gal) {
            $ret['data'][] = [
                'id' => $gal->id,
                'title' => $gal->title,
                'file_size' => $gal->file_size,
                'url' => Url::to("@web/media/$gal->name.$gal->ext", true),
                'url_download' => Url::to("@web/media/$gal->name.$gal->ext", true),
            ];
        }

        if ($limit < $galCount) {
            $next = true;
        }else {
            $next = false;
        }

        if ($offset > 0) {
            $prev = true;
        }else {
            $prev = false;
        }

        $ret['meta'] = [
            'next' => $next,
            'prev' => $prev,
            'count' => $galCount
        ];

        return $ret;
    }

    public function actionPreviewFile($id = null, $modelOwner = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $ret = [];
        if ($id && $modelOwner) {
            $galleryOwner = GalleryOwner::find()->where([
                'owner_id' => $id,
                'model' => $modelOwner
            ])->all();

            foreach ($galleryOwner as $go) {
                $gallery = Gallery4::findOne($go->gallery_id);
                if ($gallery) {
                    $fileUrl = Url::to("@web/media/$gallery->name.$gallery->ext", true);
                    $ret[] = [
                        'id' => $gallery->id,
                        'title' => $gallery->title,
                        'size' => $gallery->size,
                        'url' => $fileUrl
                    ];
                } 
            }
        }

        return $ret;
    }
}
?>