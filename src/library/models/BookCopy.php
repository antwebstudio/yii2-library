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
    use \ant\db\traits\ActiveRecordShortcut;

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
	
	public function behaviors() {
		return [
			[
				'class' => \ant\behaviors\TimestampBehavior::class,
				'updatedAtAttribute' => null,
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id'], 'required'],
            [['book_id', 'status', 'created_by'], 'integer'],
            [['status', 'is_trashed', 'sticker_label_status'], 'default', 'value' => 0],
            [['created_at', 'custom_barcode', 'shelf_mark'], 'safe'],
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

    public static function barcodeAttribute() {
        $module = Yii::$app->getModule('library');
        return $module->barcode['attribute'] ?? 'id';
    }

    public static function barcodeType() {
        $module = Yii::$app->getModule('library');
        return $module->barcode['generator']['type'];
    }

    public function reserveBy($user, $day) {
        $model = new BookBorrow;
        $model->book_copy_id = $this->id;
        $model->user_id = is_object($user) ? $user->id : $user;
        $model->borrow_days = $day;
        $model->status = BookBorrow::STATUS_RESERVED;

        $borrow = $this->getBookBorrow()->expireLast()->notExpired()->one();

        if (isset($borrow) && isset($borrow->expireAt)) {
            $model->setExpireAt($borrow->expireAt->addDays($day));
        } else {
            $model->expireAfterDays($day, true);
        }

        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

        return $model;
    }

    public function lendTo($user, $day) {
        $model = new BookBorrow;
        $model->book_copy_id = $this->id;
        $model->user_id = is_object($user) ? $user->id : $user;
        $model->borrow_days = $day;

        $model->expireAfterDays($day, true);

        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

        return $model;
    }
	
	public function getBarcode() {
        $attribute = self::barcodeAttribute();
		return $this->{$attribute};
	}

    public function getBook() {
        return $this->hasOne(Book::className(), ['id' => 'book_id']);
    }

    public function getBookBorrow($notReturnedOnly = false) {
        if (YII_DEBUG && $notReturnedOnly) deprecate();

        $query = $this->hasMany(BookBorrow::className(), ['book_copy_id' => 'id']);
        if ($notReturnedOnly) $query->andWhere(['returned_at' => null]);
        return $query;
    }

    public function getIsAvailableForBorrow() {
        return !$this->isBorrowed && !$this->isReserved;
    }

    public function isAvailableForBorrow($user) {
        $userId = is_object($user) ? $user->id : $user;
        return !$this->isBorrowed && (!$this->isReserved || $userId == $this->currentReservee->id);
    }

    public function getCurrentReservee() {
        $borrow = $this->getBookBorrow()->reserved()->notExpired()->one();
        return $borrow->user ?? null;
    }

    public function getIsBorrowed() {
        return $this->getBookBorrow()->borrowed()->notReturned()->notExpired()->count() > 0;
    }

    public function getIsReserved() {
        return $this->getBookBorrow()->reserved()->notExpired()->count() > 0;
    }

    public function getBookShelfCode() {
        return $this->shelf_mark ?? null;
    }

    public function getDefaultCategoryName() {
        $category = $this->book->getCategories()->one();
        return isset($category) ? $category->title : null;
    }
}
