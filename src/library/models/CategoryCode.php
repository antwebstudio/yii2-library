<?php

namespace ant\library\models;

use Yii;
use ant\category\models\Category;

/**
 * This is the model class for table "ks_library_category_code".
 *
 * @property int $id
 * @property int $category_id
 * @property string $udc
 * @property string $dewey_my
 * @property string $dewey_tw
 * @property string $custom
 *
 * @property Category $category
 */
class CategoryCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%library_category_code}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id'], 'integer'],
            [['udc'], 'string', 'max' => 255],
            [['dewey_my', 'dewey_tw', 'custom'], 'string', 'max' => 10],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'udc' => 'Udc',
            'dewey_my' => 'Dewey My',
            'dewey_tw' => 'Dewey Tw',
            'custom' => 'Custom',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
