<?php

namespace ant\library\models\query;

use Yii;

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
    public function olderThan($dayPast = 1){
        return $this->andWhereOlderDaysAgo('created_at', $dayPast);
    }

    public function notReturned() {
        return $this->andWhere(['returned_at' => null, 'returned_by' => null]);
    }

    public function returned() {
        return $this->andWhere(['NOT', ['returned_at' => null, 'returned_by' => null]]);
    }
}