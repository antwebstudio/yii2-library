<?php
namespace ant\library\backend\controllers;

use ant\library\models\DepositMoney;
use ant\user\models\User;
use ant\user\models\UserSearch;

class MemberController extends \yii\web\Controller {
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

    public function actionIndex() {
        $model = new UserSearch;
        $dataProvider = $model->search(\Yii::$app->request->queryParams);

        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReturnDeposit($id) {
        $deposit = DepositMoney::findOne(['user_id' => $id, 'returned_by' => null, 'returned_at' => null]);
        if (isset($deposit) && $deposit->isPaid) {
            if (!$deposit->returnBy(\Yii::$app->user->id)->save()) throw new \Exception(print_r($deposit->errors, 1));

            \Yii::$app->session->setFlash('success', 'Deposit is returned. ');
        } else {
            \Yii::$app->session->setFlash('error', 'Deposit is not yet paid. ');
        }
        return $this->goBack(\Yii::$app->request->referrer);
    }

    public function actionPayDeposit($id) {
        $deposit = DepositMoney::findOne(['user_id' => $id, 'returned_by' => null, 'returned_at' => null]);
        if (isset($deposit)) {
            if ($deposit->isPaid) {
                \Yii::$app->session->setFlash('success', 'Deposit is paid. ');
                return $this->goBack(\Yii::$app->request->referrer);
            } else{
                return $this->redirect($deposit->invoice->adminPanelRoute);
            }
        } else {
            $user = User::findOne($id);

            if (isset($user)) {
                $deposit = new DepositMoney;
                $deposit->user_id = $id;
                $invoice = $deposit->createInvoice($user, $user->getMembershipDepositAmount());

                if (!$deposit->save()) throw new \Exception(print_r($deposit->errors, 1));

                return $this->redirect($invoice->adminPanelRoute);
            }
        }
        return $this->render($this->action->id, [

        ]);
    }
}