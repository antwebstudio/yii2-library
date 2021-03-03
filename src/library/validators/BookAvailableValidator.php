<?php
namespace ant\library\validators;

use ant\library\models\BookCopy;

class BookAvailableValidator extends \yii\validators\Validator {
    public $not = false;

    public $message = 'Book is not available for borrow. ';

    public $notMessage = 'Book is not borrowed. ';

    public $userIdAttribute;

    public function init() {
       
    }
    
    public function validateAttribute($model, $attribute) {
        if (isset($this->userIdAttribute)) {
            $userId = $model->{$this->userIdAttribute};
        }
        
        $bookCopy = BookCopy::findOne($model->{$attribute});

        $message = $this->not ? $this->notMessage : $this->message;

        if (isset($bookCopy)) {
            if (isset($userId)) {
                $valid = (!$this->not && $bookCopy->isAvailableForBorrow($userId)) || ($this->not && !$bookCopy->isAvailableForBorrow($userId));
            } else {
                $valid = (!$this->not && $bookCopy->isAvailableForBorrow) || ($this->not && !$bookCopy->isAvailableForBorrow);
            }

            if (!$valid) {
                $model->addError($attribute, $message);
            }
        } else {
            throw new \Exception('Book copy with ID: '.$model->{$attribute} .' it not exist. ');
        }
    }
}