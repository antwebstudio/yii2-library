<?php
use yii\helpers\Html;
use yii\helpers\Url;

$tabQueryParamName = 'tab';
$tab = Yii::$app->request->get($tabQueryParamName);

if ($tab == 'expired') {
    $dataProvider->query->notReturned()->expired();
} elseif ($tab == 'not-expired') {
    $dataProvider->query->notReturned()->notExpired();
} elseif ($tab == 'returned') {
    $dataProvider->query->returned();
} else {
	$dataProvider->query->notReturned();
}
?>

<?php if (isset($user)): ?>
	<?php $userIc = $user->getIdentityId()->andWhere(['type' => 'ic'])->one() ?>
	<div class="well">
		<div>
			<div>Username: <?= $user->username ?></div>
			<div>Email: <?= $user->email ?></div>
			<div>Name: <?= $user->fullName ?></div>
			<div>IC: <?= isset($userIc) ? $userIc->value : '' ?></div>
		</div>
	</div>
<?php endif ?>

<?= \ant\widgets\Tabs::widget([
    'items' => [
        [
            'label' => 'All',
            'url' => Url::current([$tabQueryParamName => null]),
        ],
        [
            'label' => 'Expired',
            'url' => Url::current([$tabQueryParamName => 'expired']),
            'active' => $tab == 'expired' ? true : false,
        ],
        [
            'label' => 'Not Expired',
            'url' => Url::current([$tabQueryParamName => 'not-expired']),
            'active' => $tab == 'not-expired' ? true : false,
        ],
        [
            'label' => 'Returned',
            'url' => Url::current([$tabQueryParamName => 'returned']),
            'active' => $tab == 'returned' ? true : false,
        ],
    ],
]) ?>
<?= \yii\grid\GridView::widget([
    'filterModel' => $model,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'barcode',
            'value' => 'bookCopy.barcode',
            'label' => 'Barcode',
        ],
        [
            'attribute' => 'bookCopy.book.title',
            'label' => '书名 Title',
        ],
		[
            'label' => '借书者 Member',
            'format' => 'html',
			'attribute' => 'userIdentityId',
			'value' => function($model) {
                $userIc = $model->user->getIdentityId()->andWhere(['type' => 'ic'])->one();
                $email = $model->user->email;
                return '<b>IC:</b> '.(isset($userIc) ? $userIc->value : '')
                    .'<br/><b>Email:</b> '.$email;
			},
		],
        [
            'attribute' => 'created_at',
            'label' => '借出时间 Borrowed At',
        ],
        [
            'attribute' => 'expireAt',
            'label' => '截止时间 Expired At',
        ],
        'createdBy.username',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{renew}',
            'buttons' => [
                'renew' => function($url, $model, $key) {
                    return Html::a('Renew', ['/library/backend/borrow/renew', 'id' => $model->id], ['data-method' => 'post', 'class' => 'btn btn-default']);
                }
            ],
        ],
    ],
]) ?>