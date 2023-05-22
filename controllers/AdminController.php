<?php 
namespace zantknight\yii\gallery\controllers;

use Yii;
use yii\web\Controller;
use zantknight\yii\gallery\models\Gallery4;
use zantknight\yii\gallery\models\GalleryOwner;

class AdminController extends Controller 
{
    public function actionIndex() {
        return $this->render('index');
    }
}
?>