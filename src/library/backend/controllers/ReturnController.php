<?php
namespace ant\library\backend\controllers;

use Yii;
use ant\library\models\ReturnBookForm;

class ReturnController extends \yii\web\Controller {
    public function actionIndex() {
        $model = new ReturnBookForm;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->confirm && $model->save()) {
				\Yii::$app->session->setFlash('success', 'Book is successfully returned. ');
                return $this->redirect(['/library/backend/borrow/borrowed', 'user' => $model->user->id]);
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
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
