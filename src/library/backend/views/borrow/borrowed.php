<?php
use yii\helpers\Html;
use yii\helpers\Url;
use ant\library\models\BookBorrow;

$this->title = 'Record of borrow';

$tabQueryParamName = 'tab';
$tab = Yii::$app->request->get($tabQueryParamName);
if (isset($user)) {
    $this->title = $this->title.' - '.$user->username;

    $libraryPolicy = Yii::$app->getModule('library')->getPolicy($user);
    $userIc = $user->getIdentityId()->andWhere(['type' => 'ic'])->one();
}

if ($tab == 'expired') {
    $dataProvider->query->notReturned()->expired()->excludeReserved();
} elseif ($tab == 'not-expired') {
    $dataProvider->query->notReturned()->notExpired()->excludeReserved();
} elseif ($tab == 'returned') {
    $dataProvider->query->returned();
} elseif ($tab == 'reserved') {
    $dataProvider->query->reserved();
} else {
	$dataProvider->query->notReturned()->excludeReserved();
}
?>

<?php if (isset($user)): ?>
    <?php $this->beginBlock('header') ?>
	<div class="card card-body mb-3">
		<div>
			<div>Username: <?= $user->username ?></div>
			<div>Email: <?= $user->email ?></div>
			<div>Name: <?= $user->fullName ?></div>
			<div>IC: <?= isset($userIc) ? $userIc->value : '' ?></div>
            <div>Member Type: <?= $libraryPolicy->getMemberTypeName() ?></div>
            <div>Member Status: <span class="badge badge-<?= $user->isMember ? 'success' : 'warning' ?>"><?= $user->membershipStatusText ?></span></div>
            <div>Book Renew Days: <?= $libraryPolicy->getRenewDays() ?></div>
		</div>
	</div>
    <?php $this->endBlock() ?>
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
        [
            'label' => 'Reserved',
            'url' => Url::current([$tabQueryParamName => 'reserved']),
            'active' => $tab == 'reserved' ? true : false,
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
            'template' => '{renew} {cancel-reserve} {borrow}',
            'buttons' => [
                'renew' => function($url, $model, $key) {
                    if (!$model->isReserved) {
                        return Html::a('Renew', ['/library/backend/borrow/renew', 'id' => $model->id], ['data-confirm' => 'Are you sure you want to renew?', 'data-method' => 'post', 'class' => 'btn btn-default'])
                            .'(Renewed: '.$model->renewCount.')';
                    }
                },
                'cancel-reserve' => function($url, $model, $key) {
                    if ($model->isReserved) {
                        return Html::a('Cancel Reserve', ['/library/backend/borrow/cancel-reserve', 'id' => $model->id], ['data-method' => 'post', 'class' => 'btn btn-default']);
                    }
                },
                'borrow' => function($url, $model, $key) {
                    if (!$model->bookCopy->isBorrowed) {
                        return Html::a('Borrow', ['/library/backend/return/returned', 'bookCopy' => $model->bookCopy->id], ['class' => 'btn btn-dark']);
                    }
                }
            ],
        ],
    ],
]) ?>