<?php

namespace ant\library\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use ant\library\models\Book;
use ant\library\models\BookSearch;
use ant\library\models\BookCopy;

class BookController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
		$searchModel = new BookSearch;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate() {
        $model = new Book;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['create']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = Book::findOne($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        
        $model = Book::findOne($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Deleted successfully');
            return $this->redirect(['index']);
        }
    }

}
