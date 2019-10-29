<?php

namespace ant\library\models;

use Yii;

/**
 * This is the model class for table "ks_library_book_copy".
 *
 * @property int $id
 * @property int $book_id
 * @property int $status
 * @property string $created_at
 * @property int $created_by
 */
class BookCopy extends \yii\db\ActiveRecord
{
	const STICKER_LABEL_NEW = 0;
	const STICKER_LABEL_NEED_REPRINT = 1;
	const STICKER_LABEL_PRINTED = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%library_book_copy}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id'], 'required'],
            [['book_id', 'status', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_id' => 'Book ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }
	
	public static function find() {
		return new \ant\library\models\query\BookCopyQuery(get_called_class());
	}
	
	public function getBarcode() {
		return $this->id;
	}

    public function getBook() {
        return $this->hasOne(Book::className(), ['id' => 'book_id']);
    }

    public function getBookBorrow($notReturnedOnly = false) {
        $query = $this->hasMany(BookBorrow::className(), ['book_copy_id' => 'id']);
        if ($notReturnedOnly) $query->andWhere(['returned_at' => null]);
        return $query;
    }

    public function getIsAvailableForBorrow() {
        return $this->getBookBorrow(true)->count() == 0;
    }
}
