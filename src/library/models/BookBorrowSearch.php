<?php

namespace ant\library\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use ant\library\models\BookBorrow;

/**
 * BookBorrowSearch represents the model behind the search form of `ant\library\models\BookBorrow`.
 */
class BookBorrowSearch extends BookBorrow
{
	public $userIdentityId;
    public $barcode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'book_copy_id', 'user_id', 'borrow_days', 'status', 'returned_by', 'created_by'], 'integer'],
            [['barcode', 'userIdentityId', 'remark', 'returned_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BookBorrow::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		
		if ($this->userIdentityId) {
			$query->joinWith(['user' => function($q) { 
				$q->alias('user');
				$q->joinWith('identityId identityId');
			}]);
			
			$query->andWhere(['identityId.value' => $this->userIdentityId])
				->andWhere(['identityId.type' => 'ic']);
		}

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'book_copy_id' => $this->barcode,
            'user_id' => $this->user_id,
            'borrow_days' => $this->borrow_days,
            'status' => $this->status,
            'returned_at' => $this->returned_at,
            'returned_by' => $this->returned_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
