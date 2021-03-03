<?php
namespace ant\library\backend\controllers;

use Yii;
use ant\library\models\ReturnBookForm;
use ant\library\models\BookCopy;

class ReturnController extends \yii\web\Controller {
    public function actionIndex() {
        $model = new ReturnBookForm;
        if (BookCopy::barcodeAttribute() == 'custom_barcode') {
            $model->scenario = $model::SCENARIO_CUSTOM_BARCODE;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->confirm && $model->save()) {
				\Yii::$app->session->setFlash('success', 'Book is successfully returned. ');

                // Next step after the book is returned
                return $this->redirect(['/library/backend/return/returned', 'bookCopy' => $model->bookCopy->id]);

                // Redirect to list of borrowed book page
                return $this->redirect(['/library/backend/borrow/borrowed', 'user' => $model->user->id]);
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionReturned($bookCopy) {
        $bookCopy = BookCopy::findOrFail($bookCopy);
        return $this->render($this->action->id, [
            'bookCopy' => $bookCopy,
            'skipUrl' => ['/library/backend/borrow/borrowed'],
        ]);
    }

    public function actionAjaxUsers($q) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $searchModel = new \ant\user\models\UserSearch;
        $dataProvider = $searchModel->searchByQuery($q);
        $dataProvider->query->asArray()->select(['*', 'username as text']);

        return $dataProvider->models;
    }
}
