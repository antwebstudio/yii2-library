<?php

namespace ant\library\models;

use Yii;
use yii\db\Expression;
use ant\user\models\User;
use ant\contact\models\Contact;

/**
 * This is the model class for table "ks_library_book_borrow".
 *
 * @property int $id
 * @property int $book_copy_id
 * @property int $user_id
 * @property string $remark
 * @property int $borrow_days
 * @property int $status
 * @property string $created_at
 * @property int $created_by
 *
 * @property BookCopy $bookCopy
 * @property User $user
 */
class BookBorrow extends \yii\db\ActiveRecord
{
    const STATUS_RESERVED = 9;
    const STATUS_BORROWED = 0;
    const STATUS_CLAIMED = 8; // For reservation

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%library_book_borrow}}';
    }

    public static function find() {
        return new \ant\library\models\query\BookBorrowQuery(get_called_class());
    }

    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\Expirable',
                'modelClass' => get_class($this),
            ],
            [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'updatedByAttribute' => false,
            ],
			[
                'class' => \ant\behaviors\TimestampBehavior::className(),
                'updatedAtAttribute' => false,
			],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_copy_id', 'user_id', 'borrow_days'], 'required'],
            [['book_copy_id', 'user_id', 'borrow_days', 'status', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['book_copy_id'], 'exist', 'skipOnError' => true, 'targetClass' => BookCopy::className(), 'targetAttribute' => ['book_copy_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_copy_id' => 'Book Copy ID',
            'user_id' => 'User ID',
            'remark' => 'Remark',
            'borrow_days' => 'Borrow Days',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookCopy()
    {
        return $this->hasOne(BookCopy::className(), ['id' => 'book_copy_id']);
    }
	
	public function getCreatedBy() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}

    public function getContact() {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
    }

    public function getAddress() {
        return $this->contact->address;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function markAsReturned($recordedByUserId) {
        $this->returned_at = new Expression('NOW()');

        // Processed by who (admin user id)
        $this->returned_by = $recordedByUserId;

        if (!$this->save()) throw new \Exception(print_r($this->errors, 1));

        return true;
    }

    public function renew($days) {
        return $this->extendExpiryDate($days);
    }

    public function getIsReturned() {

    }

    public function getIsReserved() {
        return $this->status == BookBorrow::STATUS_RESERVED;
    }
}
