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
            'owner_id' => $this->integer(),
            'name' => $this->string(100),
            'file' => $this->string(),
            'title' => $this->string(100),
            'type' => $this->string('50'),
            'ext' => $this->string('10'),
            'description' => $this->text()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('gallery_4');
    }
}
