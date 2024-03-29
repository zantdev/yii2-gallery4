<?php

namespace zantknight\yii\gallery\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\web\UploadedFile;

use zantknight\yii\gallery\models\Gallery4;
use zantknight\yii\gallery\models\GalleryOwner;

class ApiController extends Controller
{
    public function actionIndex()
    {
        return $this->render('indexWidget', [
            'label' => 'Gallery',
        ]);
    }

    public function actionUpload()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $gallery = new Gallery4();
            $gallery->fileInput = UploadedFile::getInstance(
                $gallery,
                'fileInput'
            );
            $gallery->type = $gallery->fileInput->type;
            $arrGallName = explode(".", $gallery->fileInput->name);
            $ext = $arrGallName[sizeof($arrGallName) - 1];
            $gallery->ext = $ext;
            if (isset($_POST['model'])) {
                $galleryOwner = new GalleryOwner();
                $galleryOwner->model = $_POST['model'];
                $galleryOwner->created_at = date('Y-m-d H:i:s');
                $galleryOwner->save();
            }
            $gallery->title = $gallery->fileInput->name;
            $gallery->name = $gallery->generateName(32);
            $gallery->category = "GALLERY4";
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
            } else {
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

    public function actionFilterImage($q, $page, $limit)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $ret = [];
        $galCount = 0;
        $next = false;
        $prev = false;
        $offset = ($page - 1) * $limit;

        if ($q) {
            $galleries = Gallery4::find()->where(
                ['ilike', 'title', $q]
            )->orderBy(
                ['created_at' => SORT_DESC]
            )
                ->limit($limit)->offset($offset)->all();
            $galCount = Gallery4::find()->where(
                ['ilike', 'title', $q]
            )->count();
        } else {
            $galleries = Gallery4::find()->orderBy([
                'created_at' => SORT_DESC
            ])->limit($limit)->offset($offset)->all();
            $galCount = Gallery4::find()->count();
        }

        $ret['data'] = [];
        foreach ($galleries as $gal) {
            $http = 'https://' . $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['HTTPS'])) {
                $http = 'https://' . $_SERVER['HTTP_HOST'];
            }
            $ret['data'][] = [
                'id' => $gal->id,
                'title' => $gal->title,
                'ext' => $gal->ext,
                'description' => ($gal->description ? $gal->description : ""),
                'file_size' => $gal->file_size,
                'url' => $http . Url::to("@web/media/$gal->name.$gal->ext", false),
                'url_download' => $http . Url::to("@web/media/$gal->name.$gal->ext", false),
            ];
        }

        if ($limit * $page < $galCount) {
            $next = true;
        } else {
            $next = false;
        }

        if ($offset > 0) {
            $prev = true;
        } else {
            $prev = false;
        }

        $ret['meta'] = [
            'next' => $next,
            'prev' => $prev,
            'count' => $galCount
        ];

        return $ret;
    }

    public function actionPreviewFile($id = null, $modelOwner = null)
    {
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
                    $fileUrl = Url::to(
                        "@web/media/$gallery->name.$gallery->ext",
                        true
                    );
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

    public function actionDeleteFile()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('galId');
            $model = Yii::$app->request->post('model');
            $gallery = Gallery4::findOne($id);
            if ($gallery) {
                if (GalleryOwner::deleteAll([
                    'gallery_id' => $id,
                    'model' => $model
                ])) {
                    return [
                        'success' => true
                    ];
                }
            }
        }
    }

    public function actionDeletePersistent()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $ret = [
            'success' => false
        ];

        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $gallery = Gallery4::findOne($id);
            if ($gallery) {
                $filePath = Yii::$app->basePath . "/web/media/" .
                    $gallery->name . "." . $gallery->ext;
                if (FileHelper::unlink($filePath)) {
                    if (GalleryOwner::deleteAll([
                        'gallery_id' => $id
                    ])) {
                        if ($gallery->delete()) {
                            $ret = [
                                'success' => true
                            ];
                        }
                    }
                }
            }
        }

        return $ret;
    }

    public function actionSaveData()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $ret = [
            'success' => false
        ];
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $title = Yii::$app->request->post('title');
            $description = Yii::$app->request->post('description');
            $gallery = Gallery4::findOne($id);
            if ($gallery) {
                $gallery->title = $title;
                $gallery->description = $description;
                if ($gallery->save()) {
                    $ret = [
                        'success' => true
                    ];
                }
            }
        }

        return $ret;
    }
}
