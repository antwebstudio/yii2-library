<?php
namespace ant\library\backend\controllers;

use Yii;
use ant\user\models\User;
use ant\library\models\BookBorrow;
use ant\library\models\BookBorrowSearch;
use ant\library\models\BookCopy;
use ant\library\models\BorrowBookForm;

class BorrowController extends \yii\web\Controller {
    public function behaviors() {
        return [
            [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'renew' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $model = $this->module->getFormModel('borrowBook');
        if (BookCopy::barcodeAttribute() == 'custom_barcode') {
            $model->scenario = $model::SCENARIO_CUSTOM_BARCODE;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->confirm && $model->save()) {
			
            if ($model->reserve) {
                Yii::$app->session->setFlash('library', 'Book copy is successfully reserved. ');
			
            } else {
                Yii::$app->session->setFlash('library', 'Book borrow is successfully recorded. ');
			
			    $model->refresh();
			
                $notification = new \ant\library\notifications\BookBorrowed($model->bookBorrowedRecords, $model->user);
                Yii::$app->notifier->send($model->user, $notification);
            }
			
			return $this->redirect(['/library/backend/borrow/borrowed', 'user' => $model->user->id]);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionBorrowed($user = null) {
        $searchModel = new BookBorrowSearch;
		if (isset($user) && $user) {
			$user = User::findOne($user);
			$searchModel->user_id = $user->id;
		}
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];

        //$dataProvider = $model->search(Yii::$app->request->queryParams);
        
        return $this->render($this->action->id, [
            'model' => $searchModel,
			'user' => isset($user) ? $user : null,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCancelReserve($id) {
        $model = BookBorrow::findOne($id);
        if ($model->isReserved && $model->delete()) {
            Yii::$app->session->setFlash('success', 'Reservation is successfully canceled. ');
            return $this->goBack(Yii::$app->request->referrer);
        }
    }

    public function actionRenew($id = null) {
        $model = BookBorrow::findOne($id);
        $policy = Yii::$app->getModule('library')->getPolicy($model->user);

        if ($policy->getRenewDays() <= 0) {
            Yii::$app->session->setFlash('error', 'This member is not allowed to renew book.');
            return $this->goBack(Yii::$app->request->referrer);
        }

        if ($model->renewCount < $policy->getMaxRenew()) {
            $model->renew($policy->getRenewDays())->save();
            $model->refresh();
			
			$notification = new \ant\library\notifications\BookBorrowed($model, $model->user);
			Yii::$app->notifier->send($model->user, $notification);

            Yii::$app->session->setFlash('success', 'Successfully renewed, new expiry date: '.$model->expireAt);

            return $this->goBack(Yii::$app->request->referrer);
        } else {
            Yii::$app->session->setFlash('error', 'Exceed limit of renew.');
            return $this->goBack(Yii::$app->request->referrer);
        }
    }

    public function actionAjaxUsers($q) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $searchModel = new \ant\user\models\UserSearch;
        $dataProvider = $searchModel->searchByQuery($q);
        $dataProvider->query->asArray()->select(['*', 'user.*', 'username as text']);

        return $dataProvider->models;
    }
}
