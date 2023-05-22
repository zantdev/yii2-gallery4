<?php 
namespace zantknight\yii\gallery;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->params['foo'] = 'bar';
    }
}
?>