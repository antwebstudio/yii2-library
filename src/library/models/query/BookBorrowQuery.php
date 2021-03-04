<?php

namespace ant\library\models\query;

use Yii;
use ant\library\models\BookBorrow;

class BookBorrowQuery extends \yii\db\ActiveQuery {
    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\DateTimeAttributeQueryBehavior',
            ],
            [
                'class' => 'ant\behaviors\ExpirableQueryBehavior',
            ]
        ];
    }

    public function forUser($user) {
        $userId = is_object($user) ? $user->id : $user;
        return $this->andWhere(['user_id' => $userId]);
    }

    public function olderThan($dayPast = 1){
        return $this->andWhereOlderDaysAgo('created_at', $dayPast);
    }

    public function notReturned() {
        return $this->andWhere(['returned_at' => null, 'returned_by' => null]);
    }

    public function returned() {
        return $this->andWhere(['NOT', ['returned_at' => null, 'returned_by' => null]]);
    }

    public function excludeReturned() {
        return $this->notReturned();
    }

    public function excludeReserved() {
        return $this->alias('borrow')->andWhere(['NOT', ['borrow.status' => BookBorrow::STATUS_RESERVED]])
        ->andWhere(['NOT', ['borrow.status' => BookBorrow::STATUS_CLAIMED]]);
    }

    public function borrowed() {
        return $this->alias('borrow')->andWhere(['borrow.status' => BookBorrow::STATUS_BORROWED]);
    }

    public function reserved() {
        return $this->alias('borrow')->andWhere(['borrow.status' => BookBorrow::STATUS_RESERVED]);
    }
}