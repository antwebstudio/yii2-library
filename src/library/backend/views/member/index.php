<?php
use yii\helpers\Html;
use ant\user\models\User;

$this->title = 'Manage Members';
?>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $model,
	'columns' => [
		'id',
		'username',
		'email',
		[
			'label' => 'IC Number',
            'format' => 'html',
			'attribute' => 'identityId',
			'value' => function($model) {
				// IC Number
				$userIc = $model->getIdentityId()->andWhere(['type' => 'ic'])->one();
				$html = isset($userIc) ? $userIc->value : null;
				
				// Full name
				return $html.'<p>('.$model->fullname.')</p>';
			},
		],
        [
            'label' => 'Membership',
            'format' => 'html',
            'value' => function($model) {
				// Membership
                $isMember = $model->isMember;
                $class = $isMember ? 'label-success badge-success' : 'label-warning badge-warning';
                $label = $isMember ? 'Member' : 'Non-member';
                $expireWord = $isMember ? 'Expire' : 'Expired';
                $text = $model->membershipExpireAt ? $expireWord.' at '.$model->membershipExpireAt : '';
                $html = '<span class="label badge '.$class.'">'.$label.'</span><p>'.$text.'</p>';
				
				// Deposit
                $isPaid = \ant\library\models\DepositMoney::checkIsPaid($model->id);
                $class = $isPaid ? 'label-success badge-success' : 'label-warning badge-warning';
                $text = $isPaid ? 'Deposit Paid' : 'Deposit Unpaid';
                return $html.'<span class="label badge '.$class.'">'.$text.'</span>';
            }
        ],
        [
            'label' => 'Role',
			'value' => function($model) {
				$roles = [];
				foreach (\Yii::$app->authManager->getRolesByUser($model->id) as $role) {
					if (!in_array($role->name, ['guest'])) {
						$roles[] = $role->name;
					}
				}
				return implode(', ', $roles);
			},
        ],
        [
			'class' => 'yii\grid\ActionColumn',
            'template' => '{renew} {payDeposit}',
			'buttons' => [
				'renew' => function($url, $model, $key) {

                    return \ant\grid\ActionColumn::dropdown([
                        'label' => 'Renew',
                        //'split' => true,
                        //'tagName' => 'a', // Needed so that href option work
						'url' => ['/member/backend/member/subscription', 'id' => $model->id],
                        'options' => [
                            'class' => 'btn-sm btn btn-default',
                        ],
						'items' => [
							['label' => 'User Invoices', 'url' => ['/payment/backend/invoice/index', 'user' => $model->id], 'linkOptions' => ['data-tester-link' => 'invoice']],
							['label' => 'Edit', 'url' => ['/user/backend/user/update', 'id' => $model->id]],
							['label' => 'Pay Deposit', 'linkOptions' => ['data-method' => 'post', 'data-tester-link' => 'pay-deposit'], 'url' => ['/library/backend/member/pay-deposit', 'id' => $model->id]],
							['label' => 'Return Deposit', 'linkOptions' => ['data-method' => 'post'], 'url' => ['/library/backend/member/return-deposit', 'id' => $model->id]],
							['label' => 'View Subscription', 'url' => ['/subscription/backend/subscription/user', 'user' => $model->id]],
						],
                    ]);
                },
			],
		],
	],
]) ?>