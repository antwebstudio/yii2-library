<?php

namespace ant\library\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use ant\library\models\BookCopy;
use SteelyWing\Chinese\Chinese;

/**
 * BookCopySearch represents the model behind the search form of `ant\library\models\BookCopy`.
 */
class BookCopySearch extends BookCopy
{
	public $barcodeId;
	public $title;
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'book_id', 'status', 'created_by', 'barcodeId'], 'integer'],
            [['title', 'created_at'], 'safe'],
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
		//throw new \Exception($this->barcodeId);
        $query = BookCopy::find()->alias('bookCopy')->joinWith('book book');

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

        // grid filtering conditions
        $query->andFilterWhere([
            'bookCopy.id' => $this->barcodeId,
            'book_id' => $this->book_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);
		
		$query->andFilterWhere(['like', 'book.title', (new Chinese)->to(Chinese::CHS, $this->title)]);

        return $dataProvider;
    }
}
