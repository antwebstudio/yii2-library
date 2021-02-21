<?php
namespace ant\library\models;

use Yii;
use ant\user\models\User;

class BorrowBookForm extends \yii\base\Model {
    const SCENARIO_CUSTOM_BARCODE = 'custom_barcode';
    public $bookCopyId;
    public $userId;
    public $confirm;
    public $borrowDays;
    public $bookLimitPerMember;
    public $customBarcode;
    public $reserve = false;

	protected $_bookBorrowed;
    protected $_bookReserved;
    protected $_bookCopy;
    protected $_user;
    protected $_libraryPolicy;
	
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
            [['bookCopyId'], 'required', 'except' => self::SCENARIO_CUSTOM_BARCODE],
            [['customBarcode'], 'required', 'on' => self::SCENARIO_CUSTOM_BARCODE],
            [['userId'], 'required'],
            [['confirm', 'reserve'], 'safe'],
            [['bookCopyId'], 'ant\library\validators\BookAvailableValidator', 'when' => function() {
                return !$this->reserve;
            }],
            [['userId'], 'ant\member\validators\MembershipValidator'],
            [['userId'], 'validateLimitOfBorrowPerMember', 'message' => 'Exceed limit of books can be borrowed by this member. '],
            [['userId'], '\ant\library\validators\DepositMoneyValidator', 'when' => function() {
                return $this->getTotalDepositAmountNeeded() > 0;
            }],
            //[['publisher_id'], 'exist', 'skipOnError' => true, 'targetClass' => BookPublisher::className(), 'targetAttribute' => ['publisher_id' => 'id']],
        ]);
    }

    public function validateLimitOfBorrowPerMember($attribute, $params, $validator) {
        $infos = $this->getBookBorrowedInfo();
        if (count($infos) >= $this->getBookLimit()) {
            $this->addError($attribute, $validator->message);
        }
    }

    public function save() {
        if ($this->validate()) {
            if ($this->reserve) {
                $this->bookCopy->reserveBy($this->userId, $this->getBookBorrowDays());
            } else {
                $this->bookCopy->lendTo($this->userId, $this->getBookBorrowDays());
            }   

            return true;
        }
    }

    protected function getLibraryPolicy() {
        if (!isset($this->_libraryPolicy)) {
            $this->_libraryPolicy = Yii::$app->getModule('library')->getPolicy($this->user);
        }
        return $this->_libraryPolicy;
    }

    public function getBookLimit() {
        return $this->libraryPolicy->getMaxBorrow() ?? $this->bookLimitPerMember;
    }

    public function getTotalDepositAmountNeeded() {
        return $this->libraryPolicy->getDepositNeeded() ?? null;
    }

    public function getMemberTypeName() {
        return $this->libraryPolicy->getMemberTypeName() ?? null;
    }

    public function getBookBorrowDays() {
        return $this->libraryPolicy->getBorrowDays() ?? $this->borrowDays;
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

    public function getUser() {
        if (!isset($this->_user)) {
            $this->_user = User::findOne($this->userId);
        }
        return $this->_user;
    }

    public function getIsBookBorrowed() {
        return $this->bookCopy->isBorrowed;
    }
	
	public function getBookBorrowedRecords() {
		if (!isset($this->_bookBorrowed)) {
			$this->_bookBorrowed = BookBorrow::find()
                ->forUser($this->user)
                ->notReturned()
                ->excludeReserved()->all();
		}
		return $this->_bookBorrowed;
	}

    public function getBookReservedRecords() {
		if (!isset($this->_bookReserved)) {
            $this->_bookReserved =  BookBorrow::find()
                ->forUser($this->user)
                ->notReturned()
                ->reserved()->all();
        }
		return $this->_bookReserved;
    }

    public function getTotalBookBorrowedOrReserved() {
        return count($this->bookBorrowedRecords) + count($this->bookReservedRecords);
    }
	
	public function getBookBorrowedInfo() {
		$lines = [];
		foreach ($this->getBookBorrowedRecords() as $record) {
			$lines[] = $record->bookCopy->book->title;
		}
		return $lines;
	}
	
	public function getBookReservedInfo() {
		$lines = [];
		foreach ($this->getBookReservedRecords() as $record) {
			$lines[] = $record->bookCopy->book->title;
		}
		return $lines;
	}
	
	public function refresh() {
		$this->_bookBorrowed = null;
		$this->_user = null;
	}
}