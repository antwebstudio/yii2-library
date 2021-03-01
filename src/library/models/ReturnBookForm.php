<?php
namespace ant\library\models;

use ant\user\models\User;

class ReturnBookForm extends \yii\base\Model {
    const SCENARIO_CUSTOM_BARCODE = 'custom_barcode';
    
    public $bookCopyId;
    public $customBarcode;
    public $confirm;

    protected $_bookCopy;
    protected $_user;
    protected $_bookBorrowRecord;

    public function rules() {
        return [
            [['bookCopyId'], 'exist', 'skipOnError' => true, 'targetClass' => BookCopy::className(), 'targetAttribute' => ['bookCopyId' => 'id']],
            [['bookCopyId'], 'number'],
            [['bookCopyId'], 'required', 'except' => self::SCENARIO_CUSTOM_BARCODE],
            [['customBarcode'], 'required', 'on' => self::SCENARIO_CUSTOM_BARCODE],
            [['customBarcode'], function($attribute, $params, $validator) {
                if (!isset($this->bookBorrow)) {
                    $this->addError($attribute, $validator->message);
                }
            }, 'message' => 'Book borrow record is not exist.', 'on' => self::SCENARIO_CUSTOM_BARCODE],
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
            if ($this->scenario == self::SCENARIO_CUSTOM_BARCODE) {
                $this->_bookCopy = BookCopy::find()->andWhere(['custom_barcode' => $this->customBarcode])->one();
            } else {
                $this->_bookCopy = BookCopy::findOne($this->bookCopyId);
            }
        }
        return $this->_bookCopy;
    }

    public function getBookBorrow() {
        if (!isset($this->_bookBorrowRecord)) {
            if (isset($this->bookCopy)) {
                $this->_bookBorrowRecord = $this->bookCopy->getBookBorrow(true)->one();
            }
        }
        return $this->_bookBorrowRecord;
    }

    public function getUser() {
        if (!isset($this->_user)) {
            if (isset($this->bookBorrow)) {
                $this->_user = User::findOne($this->bookBorrow->user_id);
            }
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