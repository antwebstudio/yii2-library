<?php
namespace ant\library\backend\controllers;

// use ant\library\models\DepositMoney;
// use ant\user\models\User;
use ant\user\models\UserSearch;

class MobileController extends \yii\web\Controller {
    public function behaviors() {
        return [
            [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'pay-deposit' => ['POST'],
                    'return-deposit' => ['POST'],
                ],
            ]
        ];
    }

    public function actionMember() {
        $model = new UserSearch;
        $dataProvider = $model->search(\Yii::$app->request->queryParams);

        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
}