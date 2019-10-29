<?php
namespace ant\library\validators;

use ant\library\models\DepositMoney;

class DepositMoneyValidator extends \yii\validators\Validator {
    public $message = 'User haven\'t paid deposit money. ';

    public function init() {
       
    }
    
    public function validateAttribute($model, $attribute) {
        $message = $this->message;

        if (isset($model->user)) {
			$userId = $model->user->id;
			
            $valid = DepositMoney::checkIsPaid($userId);

            if (!$valid) {
                $model->addError($attribute, $message);
            }
        } else {
            throw new \Exception('User ID is not set. ');
        }
    }
}