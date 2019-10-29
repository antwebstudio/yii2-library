<?php
namespace ant\library\models;

use ant\user\models\User;

class ReturnBookForm extends \yii\base\Model {
    public $bookCopyId;
    public $confirm;

    protected $_bookCopy;
    protected $_user;
    protected $_bookBorrowRecord;

    public function rules() {
        return [
            [['bookCopyId'], 'exist', 'skipOnError' => true, 'targetClass' => BookCopy::className(), 'targetAttribute' => ['bookCopyId' => 'id']],
            [['bookCopyId'], 'number'],
            [['bookCopyId'], 'required'],
            [['confirm'], 'safe'],
            [['bookCopyId'], 'ant\library\validators\BookAvailableValidator', 'not' => true],
        ];
    }
	
	public function attributeLabels() {
		return [
			'bookCopyId' => 'Barcode ID',
		];
	}

    public function save() {
        if ($this->validate()) {
            return $this->bookBorrow->markAsReturned(\Yii::$app->user->id);
        }
    }

    public function getBookCopy() {
        if (!isset($this->_bookCopy)) {
            $this->_bookCopy = BookCopy::findOne($this->bookCopyId);
        }
        return $this->_bookCopy;
    }

    public function getBookBorrow() {
        if (!isset($this->_bookBorrowRecord)) {
            $this->_bookBorrowRecord = $this->bookCopy->getBookBorrow(true)->one();
        }
        return $this->_bookBorrowRecord;
    }

    public function getUser() {
        if (!isset($this->_user)) {
            $this->_user = User::findOne($this->bookBorrow->user_id);
        }
        return $this->_user;
    }
	
	public function getBookBorrowedInfo() {
		$borrow = BookBorrow::find()->andWhere([
			'user_id' => $this->user->id,
			'returned_at' => null,
			'returned_by' => null,
		]);
		
		$lines = [];
		foreach ($borrow->all() as $record) {
			$lines[] = $record->bookCopy->book->title;
		}
		return $lines;
	}
}