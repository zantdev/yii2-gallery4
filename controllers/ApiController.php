<?php
namespace zantknight\yii\gallery\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\web\UploadedFile;

use zantknight\yii\gallery\models\Gallery4;

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
                $gallery->model = $_POST['model'];
            }
            $gallery->name = $gallery->generateName(32);
            if ($gallery->upload()) {
                $fileUrl = Url::to("@web/media/$gallery->name.$gallery->ext", true);
                $preview[] = $fileUrl;
                $config[] =  [
                    'key' => $gallery->id,
                    'caption' => $gallery->name,
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
}
?>