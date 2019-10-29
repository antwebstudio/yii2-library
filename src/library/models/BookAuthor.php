<?php

namespace ant\library\models;

use Yii;

/**
 * This is the model class for table "ks_library_book_author".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $created_at
 * @property int $created_by
 *
 * @property LibraryBookAuthorMap[] $libraryBookAuthorMaps
 */
class BookAuthor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%library_book_author}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'ant\validators\ChineseStringValidator'], // This validator/rule must be before the unique validator/rule
            [['status', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'unique'],
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
    public function getLibraryBookAuthorMaps()
    {
        return $this->hasMany(LibraryBookAuthorMap::className(), ['author_id' => 'id']);
    }

    /*public function getText() {
        return $this->name;
    }*/
}
