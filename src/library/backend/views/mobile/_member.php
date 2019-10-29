<?php 
use yii\helpers\Url;

$userIc = $model->getIdentityId()->andWhere(['type' => 'ic'])->one();
?>
<div class="well">
	<div>
		<div>Username: <?= $model->username ?></div>
		<div>Email: <?= $model->email ?></div>
		<div>Name: <?= $model->fullName ?></div>
		<div>IC: <?= isset($userIc) ? $userIc->value : '' ?></div>
	</div>
	<a class="btn btn-primary" href="<?= Url::to(['/subscription/backend/subscription/user', 'user' => $model->id]) ?>">Membership</a>
	
	<a class="btn btn-primary" href="<?= Url::to(['/library/borrow/borrowed', 'user' => $model->id]) ?>">Borrowed Book</a>
</div>