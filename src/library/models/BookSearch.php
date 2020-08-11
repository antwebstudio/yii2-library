<?php

namespace ant\library\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ant\library\models\Book;
use SteelyWing\Chinese\Chinese;

/**
 * This is the model class for table "ks_library_book".
 *
 * @property int $id
 * @property string $isbn
 * @property string $title
 * @property string $small_title
 * @property int $publisher_id
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property BookPublisher $publisher
 * @property BookAuthorMap[] $libraryBookAuthorMaps
 */
class BookSearch extends Book
{
	
	public $category_ids;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'/*, 'status', 'is_approved'*/], 'integer'],
            [['category_ids', 'categories', 'title', 'isbn', 'publisher_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
	
	public function getDataProviderForExport($params = []) {
        $query = Book::find();
        
        // add conditions that should always apply here
        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		return $dataProvider;
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
        $query = Book::find()->alias('book');
		$query->joinWith('categories categories');
        
        // add conditions that should always apply here
        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
			'book.id' => $this->id,
            'categories.id' => $this->category_ids,
			'publisher_id' => $this->publisher_id,
        ]);

        $query->andFilterWhere(['like', 'book.title', (new Chinese)->to(Chinese::CHS, $this->title)])
            //->andFilterWhere(['like', 'auth_key', $this->auth_key])
            //->andFilterWhere(['like', 'password_hash', $this->password_hash])
            //->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'book.isbn', $this->isbn]);

        return $dataProvider;
    }
}
