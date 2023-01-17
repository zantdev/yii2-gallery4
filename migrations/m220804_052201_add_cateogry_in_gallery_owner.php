<?php

use yii\db\Migration;

/**
 * Class m220804_052201_add_cateogry_in_gallery_owner
 */
class m220804_052201_add_cateogry_in_gallery_owner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('gallery_owner', 'category', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('gallery_owner', 'category');
    }
}
