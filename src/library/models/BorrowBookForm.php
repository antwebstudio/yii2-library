<?php
namespace ant\library\models;

use ant\user\models\User;

class BorrowBookForm extends \yii\base\Model {
    public $bookCopyId;
    public $userId;
    public $confirm;
    public $borrowDays = 7;
    public $bookLimitPerMember = 2;

    protected $_bookCopy;
    protected $_user;
	
	public function behaviors() {
		return [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
		];
	}

    public function rules() {
        return $this->getCombinedRules([
            [['bookCopyId'], 'exist', 'skipOnError' => true, 'targetClass' => BookCopy::className(), 'targetAttribute' => ['bookCopyId' => 'id']],
            [['bookCopyId', 'userId'], 'number'],
            [['bookCopyId', 'userId'], 'required'],
            [['confirm'], 'safe'],
            [['bookCopyId'], 'ant\library\validators\BookAvailableValidator'],
            [['userId'], 'ant\member\validators\MembershipValidator'],
            [['userId'], 'validateLimitOfBorrowPerMember', 'message' => 'Exceed limit of books can be borrowed by this member. '],
            //[['publisher_id'], 'exist', 'skipOnError' => true, 'targetClass' => BookPublisher::className(), 'targetAttribute' => ['publisher_id' => 'id']],
        ]);
    }

    public function validateLimitOfBorrowPerMember($attribute, $params, $validator) {
        $infos = $this->getBookBorrowedInfo();
        if (count($infos) >= $this->bookLimitPerMember) {
            $this->addError($attribute, $validator->message);
        }
    }

    public function save() {
        if ($this->validate()) {
            $model = new BookBorrow;
            $model->book_copy_id = $this->bookCopyId;
            $model->user_id = $this->userId;
            $model->borrow_days = $this->borrowDays;

            $model->expireAfterDays($this->borrowDays, true);

            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

            return true;
        }
    }

    public function getBookCopy() {
        if (!isset($this->_bookCopy)) {
            $this->_bookCopy = BookCopy::findOne($this->bookCopyId);
        }
        return $this->_bookCopy;
    }

    public function getUser() {
        if (!isset($this->_user)) {
            $this->_user = User::findOne($this->userId);
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