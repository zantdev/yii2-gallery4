<?php

use yii\db\Migration;

/**
 * Class m200315_030748_gallery4
 */
class m200315_030748_gallery4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('gallery_4', [
            'id' => $this->primaryKey(),
            'model' => $this->string(100),
            'owner_id' => $this->string(),
            'name' => $this->string(100),
            'file' => $this->string(),
            'title' => $this->string(100),
            'type' => $this->string('50'),
            'ext' => $this->string('10'),
            'description' => $this->text()
        ]);
        $this->createIndex(
            'idx-gallery_4-owner_id',
            'gallery_4',
            'owner_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('gallery_4');
    }
}
