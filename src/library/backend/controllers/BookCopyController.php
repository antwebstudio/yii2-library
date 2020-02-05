<?php

namespace ant\library\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use ant\helpers\DateTime;
use ant\library\models\Book;
use ant\library\models\BookCopy;
use ant\library\models\BookCopySearch;
use SteelyWing\Chinese\Chinese;
use yii\db\Expression;

class BookCopyController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
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
		$model = new BookCopySearch;
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        /*if (Yii::$app->request->post()) {
            $selected = Yii::$app->request->post();
        }*/

        return $this->render($this->action->id, [
			'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionPrintSticker() {
		return $this->render($this->action->id, [
		]);
	}

    public function actionSticker($status = null, $date = null) {
		$stickerPerPage = 30;
		$pagePerPrint = 10;
		$date = isset($date) && $date != '' ? explode(' - ', $date) : null;
		
		$query = BookCopy::find()->alias('bookCopy')->joinWith(['book' => function($q) { 
				$q->alias('book')->joinWith('categories categories'); 
			}])->orderBy('book.language, categories.id, bookCopy.id'); // order by bookCopy.id to make sure the sequence is always the same so that it wont print duplicated stickers
				
		if (isset($status) && $status) {
			$query->andWhere(['bookCopy.sticker_label_status' => $status]);
		}
		
		if (isset($date)) {
			$startAt = new DateTime($date[0]);
			$endAt = new DateTime($date[1]);
			$endAt->setTimeAsEndOfDay();
			$query->andWhere(['between', 'bookCopy.created_at', $startAt->systemFormat(), $endAt->systemFormat()]);
		}
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			
            'pagination' => [
                'pageSize' => $stickerPerPage * $pagePerPrint,
            ],
        ]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
			'stickerPerPage' => $stickerPerPage,
        ]);
    }

    /**
     * Deletes an existing BookCopy model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	
	public function actionMarkStickerLabelStatus($id, $status) {
		$model = $this->findModel($id);
		$model->sticker_label_status = $status;
		
		if (!$model->save()) throw new \Exception('Failed to update sticker label status. ');
		
		return $this->redirect(['index']);
	}

    /**
     * Finds the BookCopy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BookCopy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BookCopy::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAjaxList($q) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $q = trim($q);

        if ($q == '') return [];
        
        $q = (new Chinese)->to(Chinese::CHS, $q);

        $result = BookCopy::find()
            ->select(['*', 'title as text'])
            ->joinWith('book book')
            ->andWhere(['like', 'book.title', $q])
            ->orderBy(new Expression('book.title = '.\Yii::$app->db->quoteValue($q).' DESC')) // To make sure the exact match will be the first option
            ->asArray()->all();

        return $result;
    }
}