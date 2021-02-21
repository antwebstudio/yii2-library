<?php
namespace ant\library\models;

use ant\user\models\User;

class BorrowBookForm extends \yii\base\Model {
    const SCENARIO_CUSTOM_BARCODE = 'custom_barcode';

    public $bookCopyId;
    public $userId;
    public $confirm;
    public $borrowDays;
    public $bookLimitPerMember;
    public $customBarcode;

	protected $_bookBorrowed;
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
            [['bookCopyId'], 'required', 'except' => self::SCENARIO_CUSTOM_BARCODE],
            [['customBarcode'], 'required', 'on' => self::SCENARIO_CUSTOM_BARCODE],
            [['userId'], 'required'],
            [['confirm'], 'safe'],
            [['bookCopyId'], 'ant\library\validators\BookAvailableValidator'],
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
        if (count($infos) >= $this->bookLimit) {
            $this->addError($attribute, $validator->message);
        }
    }

    public function save() {
        if ($this->validate()) {
            $model = new BookBorrow;
            $model->book_copy_id = $this->bookCopy->id;
            $model->user_id = $this->userId;
            $model->borrow_days = $this->borrowDays;

            $model->expireAfterDays($this->borrowDays, true);

            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

            return true;
        }
    }

    protected function getMemberTypePackageItem() {
        $memberType = $this->getUserMemberType();
        if (isset($memberType)) {
            return $memberType->packageItems[0];
        }
    }

    protected function getUserMemberType() {
        if (isset($this->_user)) {
            $subscription = \ant\subscription\models\Subscription::find()->currentlyActiveForUser($this->_user->id)
                ->type('member')
                ->isPaid()
                ->orderBy('expire_at DESC')
                ->one();
        }

        return isset($subscription) ? $subscription->package : null;
    }

    public function getBookLimit() {
        $subscriptionItem = $this->getMemberTypePackageItem();
        if (isset($subscriptionItem->book_limit)) {
            return $subscriptionItem->book_limit;
        }
        return $this->bookLimitPerMember;
    }

    public function getTotalDepositAmountNeeded() {
        $subscriptionItem = $this->getMemberTypePackageItem();
        return $subscriptionItem->options['depositAmount'] ?? 0;
    }

    public function getMemberTypeName() {
        $memberType = $this->getUserMemberType();
        return isset($memberType) ? $memberType->name : '';
    }

    public function getBookBorrowDays() {
        $subscriptionItem = $this->getMemberTypePackageItem();
        if (isset($subscriptionItem->book_limit) && $subscriptionItem->book_limit) {
           return $subscriptionItem->content_valid_period; 
        }
        return $this->borrowDays;
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
			$this->_bookBorrowed = BookBorrow::find()->andWhere([
				'user_id' => $this->user->id,
				'returned_at' => null,
				'returned_by' => null,
			])->all();
		}
		return $this->_bookBorrowed;
	}
	
	public function getBookBorrowedInfo() {
		$lines = [];
		foreach ($this->getBookBorrowedRecords() as $record) {
			$lines[] = $record->bookCopy->book->title;
		}
		return $lines;
	}
	
	public function refresh() {
		$this->_bookBorrowed = null;
		$this->_user = null;
	}
}