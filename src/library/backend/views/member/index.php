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
			'attribute' => 'identityId',
			'value' => function($model) {
				$userIc = $model->getIdentityId()->andWhere(['type' => 'ic'])->one();
				return isset($userIc) ? $userIc->value : null;
			},
		],
		[
			'attribute' => 'fullname',
		],
        [
            'label' => 'Membership',
            'format' => 'html',
            'value' => function($model) {
                $isMember = $model->isMember;
                $class = $isMember ? 'label-success' : 'label-warning';
                $label = $isMember ? 'Member' : 'Non-member';
                $expireWord = $isMember ? 'Expire' : 'Expired';
                $text = $model->membershipExpireAt ? $expireWord.' at '.$model->membershipExpireAt : '';
                return '<span class="label '.$class.'">'.$label.'</span><p>'.$text.'</p>';
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
            'label' => 'Deposit',
            'format' => 'html',
            'value' => function($model) {
                $isPaid = \ant\library\models\DepositMoney::checkIsPaid($model->id);
                $class = $isPaid ? 'label-success' : 'label-warning';
                $text = $isPaid ? 'Paid' : 'Unpaid';
                return '<span class="label '.$class.'">'.$text.'</span>';
            }
        ],
        [
			'class' => 'yii\grid\ActionColumn',
            'template' => '{renew} {payDeposit}',
			'buttons' => [
				'renew' => function($url, $model, $key) {

                    return \yii\bootstrap\ButtonDropdown::widget([
                        'label' => 'Renew',
                        'split' => true,
                        'tagName' => 'a', // Needed so that href option work
                        'options' => [
                            'href' => ['/member/backend/member/subscription', 'id' => $model->id],
                            'class' => 'btn-sm btn btn-default',
                        ],
                        'dropdown' => [
                            'items' => [
                                ['label' => 'User Invoices', 'url' => ['/payment/invoice/index', 'user' => $model->id]],
                                ['label' => 'Edit', 'url' => ['/user/user/update', 'id' => $model->id]],
                                ['label' => 'Pay Deposit', 'linkOptions' => ['data-method' => 'post'], 'url' => ['/library/member/pay-deposit', 'id' => $model->id]],
                                ['label' => 'Return Deposit', 'linkOptions' => ['data-method' => 'post'], 'url' => ['/library/member/return-deposit', 'id' => $model->id]],
								['label' => 'View Subscription', 'url' => ['/subscription/backend/subscription/user', 'user' => $model->id]],
                            ],
                        ],
                    ]);
                },
			],
		],
	],
]) ?>