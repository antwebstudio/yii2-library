<?php

namespace ant\library\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use ant\library\models\BookPublisher;
use SteelyWing\Chinese\Chinese;

class PublisherController extends \yii\web\Controller
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
        $dataProvider = new ActiveDataProvider([
            'query' => BookPublisher::find(),
        ]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate() {
        $model = new BookPublisher;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = BookPublisher::findOne($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        
        $model = BookPublisher::findOne($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Deleted successfully');
            return $this->redirect(['index']);
        }
    }

    public function actionAjaxPublishers($q) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $q = trim($q);

        if ($q == '') return [];
        
        $q = (new Chinese)->to(Chinese::CHS, $q);

        $result = BookPublisher::find()->select(['*', 'name as text'])->andWhere(['like', 'name', $q])
           ->orderBy(new Expression('name = '.\Yii::$app->db->quoteValue($q).' DESC')) // To make sure the exact match will be the first option
            ->asArray()->all();

        if (count($result) == 0 || $result[0]['name'] != $q) {
            // Add the option to create the author if it is not exist.
            $result[] = ['id' => 'new:'.$q, 'text' => 'New Publisher: '.$q];
        }

        return $result;
    }

}
