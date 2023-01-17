<?php 
namespace zantknight\yii\gallery\models;


/**
 * This is the model class for table "gallery_4".
 *
 * @property int $id
 * @property int $gallery_id
 * @property string|null $model
 * @property string|null $category
 * @property string|null $owner_id
 * @property string|null $created_at
 */
class GalleryOwner extends \yii\db\ActiveRecord
{
    /**
    * {@inheritdoc}
    */
    public static function tableName()
    {
        return 'gallery_owner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gallery_id'], 'integer'],
            [['owner_id', 'created_at', 'category'], 'string'],
            [['model'], 'string', 'max' => 100],
        ];
    }
}
