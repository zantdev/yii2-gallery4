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
            'name' => $this->string(100),
            'file_size' => $this->integer(),
            'title' => $this->string(100),
            'type' => $this->string('50'),
            'ext' => $this->string('10'),
            'category' => $this->string('20'),
            'description' => $this->text(),
            'created_at' => $this->dateTime(),
        ]);
        $this->createTable('gallery_owner', [
            'id' => $this->primaryKey(),
            'gallery_id' => $this->integer(),
            'owner_id' => $this->string(),
            'model' => $this->string(100),
            'created_at' => $this->dateTime(),
        ]);
        $this->createIndex(
            'idx-gallery_owner-owner_id',
            'gallery_owner',
            'owner_id'
        );
        $this->addForeignKey(
            'fk-gallery_owner-gallery_id',
            'gallery_owner',
            'gallery_id',
            'gallery_4',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('gallery_owner');
        $this->dropTable('gallery_4');
    }
}
