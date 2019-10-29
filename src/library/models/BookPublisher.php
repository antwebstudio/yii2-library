<?php

namespace ant\library\models;

use Yii;

/**
 * This is the model class for table "ks_library_book_publisher".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $created_at
 * @property int $created_by
 *
 * @property LibraryBook[] $libraryBooks
 */
class BookPublisher extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%library_book_publisher}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLibraryBooks()
    {
        return $this->hasMany(LibraryBook::className(), ['publisher_id' => 'id']);
    }
}
